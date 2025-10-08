@extends('layouts.frontend')

@section('title', 'Hồ sơ của tôi')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            Thông tin tài khoản
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Cập nhật thông tin tài khoản của bạn.
                        </p>
                    </header>

                    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                        @csrf
                        @method('put')

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Họ tên</label>
                            <input type="text" name="name" id="name" value="{{ old('name', Auth::user()->name) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', Auth::user()->email) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Lưu thay đổi
                            </button>

                            @if (session('status') === 'profile-updated')
                                <p class="text-sm text-gray-600">Đã lưu.</p>
                            @endif
                        </div>
                    </form>
                </section>
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            Đổi mật khẩu
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Đảm bảo tài khoản của bạn đang sử dụng mật khẩu đủ mạnh và an toàn.
                        </p>
                    </header>

                    <form method="post" action="{{ route('profile.password') }}" class="mt-6 space-y-6">
                        @csrf
                        @method('put')

                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700">
                                Mật khẩu hiện tại
                            </label>
                            <input type="password" name="current_password" id="current_password"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('current_password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Mật khẩu mới
                            </label>
                            <input type="password" name="password" id="password"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                Xác nhận mật khẩu mới
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('password_confirmation')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Lưu mật khẩu
                            </button>

                            @if (session('status') === 'password-updated')
                                <p class="text-sm text-gray-600">Đã lưu.</p>
                            @endif
                        </div>
                    </form>
                </section>
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                <section class="space-y-6">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            Xóa tài khoản
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Sau khi tài khoản của bạn bị xóa, tất cả tài nguyên và dữ liệu của tài khoản sẽ bị xóa vĩnh viễn.
                        </p>
                    </header>

                    <button type="button"
                            x-data=""
                            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Xóa tài khoản
                    </button>

                    <div x-data="{ open: false }" 
                         x-show="open" 
                         x-on:open-modal.window="if ($event.detail === 'confirm-user-deletion') open = true"
                         x-on:close.stop="open = false"
                         x-on:keydown.escape.window="open = false"
                         class="fixed inset-0 z-50 overflow-y-auto" 
                         style="display: none;">
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div x-show="open" 
                                 x-transition:enter="ease-out duration-300" 
                                 x-transition:enter-start="opacity-0" 
                                 x-transition:enter-end="opacity-100" 
                                 x-transition:leave="ease-in duration-200" 
                                 x-transition:leave-start="opacity-100" 
                                 x-transition:leave-end="opacity-0"
                                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                                 @click="open = false">
                            </div>

                            <div x-show="open" 
                                 x-transition:enter="ease-out duration-300" 
                                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                                 x-transition:leave="ease-in duration-200" 
                                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                 class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                                    @csrf
                                    @method('delete')

                                    <h2 class="text-lg font-medium text-gray-900">
                                        Bạn có chắc chắn muốn xóa tài khoản của mình?
                                    </h2>

                                    <p class="mt-1 text-sm text-gray-600">
                                        Sau khi tài khoản của bạn bị xóa, tất cả tài nguyên và dữ liệu của tài khoản sẽ bị xóa vĩnh viễn. Vui lòng nhập mật khẩu của bạn để xác nhận bạn muốn xóa vĩnh viễn tài khoản của mình.
                                    </p>

                                    <div class="mt-6">
                                        <label for="password" class="sr-only">Mật khẩu</label>
                                        <input type="password" name="password" id="password"
                                               class="mt-1 block w-3/4 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               placeholder="Mật khẩu">
                                        @error('password', 'userDeletion')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="mt-6 flex justify-end">
                                        <button type="button"
                                                x-on:click="open = false"
                                                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                            Hủy
                                        </button>

                                        <button type="submit"
                                                class="ml-3 inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Xóa tài khoản
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
@endsection
