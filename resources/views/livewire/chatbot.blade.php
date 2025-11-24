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
            class="bg-white rounded-lg shadow-2xl w-full max-w-2xl h-[600px] flex flex-col transition-all duration-300 transform"
            style="max-height: 90vh;"
        >
            <!-- Chat Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-4 rounded-t-lg flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('download.jpeg') }}" alt="HSA Assistant" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">HSA Assistant</h3>
                        <p class="text-xs text-blue-100">Online</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <!-- Chat History Button -->
                    <button 
                        wire:click="toggleHistory"
                        class="p-2 hover:bg-white hover:bg-opacity-20 rounded-full transition-colors"
                        title="Chat History"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                    
                    <!-- New Chat Button -->
                    <button 
                        wire:click="startNewChat"
                        class="p-2 hover:bg-white hover:bg-opacity-20 rounded-full transition-colors"
                        title="New Chat"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
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

            <!-- Chat History View -->
            @if($showHistory)
                <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-800">Chat History</h4>
                        <button 
                            wire:click="startNewChat"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors"
                        >
                            New Chat
                        </button>
                    </div>
                    
                    @php
                        $sessions = $this->getChatSessions();
                    @endphp
                    
                    @if($sessions->isEmpty())
                        <div class="text-center text-gray-500 py-12">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <p class="text-lg font-medium">No chat history yet</p>
                            <p class="text-sm">Start a conversation to see it here</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($sessions as $session)
                                <div 
                                    wire:click="loadSession('{{ $session->session_id }}')"
                                    class="p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow cursor-pointer border-l-4 {{ $session->session_id === $sessionId ? 'border-blue-600' : 'border-gray-300' }}"
                                >
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-800">
                                                {{ $session->started_at->format('M d, Y - H:i') }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Last message: {{ $session->last_message_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
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
                                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center flex-shrink-0 overflow-hidden border-2 border-blue-600">
                                        <img src="{{ asset('download.jpeg') }}" alt="HSA Assistant" class="w-full h-full object-cover">
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
                            <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center overflow-hidden border-2 border-blue-600">
                                <img src="{{ asset('download.jpeg') }}" alt="HSA Assistant" class="w-full h-full object-cover">
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
            @endif

            <!-- Chat Input Area -->
            <div class="bg-white border-t border-gray-200 p-4 rounded-b-lg">
                @if(!$showHistory)
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
                @else
                <div class="text-center text-gray-500 py-3">
                    <p class="text-sm">Select a session to continue chatting or start a new chat</p>
                </div>
                @endif
            </div>
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
