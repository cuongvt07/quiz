<div class="modal fade" id="adminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="admin-form">
                <div class="modal-header">
                    <h5 class="modal-title" id="adminModalLabel">Thêm/Sửa tài khoản admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body space-y-3">
                    <input type="hidden" name="id" id="admin-id">
                    <div>
                        <label class="block mb-1 font-semibold">Tên <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="admin-name" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block mb-1 font-semibold">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="admin-email" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block mb-1 font-semibold">Mật khẩu <span class="text-red-500">*</span></label>
                        <input type="password" name="password" id="admin-password" class="w-full border rounded px-3 py-2">
                        <small id="password-note" class="text-gray-500">Để trống nếu không đổi mật khẩu khi sửa</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="px-4 py-2 rounded bg-gray-100 hover:bg-gray-200" data-bs-dismiss="modal">Huỷ</button>
                    <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 font-semibold">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>
