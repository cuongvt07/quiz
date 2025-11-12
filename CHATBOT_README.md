# AI Chatbot Component

## Overview
A fully functional chatbot interface for the Laravel Quiz System with a clean, modern UI similar to Claude. The chatbot appears as a floating button in the bottom-right corner and opens as a modal popup when clicked.

## Features

✅ **Floating Chat Button** - Bottom-right corner, visible on all pages when logged in
✅ **Modal/Popup Interface** - Clean, centered chat window
✅ **Minimize/Maximize** - Collapse chat window to save space
✅ **Close Button** - Dismiss the chat completely
✅ **Typing Indicator** - Shows when AI is "thinking"
✅ **Message History** - Persistent chat history stored in session
✅ **Clear Chat** - Button to reset conversation
✅ **Responsive Design** - Works on all screen sizes
✅ **Livewire Component** - Matches existing tech stack
✅ **Auto-scroll** - Messages automatically scroll to bottom

## Installation

The chatbot has been automatically added to all layouts:
- ✅ `resources/views/layouts/app.blade.php`
- ✅ `resources/views/layouts/frontend.blade.php`
- ✅ `resources/views/layouts/admin.blade.php`

## How to Use

### For Users
1. Log in to your account
2. Look for the blue chat icon in the bottom-right corner
3. Click it to open the chatbot
4. Type your message and press Enter (Shift+Enter for new line)
5. Minimize, clear, or close as needed

### For Developers - Integrating AI API

The chatbot currently uses a **placeholder AI response**. To integrate your actual AI agent:

#### 1. Update the `callAIAgent` method in `app/Http/Livewire/Chatbot.php`

Replace the placeholder code (lines 59-78) with your actual API call:

```php
public function callAIAgent($userInput)
{
    try {
        // Call your AI API endpoint
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.ai.api_key'),
            'Content-Type' => 'application/json'
        ])->post(config('services.ai.endpoint'), [
            'message' => $userInput,
            'user_id' => Auth::id(),
            'conversation_history' => $this->messages,
            'context' => [
                'exam_history' => $this->getUserExamContext(),
                'subscription' => $this->getUserSubscription()
            ]
        ]);

        if ($response->successful()) {
            $aiResponse = [
                'type' => 'ai',
                'content' => $response->json('response') ?? $response->json('message'),
                'timestamp' => now()->format('H:i')
            ];
            
            $this->messages[] = $aiResponse;
            session(['chatbot_messages' => $this->messages]);
        } else {
            throw new \Exception('AI API Error: ' . $response->status());
        }
    } catch (\Exception $e) {
        \Log::error('Chatbot AI Error: ' . $e->getMessage());
        
        $errorResponse = [
            'type' => 'ai',
            'content' => 'Sorry, I encountered an error. Please try again later.',
            'timestamp' => now()->format('H:i')
        ];
        
        $this->messages[] = $errorResponse;
    } finally {
        $this->isTyping = false;
    }
}
```

#### 2. Add AI API Configuration

Add to `config/services.php`:

```php
'ai' => [
    'endpoint' => env('AI_API_ENDPOINT', 'https://api.your-ai-service.com/chat'),
    'api_key' => env('AI_API_KEY'),
],
```

Add to `.env`:

```env
AI_API_ENDPOINT=https://api.your-ai-service.com/chat
AI_API_KEY=your_api_key_here
```

#### 3. Add Helper Methods (Optional)

Add these methods to the Chatbot component for better context:

```php
private function getUserExamContext()
{
    return Auth::user()->examAttempts()
        ->with('exam')
        ->latest()
        ->limit(5)
        ->get()
        ->map(fn($attempt) => [
            'exam' => $attempt->exam->title,
            'score' => $attempt->score,
            'date' => $attempt->created_at->format('Y-m-d')
        ]);
}

private function getUserSubscription()
{
    $subscription = Auth::user()->activeSubscription;
    return $subscription ? [
        'plan' => $subscription->plan->name,
        'status' => $subscription->status,
        'expires' => $subscription->end_date->format('Y-m-d')
    ] : null;
}
```

## API Integration Examples

### Example 1: OpenAI GPT API

```php
use OpenAI;

public function callAIAgent($userInput)
{
    $this->isTyping = true;
    
    try {
        $client = OpenAI::client(config('services.openai.key'));
        
        $messages = collect($this->messages)->map(fn($msg) => [
            'role' => $msg['type'] === 'user' ? 'user' : 'assistant',
            'content' => $msg['content']
        ])->toArray();
        
        $messages[] = ['role' => 'user', 'content' => $userInput];
        
        $response = $client->chat()->create([
            'model' => 'gpt-4',
            'messages' => $messages,
        ]);
        
        $aiResponse = [
            'type' => 'ai',
            'content' => $response->choices[0]->message->content,
            'timestamp' => now()->format('H:i')
        ];
        
        $this->messages[] = $aiResponse;
        session(['chatbot_messages' => $this->messages]);
    } catch (\Exception $e) {
        \Log::error('OpenAI Error: ' . $e->getMessage());
    } finally {
        $this->isTyping = false;
    }
}
```

### Example 2: Custom AI Endpoint

```php
use Illuminate\Support\Facades\Http;

public function callAIAgent($userInput)
{
    $this->isTyping = true;
    
    try {
        $response = Http::timeout(30)->post('https://your-ai-api.com/chat', [
            'prompt' => $userInput,
            'history' => $this->messages,
            'user_context' => [
                'user_id' => Auth::id(),
                'recent_exams' => $this->getUserExamContext()
            ]
        ]);
        
        $aiResponse = [
            'type' => 'ai',
            'content' => $response->json('answer'),
            'timestamp' => now()->format('H:i')
        ];
        
        $this->messages[] = $aiResponse;
        session(['chatbot_messages' => $this->messages]);
    } catch (\Exception $e) {
        \Log::error('AI API Error: ' . $e->getMessage());
    } finally {
        $this->isTyping = false;
    }
}
```

## Customization

### Change Colors

Edit `resources/views/livewire/chatbot.blade.php`:

```html
<!-- Change gradient colors -->
<div class="bg-gradient-to-r from-blue-600 to-purple-600">
<!-- To -->
<div class="bg-gradient-to-r from-green-600 to-teal-600">

<!-- Change button color -->
<button class="bg-blue-600 hover:bg-blue-700">
<!-- To -->
<button class="bg-green-600 hover:bg-green-700">
```

### Change Position

Edit the floating button position:

```html
<!-- Bottom Right (default) -->
<div class="fixed bottom-6 right-6 z-50">

<!-- Bottom Left -->
<div class="fixed bottom-6 left-6 z-50">

<!-- Top Right -->
<div class="fixed top-6 right-6 z-50">
```

### Add Persistent Storage

To save chat history to database instead of session:

1. Create a migration:
```bash
php artisan make:migration create_chat_messages_table
```

2. Update the migration:
```php
Schema::create('chat_messages', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->enum('type', ['user', 'ai']);
    $table->text('content');
    $table->timestamp('created_at');
});
```

3. Update `Chatbot.php`:
```php
public function mount()
{
    $this->messages = Auth::user()
        ->chatMessages()
        ->latest()
        ->limit(50)
        ->get()
        ->map(fn($msg) => [
            'type' => $msg->type,
            'content' => $msg->content,
            'timestamp' => $msg->created_at->format('H:i')
        ])
        ->toArray();
}
```

## Troubleshooting

### Chatbot not appearing
1. Make sure you're logged in
2. Check browser console for JavaScript errors
3. Verify Livewire scripts are loaded

### Styles not working
1. Ensure Tailwind CSS is loaded
2. Clear browser cache
3. Run `npm run dev` to rebuild assets

### API calls failing
1. Check your `.env` configuration
2. Verify API credentials
3. Check Laravel logs: `tail -f storage/logs/laravel.log`

## Files Modified/Created

- ✅ `app/Http/Livewire/Chatbot.php` - Livewire component logic
- ✅ `resources/views/livewire/chatbot.blade.php` - Chatbot UI template
- ✅ `resources/views/layouts/app.blade.php` - Added chatbot to app layout
- ✅ `resources/views/layouts/frontend.blade.php` - Added chatbot to frontend layout
- ✅ `resources/views/layouts/admin.blade.php` - Added chatbot to admin layout

## Next Steps

1. Configure your AI API endpoint in `.env`
2. Update the `callAIAgent()` method with your AI integration
3. Test the chatbot functionality
4. Customize colors/styling as needed
5. (Optional) Add database persistence for chat history

## Support

For issues or questions:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check browser console for JavaScript errors
3. Verify Livewire is properly installed: `composer show livewire/livewire`

---

**Created:** November 12, 2025
**Version:** 1.0
**Author:** AI Assistant
