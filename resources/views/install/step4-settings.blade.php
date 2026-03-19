<x-install-layout :step="4">
    <h2 class="text-lg font-semibold text-gray-900 font-display mb-1">Cấu hình ứng dụng</h2>
    <p class="text-sm text-gray-500 mb-6">Đặt tên trung tâm và URL truy cập.</p>

    <form method="POST" action="{{ route('install.save-settings') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tên ứng dụng</label>
            <input type="text" name="app_name" value="{{ old('app_name', 'TutorCenter CRM') }}"
                   class="w-full px-3 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-red-400" required>
            <p class="text-xs text-gray-400 mt-1">Tên hiển thị trên tiêu đề trang, email thông báo.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">URL ứng dụng</label>
            <input type="url" name="app_url" value="{{ old('app_url', url('/')) }}" placeholder="https://crm.trungtam.vn"
                   class="w-full px-3 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-red-400" required>
            <p class="text-xs text-gray-400 mt-1">URL đầy đủ bao gồm https://. Không có dấu / ở cuối.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tên trung tâm đầu tiên</label>
            <input type="text" name="tenant_name" value="{{ old('tenant_name') }}" placeholder="Trung tâm Gia sư ABC"
                   class="w-full px-3 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-red-400" required>
            <p class="text-xs text-gray-400 mt-1">Tenant mặc định sẽ được tạo. Bạn có thể thêm tenant khác sau.</p>
        </div>

        <div class="flex justify-between pt-4">
            <a href="{{ route('install.admin') }}" class="px-4 py-2.5 text-sm text-gray-500 hover:text-gray-700">← Quay lại</a>
            <button type="submit" class="px-6 py-2.5 bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition-colors">
                Tiếp tục →
            </button>
        </div>
    </form>
</x-install-layout>
