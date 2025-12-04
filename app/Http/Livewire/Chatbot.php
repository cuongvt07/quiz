<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\ExamAttemptAnswer;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\QuestionChoice;
use App\Models\ChatbotConversation;
use Illuminate\Support\Str;

class Chatbot extends Component
{
    public $isOpen = false;
    public $message = '';
    public $messages = [];
    public $isTyping = false;
    public $sessionId;
    public $showHistory = false;

    public function mount()
    {
        // Generate or retrieve session ID
        $this->sessionId = session('chatbot_session_id', Str::uuid()->toString());
        session(['chatbot_session_id' => $this->sessionId]);
        
        // Load chat history from database if user is authenticated
        if (Auth::check()) {
            $this->loadChatHistory();
        } else {
            // Load chat history from session if exists (for guests)
            $this->messages = session('chatbot_messages', []);
        }
    }

    /**
     * Load chat history from database for the current session
     */
    private function loadChatHistory()
    {
        $history = ChatbotConversation::where('user_id', Auth::id())
            ->where('session_id', $this->sessionId)
            ->orderBy('created_at', 'asc')
            ->get();

        $this->messages = [];
        foreach ($history as $record) {
            $this->messages[] = [
                'type' => $record->message_type,
                'content' => $record->message_content,
                'timestamp' => $record->created_at->format('H:i')
            ];
        }
        
        // Also store in session for compatibility
        session(['chatbot_messages' => $this->messages]);
    }

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function closeChat()
    {
        $this->isOpen = false;
        $this->showHistory = false;
    }
    
    public function toggleHistory()
    {
        $this->showHistory = !$this->showHistory;
    }
    
    public function startNewChat()
    {
        $this->showHistory = false;
        
        // Clear messages from view
        $this->messages = [];
        session()->forget('chatbot_messages');
        
        // Generate new session ID (keep old session in database)
        $this->sessionId = Str::uuid()->toString();
        session(['chatbot_session_id' => $this->sessionId]);
    }

    public function sendMessage()
    {
        if (trim($this->message) === '') {
            return;
        }

        // Add user message
        $userMessage = [
            'type' => 'user',
            'content' => $this->message,
            'timestamp' => now()->format('H:i')
        ];

        $this->messages[] = $userMessage;

        // Store user message in database
        if (Auth::check()) {
            ChatbotConversation::create([
                'user_id' => Auth::id(),
                'session_id' => $this->sessionId,
                'message_type' => 'user',
                'message_content' => $this->message
            ]);
        }

        // Clear input
        $userInput = $this->message;
        $this->message = '';

        // Show typing indicator - dispatch event for immediate UI update
        $this->isTyping = true;
        $this->dispatchBrowserEvent('chatbot-thinking');

        // Call AI Agent
        $this->callAIAgent($userInput);
    }

    public function callAIAgent($userInput)
    {
        try {
            // Step 1: Detect query type using AI
            $queryType = $this->detectQueryTypeWithAI($userInput);
            
            // Step 2: Handle based on query type
            if ($queryType === 'review') {
                $this->handleReviewRequest($userInput);
            } else {
                $this->handleNormalQuery($userInput);
            }

        } catch (\Exception $e) {
            Log::error('Chatbot AI Error: ' . $e->getMessage());

            $errorResponse = [
                'type' => 'ai',
                'content' => 'Xin lỗi, tôi gặp lỗi khi kết nối với dịch vụ AI. Vui lòng thử lại sau.',
                'timestamp' => now()->format('H:i')
            ];

            $this->messages[] = $errorResponse;
        } finally {
            $this->isTyping = false;
            $this->dispatchBrowserEvent('chatbot-finished');
        }
    }

    /**
     * Use AI to detect if user wants to review mistakes
     */
    private function detectQueryTypeWithAI($userInput)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ])
            ->timeout(10)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a query classifier for an educational quiz platform. 
                        Classify the user query into one of two types:
                        - "review": User wants to review their mistakes, see wrong answers, get improvement suggestions, analyze their performance, or learn from errors
                        - "normal": Any other general question
                        
                        Respond with ONLY the word "review" or "normal", nothing else.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $userInput
                    ]
                ],
                'temperature' => 0.3,
                'max_tokens' => 10,
            ]);

            if ($response->successful()) {
                $classification = trim(strtolower($response->json('choices.0.message.content')));
                return $classification === 'review' ? 'review' : 'normal';
            }
            
            return 'normal';
            
        } catch (\Exception $e) {
            Log::error('Query classification error: ' . $e->getMessage());
            return 'normal';
        }
    }

    /**
     * Handle review/mistake analysis request
     */
    private function handleReviewRequest($userInput)
    {
        // Get user's mistakes from database
        $mistakes = $this->getUserMistakes();
        
        // If no mistakes, return constant congratulation message
        if (empty($mistakes)) {
            $congratsMessage = '🎉 Chúc mừng bạn! Bạn chưa có câu trả lời sai nào. Bạn đang làm rất tốt! Hãy tiếp tục duy trì phong độ này và không ngừng học hỏi nhé! 💪📚';
            
            $aiResponse = [
                'type' => 'ai',
                'content' => $congratsMessage,
                'timestamp' => now()->format('H:i')
            ];
            
            $this->messages[] = $aiResponse;
            session(['chatbot_messages' => $this->messages]);
            
            // Store AI response in database
            if (Auth::check()) {
                ChatbotConversation::create([
                    'user_id' => Auth::id(),
                    'session_id' => $this->sessionId,
                    'message_type' => 'ai',
                    'message_content' => $congratsMessage
                ]);
            }
            
            return;
        }

        // Format mistakes for AI context
        $mistakesContext = $this->formatMistakesForAI($mistakes);
        
        // Build conversation history with mistake context and specific instructions
        $conversationHistory = [
            [
                'role' => 'system',
                'content' => 'Bạn là một trợ lý giáo dục thông minh và thân thiện. Nhiệm vụ của bạn là giúp học sinh học từ những sai lầm của họ.

CỰC KỲ QUAN TRỌNG: Bạn PHẢI tuân thủ cấu trúc sau:

**PHẦN 1: HIỂN THỊ TẤT CẢ CÁC CÂU HỎI SAI**
- PHẢI liệt kê TỪNG CÂU HỎI một cách đầy đủ, KHÔNG ĐƯỢC bỏ qua bất kỳ câu nào
- KHÔNG ĐƯỢC viết "... (tiếp tục tương tự cho các câu khác)" hay bất kỳ câu rút gọn nào
- KHÔNG ĐƯỢC tóm tắt hay bỏ qua câu hỏi
- Mỗi câu bao gồm: 
  * Số thứ tự câu
  * Câu hỏi đầy đủ
  * ❌ Câu trả lời sai của học sinh
  * ✅ Đáp án đúng
  * 💡 Giải thích (nếu có)

**PHẦN 2: PHÂN TÍCH VÀ GỢI Ý CẢI THIỆN**
- Chỉ sau khi đã liệt kê HẾT TẤT CẢ các câu sai, mới bắt đầu phần này
- Phân tích những điểm yếu chung
- Đưa ra lời khuyên cụ thể để cải thiện
- Khuyến khích và động viên học sinh

Hãy trả lời bằng tiếng Việt một cách thân thiện, có cấu trúc rõ ràng và dễ hiểu.
Bạn có đủ dung lượng để hiển thị tất cả các câu, hãy làm đầy đủ.

DỮ LIỆU CÁC CÂU SAI:
' . $mistakesContext
            ],
            [
                'role' => 'user',
                'content' => $userInput
            ]
        ];

        // Call OpenAI with enriched context
        $this->callOpenAIWithHistory($conversationHistory);
    }

    /**
     * Handle normal query without mistake context
     */
    private function handleNormalQuery($userInput)
    {
        // Build conversation history from current messages
        $conversationHistory = [
            [
                'role' => 'system',
                'content' => 'Bạn là một trợ lý ảo thông minh và thân thiện. Nhiệm vụ của bạn là trả lời các câu hỏi của người dùng một cách chính xác, hữu ích. Hãy trả lời bằng tiếng Việt.'
            ]
        ];

        // Add previous messages for context (limit to last 10 messages)
        $recentMessages = array_slice($this->messages, -10);
        foreach ($recentMessages as $msg) {
            $conversationHistory[] = [
                'role' => $msg['type'] === 'user' ? 'user' : 'assistant',
                'content' => $msg['content']
            ];
        }

        // Add current user input
        $conversationHistory[] = [
            'role' => 'user',
            'content' => $userInput
        ];

        // Call OpenAI
        $this->callOpenAIWithHistory($conversationHistory);
    }

    /**
     * Call OpenAI API with conversation history
     */
    private function callOpenAIWithHistory($conversationHistory)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ])
        ->timeout(90)
        ->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => $conversationHistory,
            'temperature' => 0.7,
            'max_tokens' => 16000,
        ]);

        if ($response->successful()) {
            $aiContent = $response->json('choices.0.message.content');
            
            $aiResponse = [
                'type' => 'ai',
                'content' => $aiContent ?? 'Xin lỗi, tôi không thể tạo phản hồi.',
                'timestamp' => now()->format('H:i')
            ];
        } else {
            throw new \Exception('OpenAI API Error: ' . $response->status());
        }

        $this->messages[] = $aiResponse;
        session(['chatbot_messages' => $this->messages]);
        
        // Store AI response in database
        if (Auth::check()) {
            ChatbotConversation::create([
                'user_id' => Auth::id(),
                'session_id' => $this->sessionId,
                'message_type' => 'ai',
                'message_content' => $aiContent ?? 'Xin lỗi, tôi không thể tạo phản hồi.'
            ]);
        }
    }

    public function clearChat()
    {
        $this->messages = [];
        session()->forget('chatbot_messages');
        
        // Clear current session from database
        if (Auth::check()) {
            ChatbotConversation::where('user_id', Auth::id())
                ->where('session_id', $this->sessionId)
                ->delete();
        }
        
        // Generate new session ID
        $this->sessionId = Str::uuid()->toString();
        session(['chatbot_session_id' => $this->sessionId]);
    }
    
    /**
     * Load previous chat sessions for history view
     */
    public function getChatSessions()
    {
        if (!Auth::check()) {
            return collect([]);
        }
        
        $sessions = ChatbotConversation::where('user_id', Auth::id())
            ->select('session_id', DB::raw('MIN(created_at) as started_at'), DB::raw('MAX(created_at) as last_message_at'))
            ->groupBy('session_id')
            ->orderBy('last_message_at', 'desc')
            ->limit(10)
            ->get();
        
        // Convert date strings to Carbon instances
        return $sessions->map(function($session) {
            $session->started_at = \Carbon\Carbon::parse($session->started_at);
            $session->last_message_at = \Carbon\Carbon::parse($session->last_message_at);
            return $session;
        });
    }
    
    /**
     * Load a specific chat session
     */
    public function loadSession($sessionId)
    {
        if (!Auth::check()) {
            return;
        }
        
        $this->sessionId = $sessionId;
        session(['chatbot_session_id' => $sessionId]);
        
        $this->loadChatHistory();
        $this->showHistory = false; // Hide history view after loading
    }

    /**
     * Detect if user is asking for mistake review
     */
    private function isReviewRequest($message)
    {
        $keywords = [
            'xem lại', 'xem lai', 'review', 'sai', 'lỗi', 'loi', 'nhầm', 'nham',
            'cải thiện', 'cai thien', 'improve', 'học tốt hơn', 'hoc tot hon',
            'yếu', 'yeu', 'weak', 'mistake', 'error', 'wrong', 'incorrect',
            'câu sai', 'cau sai', 'trả lời sai', 'tra loi sai',
            'phân tích', 'phan tich', 'analyze', 'đánh giá', 'danh gia'
        ];

        $messageLower = mb_strtolower($message);
        
        foreach ($keywords as $keyword) {
            if (str_contains($messageLower, $keyword)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get user's wrong answers from database (limit to 30 most recent)
     */
    private function getUserMistakes()
    {
        if (!Auth::check()) {
            return [];
        }

        return DB::table('exam_attempt_answers')
            ->join('exam_attempts', 'exam_attempt_answers.attempt_id', '=', 'exam_attempts.id')
            ->join('questions', 'exam_attempt_answers.question_id', '=', 'questions.id')
            // Join to get the user's selected answer (wrong answer)
            ->leftJoin('question_choices as user_choice', 'exam_attempt_answers.choice_id', '=', 'user_choice.id')
            // Join to get the correct answer
            ->leftJoin('question_choices as correct_choice', function($join) {
                $join->on('questions.id', '=', 'correct_choice.question_id')
                     ->where('correct_choice.is_correct', '=', 1);
            })
            ->where('exam_attempts.user_id', Auth::id())
            ->where('exam_attempt_answers.is_correct', 0)
            ->select(
                'questions.question',
                'questions.loai as type',
                'user_choice.name as user_answer',
                'exam_attempt_answers.text_answer as user_text_answer',
                'correct_choice.name as correct_answer',
                'correct_choice.explanation',
                'exam_attempt_answers.created_at'
            )
            ->orderBy('exam_attempt_answers.created_at', 'desc')
            ->limit(30)
            ->get()
            ->toArray();
    }

    /**
     * Format mistakes for AI context
     */
    private function formatMistakesForAI($mistakes)
    {
        if (empty($mistakes)) {
            return "Người dùng chưa có câu trả lời sai nào. Họ đang làm rất tốt!";
        }

        $formatted = "DANH SÁCH 30 CÂU TRẢ LỜI SAI GẦN ĐÂY NHẤT:\n\n";
        
        foreach ($mistakes as $index => $mistake) {
            $formatted .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            $formatted .= "CÂU " . ($index + 1) . ":\n";
            $formatted .= "Câu hỏi: " . $mistake->question . "\n\n";
            
            // User's wrong answer (could be from choice_id or text_answer)
            $userAnswer = $mistake->user_answer ?? $mistake->user_text_answer ?? 'Không trả lời';
            $formatted .= "❌ Câu trả lời của user: " . $userAnswer . "\n";
            
            // Correct answer
            $formatted .= "✅ Đáp án đúng: " . ($mistake->correct_answer ?? 'N/A') . "\n";
            
            // Explanation if available
            if ($mistake->explanation) {
                $formatted .= "💡 Giải thích: " . $mistake->explanation . "\n";
            }
            
            $formatted .= "\n";
        }

        return $formatted;
    }

    public function render()
    {
        return view('livewire.chatbot');
    }
}
