<div x-data="{ notifications: [] }" 
    @notification.window="
        if ($event.detail && $event.detail.message) {
            notifications.push($event.detail);
            setTimeout(() => {
                notifications = notifications.filter(n => n !== $event.detail);
            }, 3000);
        }
    "
    class="fixed bottom-0 left-0 right-0 flex flex-col items-center space-y-4 px-4 py-6 z-50">
    <template x-for="(notification, index) in notifications" :key="index">
        <div x-show="notification && notification.message"
            x-transition:enter="transform ease-out duration-300 transition"
            x-transition:enter-start="translate-y-2 opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="w-full max-w-4xl shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
            <div :class="{
                'bg-green-50': notification.type === 'success',
                'bg-red-50': notification.type === 'error',
                'bg-blue-50': notification.type === 'info',
                'bg-yellow-50': notification.type === 'warning'
            }">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <template x-if="notification.type === 'success'">
                                <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </template>
                            <template x-if="notification.type === 'error'">
                                <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </template>
                            <template x-if="notification.type === 'info'">
                                <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </template>
                            <template x-if="notification.type === 'warning'">
                                <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </template>
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p x-text="notification.message"
                                :class="{
                                    'text-green-800': notification.type === 'success',
                                    'text-red-800': notification.type === 'error',
                                    'text-blue-800': notification.type === 'info',
                                    'text-yellow-800': notification.type === 'warning'
                                }"
                                class="text-sm font-medium">
                            </p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button @click="notifications = notifications.filter((i, idx) => idx !== index)"
                                :class="{
                                    'text-green-500 hover:text-green-600': notification.type === 'success',
                                    'text-red-500 hover:text-red-600': notification.type === 'error',
                                    'text-blue-500 hover:text-blue-600': notification.type === 'info',
                                    'text-yellow-500 hover:text-yellow-600': notification.type === 'warning'
                                }"
                                class="inline-flex rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2"
                                :class="{
                                    'focus:ring-green-500': notification.type === 'success',
                                    'focus:ring-red-500': notification.type === 'error',
                                    'focus:ring-blue-500': notification.type === 'info',
                                    'focus:ring-yellow-500': notification.type === 'warning'
                                }">
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>