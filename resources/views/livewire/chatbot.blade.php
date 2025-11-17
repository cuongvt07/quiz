<div>
    <!-- Floating Chat Button (Bottom Right) -->
    @auth
    <div class="fixed bottom-6 right-6 z-50">
        @if(!$isOpen)
            <button 
                wire:click="toggleChat"
                class="bg-blue-600 hover:bg-blue-700 text-white rounded-full p-4 shadow-lg transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-4 focus:ring-blue-300"
                title="Open Chat Assistant"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <span class="absolute top-0 right-0 block h-3 w-3 rounded-full bg-green-400 ring-2 ring-white"></span>
            </button>
        @endif
    </div>

    <!-- Chat Modal/Popup -->
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50 transition-opacity duration-300">
        <div 
            class="bg-white rounded-lg shadow-2xl w-full max-w-2xl flex flex-col transition-all duration-300 transform"
            :class="{ 'h-96': @js($isMinimized), 'h-[600px]': !@js($isMinimized) }"
            style="max-height: 90vh;"
        >
            <!-- Chat Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-4 rounded-t-lg flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">AI Assistant</h3>
                        <p class="text-xs text-blue-100">Online</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <!-- Minimize Button -->
                    <button 
                        wire:click="minimizeChat"
                        class="p-2 hover:bg-white hover:bg-opacity-20 rounded-full transition-colors"
                        title="{{ $isMinimized ? 'Maximize' : 'Minimize' }}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            @if($isMinimized)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            @endif
                        </svg>
                    </button>
                    
                    <!-- Clear Chat Button -->
                    <button 
                        wire:click="clearChat"
                        class="p-2 hover:bg-white hover:bg-opacity-20 rounded-full transition-colors"
                        title="Clear Chat"
                        onclick="return confirm('Are you sure you want to clear the chat history?')"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                    
                    <!-- Close Button -->
                    <button 
                        wire:click="closeChat"
                        class="p-2 hover:bg-white hover:bg-opacity-20 rounded-full transition-colors"
                        title="Close"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            @if(!$isMinimized)
            <!-- Chat Messages Area -->
            <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50" id="chatMessages">
                @if(empty($messages))
                    <div class="text-center text-gray-500 py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p class="text-lg font-medium">Start a conversation</p>
                        <p class="text-sm">Ask me anything about your exams!</p>
                    </div>
                @else
                    @foreach($messages as $msg)
                        <div class="flex {{ $msg['type'] === 'user' ? 'justify-end' : 'justify-start' }}">
                            <div class="flex items-end space-x-2 max-w-[80%]">
                                @if($msg['type'] === 'ai')
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-600 to-purple-600 flex items-center justify-center flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                
                                <div class="flex flex-col">
                                    <div class="px-4 py-3 rounded-2xl {{ $msg['type'] === 'user' ? 'bg-blue-600 text-white' : 'bg-white text-gray-800 shadow-md' }}">
                                        <p class="text-sm whitespace-pre-wrap">{{ $msg['content'] }}</p>
                                    </div>
                                    <span class="text-xs text-gray-500 mt-1 {{ $msg['type'] === 'user' ? 'text-right' : 'text-left' }}">
                                        {{ $msg['timestamp'] }}
                                    </span>
                                </div>

                                @if($msg['type'] === 'user')
                                    <div class="w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif

                <!-- Typing Indicator -->
                @if($isTyping)
                    <div class="flex justify-start">
                        <div class="flex items-end space-x-2">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-600 to-purple-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="bg-white px-4 py-3 rounded-2xl shadow-md">
                                <div class="flex space-x-2">
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Chat Input Area -->
            <div class="bg-white border-t border-gray-200 p-4 rounded-b-lg">
                <form wire:submit.prevent="sendMessage" class="flex items-end space-x-3">
                    <div class="flex-1">
                        <textarea 
                            wire:model.defer="message"
                            rows="1"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                            placeholder="Type your message..."
                            @keydown.enter.prevent="if(!event.shiftKey) { $wire.sendMessage(); }"
                        ></textarea>
                    </div>
                    <button 
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </form>
                <p class="text-xs text-gray-500 mt-2 text-center">
                    Press Enter to send, Shift+Enter for new line
                </p>
            </div>
            @endif
        </div>
    </div>
    @endif
    @endauth

    <script>
        // Auto-scroll to bottom when new messages arrive
        window.addEventListener('message-sent', event => {
            setTimeout(() => {
                const chatMessages = document.getElementById('chatMessages');
                if (chatMessages) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            }, 100);
        });

        // Scroll to bottom on initial load
        document.addEventListener('DOMContentLoaded', function() {
            const chatMessages = document.getElementById('chatMessages');
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        });
    </script>
</div>
