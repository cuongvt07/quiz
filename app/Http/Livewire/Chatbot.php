<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Chatbot extends Component
{
    public $isOpen = false;
    public $isMinimized = false;
    public $message = '';
    public $messages = [];
    public $isTyping = false;

    public function mount()
    {
        // Load chat history from session if exists
        $this->messages = session('chatbot_messages', []);
    }

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen) {
            $this->isMinimized = false;
        }
    }

    public function minimizeChat()
    {
        $this->isMinimized = !$this->isMinimized;
    }

    public function closeChat()
    {
        $this->isOpen = false;
        $this->isMinimized = false;
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

        // Clear input
        $userInput = $this->message;
        $this->message = '';

        // Show typing indicator
        $this->isTyping = true;

        // PLACEHOLDER: Call AI Agent API here
        // This is where you'll integrate your AI agent
        $this->callAIAgent($userInput);
    }

    public function callAIAgent($userInput)
    {
        try {
            // Build conversation history for OpenAI
            $conversationHistory = collect($this->messages)->map(function($msg) {
                return [
                    'role' => $msg['type'] === 'user' ? 'user' : 'assistant',
                    'content' => $msg['content']
                ];
            })->toArray();

            // Add system message for context
            array_unshift($conversationHistory, [
                'role' => 'system',
                'content' => 'You are a helpful AI assistant for a quiz/exam platform. Help users with questions about exams, studying, scores, and the platform. Be friendly, concise, and helpful.'
            ]);

            // Add the current user message
            $conversationHistory[] = [
                'role' => 'user',
                'content' => $userInput
            ];

            // Call OpenAI API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ])
            ->timeout(30)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => $conversationHistory,
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

            if ($response->successful()) {
                $aiContent = $response->json('choices.0.message.content');
                
                $aiResponse = [
                    'type' => 'ai',
                    'content' => $aiContent ?? 'Sorry, I couldn\'t generate a response.',
                    'timestamp' => now()->format('H:i')
                ];
            } else {
                throw new \Exception('OpenAI API Error: ' . $response->status());
            }

            $this->messages[] = $aiResponse;
            session(['chatbot_messages' => $this->messages]);

        } catch (\Exception $e) {
            Log::error('Chatbot OpenAI Error: ' . $e->getMessage());
            
            $errorResponse = [
                'type' => 'ai',
                'content' => 'Sorry, I encountered an error connecting to the AI service. Please try again later.',
                'timestamp' => now()->format('H:i')
            ];
            
            $this->messages[] = $errorResponse;
        } finally {
            $this->isTyping = false;
        }

        /* 
         * TODO: Replace above code with actual AI API call
         * Example:
         * 
         * try {
         *     $response = Http::post('YOUR_AI_API_ENDPOINT', [
         *         'message' => $userInput,
         *         'user_id' => Auth::id(),
         *         'conversation_history' => $this->messages
         *     ]);
         *     
         *     $aiResponse = [
         *         'type' => 'ai',
         *         'content' => $response->json('message'),
         *         'timestamp' => now()->format('H:i')
         *     ];
         *     
         *     $this->messages[] = $aiResponse;
         * } catch (\Exception $e) {
         *     // Handle error
         * } finally {
         *     $this->isTyping = false;
         * }
         */
    }

    public function clearChat()
    {
        $this->messages = [];
        session()->forget('chatbot_messages');
    }

    public function render()
    {
        return view('livewire.chatbot');
    }
}
