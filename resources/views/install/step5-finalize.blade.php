<x-install-layout :step="5">
    <h2 class="text-lg font-semibold text-gray-900 font-display mb-1">Xác nhận & Cài đặt</h2>
    <p class="text-sm text-gray-500 mb-6">Kiểm tra lại thông tin trước khi bắt đầu cài đặt.</p>

    <div class="space-y-3 mb-8">
        <div class="flex justify-between py-2 px-3 bg-gray-50">
            <span class="text-sm text-gray-500">Database Host</span>
            <span class="text-sm font-medium text-gray-900">{{ $config['db_host'] }}</span>
        </div>
        <div class="flex justify-between py-2 px-3 bg-gray-50">
            <span class="text-sm text-gray-500">Database Name</span>
            <span class="text-sm font-medium text-gray-900">{{ $config['db_database'] }}</span>
        </div>
        <div class="flex justify-between py-2 px-3 bg-gray-50">
            <span class="text-sm text-gray-500">DB Username</span>
            <span class="text-sm font-medium text-gray-900">{{ $config['db_username'] }}</span>
        </div>
        <div class="flex justify-between py-2 px-3 bg-gray-50">
            <span class="text-sm text-gray-500">Admin Email</span>
            <span class="text-sm font-medium text-gray-900">{{ $config['admin_email'] }}</span>
        </div>
        <div class="flex justify-between py-2 px-3 bg-gray-50">
            <span class="text-sm text-gray-500">Admin Name</span>
            <span class="text-sm font-medium text-gray-900">{{ $config['admin_name'] }}</span>
        </div>
        <div class="flex justify-between py-2 px-3 bg-gray-50">
            <span class="text-sm text-gray-500">Tên ứng dụng</span>
            <span class="text-sm font-medium text-gray-900">{{ $config['app_name'] }}</span>
        </div>
        <div class="flex justify-between py-2 px-3 bg-gray-50">
            <span class="text-sm text-gray-500">URL</span>
            <span class="text-sm font-medium text-gray-900">{{ $config['app_url'] }}</span>
        </div>
        <div class="flex justify-between py-2 px-3 bg-gray-50">
            <span class="text-sm text-gray-500">Trung tâm</span>
            <span class="text-sm font-medium text-gray-900">{{ $config['tenant_name'] }}</span>
        </div>
    </div>

    <div class="p-4 bg-yellow-50 border border-yellow-100 mb-6">
        <p class="text-sm text-yellow-800">⚠ Quá trình cài đặt sẽ:</p>
        <ul class="text-sm text-yellow-700 mt-2 space-y-1 ml-4 list-disc">
            <li>Tạo file cấu hình <code>.env</code></li>
            <li>Sinh APP_KEY mã hóa bảo mật</li>
            <li>Tạo toàn bộ bảng dữ liệu</li>
            <li>Tạo tài khoản Super Admin</li>
        </ul>
    </div>

    <form method="POST" action="{{ route('install.execute') }}" id="installForm">
        @csrf
        <div class="flex justify-between">
            <a href="{{ route('install.settings') }}" class="px-4 py-2.5 text-sm text-gray-500 hover:text-gray-700">← Quay lại</a>
            <button type="submit" id="installBtn" onclick="this.disabled=true; this.innerText='⏳ Đang cài đặt...'; document.getElementById('installForm').submit();"
                    class="px-8 py-2.5 bg-green-600 text-white text-sm font-medium hover:bg-green-700 transition-colors">
                🚀 Bắt đầu cài đặt
            </button>
        </div>
    </form>
</x-install-layout>
