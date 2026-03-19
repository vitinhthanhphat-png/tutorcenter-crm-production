<x-install-layout :step="1">
    <h2 class="text-lg font-semibold text-gray-900 font-display mb-1">Kiểm tra yêu cầu hệ thống</h2>
    <p class="text-sm text-gray-500 mb-6">Đảm bảo server đáp ứng tất cả yêu cầu trước khi cài đặt.</p>

    {{-- PHP Extensions --}}
    <div class="mb-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">PHP Extensions</h3>
        <div class="space-y-2">
            @foreach($requirements as $req)
                <div class="flex items-center justify-between py-2 px-3 {{ $req['status'] ? 'bg-green-50' : 'bg-red-50' }}">
                    <span class="text-sm text-gray-700">{{ $req['name'] }}</span>
                    @if($req['status'])
                        <span class="text-green-600 text-sm font-medium">✓ OK</span>
                    @else
                        <span class="text-red-600 text-sm font-medium">✗ Thiếu</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Directory Permissions --}}
    <div class="mb-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Quyền thư mục</h3>
        <div class="space-y-2">
            @foreach($permissions as $perm)
                <div class="flex items-center justify-between py-2 px-3 {{ $perm['status'] ? 'bg-green-50' : 'bg-red-50' }}">
                    <span class="text-sm text-gray-700 font-mono">{{ $perm['name'] }}</span>
                    @if($perm['status'])
                        <span class="text-green-600 text-sm font-medium">✓ Writable</span>
                    @else
                        <span class="text-red-600 text-sm font-medium">✗ Không ghi được</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="flex justify-end">
        @if($allPassed && $allPermissions)
            <a href="{{ route('install.database') }}"
               class="px-6 py-2.5 bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition-colors">
                Tiếp tục →
            </a>
        @else
            <span class="px-6 py-2.5 bg-gray-200 text-gray-500 text-sm font-medium cursor-not-allowed">
                Vui lòng cài đặt đủ yêu cầu
            </span>
        @endif
    </div>
</x-install-layout>
