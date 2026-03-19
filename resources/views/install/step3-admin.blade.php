<x-install-layout :step="3">
    <h2 class="text-lg font-semibold text-gray-900 font-display mb-1">Tạo tài khoản Admin</h2>
    <p class="text-sm text-gray-500 mb-6">Tài khoản Super Admin có toàn quyền quản trị hệ thống.</p>

    <form method="POST" action="{{ route('install.save-admin') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên</label>
            <input type="text" name="admin_name" value="{{ old('admin_name') }}" placeholder="Nguyễn Văn A"
                   class="w-full px-3 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-red-400" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email đăng nhập</label>
            <input type="email" name="admin_email" value="{{ old('admin_email') }}" placeholder="admin@tutorcenter.vn"
                   class="w-full px-3 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-red-400" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu</label>
            <input type="password" name="admin_password" placeholder="Tối thiểu 8 ký tự"
                   class="w-full px-3 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-red-400" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu</label>
            <input type="password" name="admin_password_confirmation" placeholder="Nhập lại mật khẩu"
                   class="w-full px-3 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-red-400" required>
        </div>

        <div class="flex justify-between pt-4">
            <a href="{{ route('install.database') }}" class="px-4 py-2.5 text-sm text-gray-500 hover:text-gray-700">← Quay lại</a>
            <button type="submit" class="px-6 py-2.5 bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition-colors">
                Tiếp tục →
            </button>
        </div>
    </form>
</x-install-layout>
