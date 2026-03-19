<x-install-layout :step="5">
    <div class="text-center py-8">
        <div class="w-16 h-16 bg-green-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h2 class="text-xl font-bold text-gray-900 font-display mb-2">Cài đặt thành công!</h2>
        <p class="text-sm text-gray-500 mb-8">TutorCenter CRM đã sẵn sàng sử dụng.</p>

        <div class="bg-green-50 border border-green-100 p-4 text-left mb-6 max-w-sm mx-auto">
            <p class="text-sm font-medium text-green-800 mb-2">Thông tin đăng nhập:</p>
            <p class="text-sm text-green-700">
                Sử dụng email và mật khẩu bạn vừa tạo để đăng nhập vào hệ thống.
            </p>
        </div>

        <div class="bg-yellow-50 border border-yellow-100 p-4 text-left mb-8 max-w-sm mx-auto">
            <p class="text-sm font-medium text-yellow-800 mb-2">🔒 Bảo mật:</p>
            <ul class="text-sm text-yellow-700 space-y-1">
                <li>• File <code>.env</code> đã được tạo và bảo mật</li>
                <li>• APP_KEY đã được sinh tự động</li>
                <li>• Trang cài đặt đã bị khóa</li>
            </ul>
        </div>

        <a href="{{ url('/login') }}"
           class="inline-block px-8 py-3 bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition-colors">
            Đăng nhập →
        </a>
    </div>
</x-install-layout>
