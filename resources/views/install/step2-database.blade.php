<x-install-layout :step="2">
    <h2 class="text-lg font-semibold text-gray-900 font-display mb-1">Cấu hình Database</h2>
    <p class="text-sm text-gray-500 mb-6">Nhập thông tin kết nối MySQL. Database phải được tạo sẵn trước.</p>

    <form method="POST" action="{{ route('install.save-database') }}" class="space-y-4">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Database Host</label>
                <input type="text" name="db_host" value="{{ old('db_host', '127.0.0.1') }}"
                       class="w-full px-3 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-red-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Port</label>
                <input type="text" name="db_port" value="{{ old('db_port', '3306') }}"
                       class="w-full px-3 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-red-400">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tên Database</label>
            <input type="text" name="db_database" value="{{ old('db_database', 'tutorcenter') }}" placeholder="tutorcenter"
                   class="w-full px-3 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-red-400">
            <p class="text-xs text-gray-400 mt-1">Database này phải đã tồn tại trên MySQL server.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
            <input type="text" name="db_username" value="{{ old('db_username', 'root') }}"
                   class="w-full px-3 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-red-400">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="db_password" value="{{ old('db_password') }}" placeholder="Để trống nếu không có"
                   class="w-full px-3 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-red-400">
        </div>

        <div class="flex justify-between pt-4">
            <a href="{{ route('install.index') }}" class="px-4 py-2.5 text-sm text-gray-500 hover:text-gray-700">← Quay lại</a>
            <button type="submit" class="px-6 py-2.5 bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition-colors">
                Kiểm tra & Tiếp tục →
            </button>
        </div>
    </form>
</x-install-layout>
