<x-app-layout>
    <x-slot name="heading">Quản lý Users</x-slot>
    <x-slot name="subheading">Tất cả users · {{ $users->total() }} tài khoản</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.users.create') }}" class="text-xs bg-indigo-600 text-white px-4 py-2 hover:bg-indigo-700 transition-colors">+ Thêm User</a>
    </x-slot>

    {{-- Reset-password modal --}}
    <div id="pwModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="bg-white w-96 p-6 shadow-xl border border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900 mb-4" style="font-family:'Space Grotesk',sans-serif">Đặt lại mật khẩu</h3>
            <p id="pwModalEmail" class="text-xs text-gray-400 mb-4"></p>
            <form id="pwForm" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Mật khẩu mới <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required minlength="6"
                           class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400"
                           placeholder="Tối thiểu 6 ký tự">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Xác nhận mật khẩu <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" required
                           class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 text-sm hover:bg-indigo-700 transition-colors">Đặt lại</button>
                    <button type="button" onclick="closePwModal()" class="px-4 py-2 text-sm border border-gray-200 hover:bg-gray-50">Hủy</button>
                </div>
            </form>
        </div>
    </div>

    <div class="p-8 space-y-4">
        @if(session('success'))
        <div class="px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="px-4 py-3 bg-red-50 border border-red-200 text-red-600 text-sm">{{ session('error') }}</div>
        @endif

        {{-- Filters --}}
        <form method="GET" class="flex gap-3 items-center flex-wrap">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm tên / email..."
                   class="border border-gray-200 px-3 py-2 text-sm w-60 focus:outline-none focus:border-indigo-400">

            <select name="tenant_id" class="border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                <option value="">Tất cả Tenant</option>
                @foreach($tenants as $t)
                <option value="{{ $t->id }}" {{ request('tenant_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
            </select>

            <select name="role" class="border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                <option value="">Tất cả Role</option>
                @foreach($roles as $r)
                <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$r)) }}</option>
                @endforeach
            </select>

            <button type="submit" class="bg-gray-900 text-white px-4 py-2 text-sm hover:bg-gray-700 transition-colors">Lọc</button>
            <a href="{{ route('admin.users') }}" class="text-sm text-gray-400 hover:text-gray-600">Reset</a>
        </form>

        {{-- Table --}}
        <div class="bg-white border border-gray-100">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">Tên</th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">Email</th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">Role</th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">Tenant</th>
                        <th class="px-5 py-2.5 text-center text-xs font-medium text-gray-400">Trạng thái</th>
                        <th class="px-5 py-2.5 text-center text-xs font-medium text-gray-400 min-w-[200px]">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $u)
                    @php
                        $roleColors = [
                            'super_admin'    => 'bg-purple-100 text-purple-700',
                            'center_manager' => 'bg-blue-100 text-blue-700',
                            'accountant'     => 'bg-amber-100 text-amber-700',
                            'teacher'        => 'bg-green-100 text-green-700',
                        ];
                        $rc = $roleColors[$u->role] ?? 'bg-gray-100 text-gray-500';
                        $isActive = $u->is_active ?? true;
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors {{ !$isActive ? 'opacity-60' : '' }}">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full {{ $isActive ? 'bg-gray-900' : 'bg-gray-400' }} flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                                    {{ strtoupper(mb_substr($u->name, 0, 2)) }}
                                </div>
                                <span class="text-sm font-medium text-gray-800">{{ $u->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $u->email }}</td>
                        <td class="px-5 py-3">
                            <span class="text-xs px-2 py-0.5 {{ $rc }}">{{ ucfirst(str_replace('_',' ',$u->role)) }}</span>
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-400">{{ $u->tenant?->name ?? '— (System)' }}</td>
                        <td class="px-5 py-3 text-center">
                            @if(!$isActive)
                                <span class="text-xs px-2 py-0.5 bg-red-50 text-red-500">Vô hiệu</span>
                            @elseif($u->email_verified_at)
                                <span class="text-xs px-2 py-0.5 bg-green-50 text-green-600">✓ Active</span>
                            @else
                                <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-400">Unverified</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-center gap-2 flex-wrap">
                                {{-- Edit --}}
                                <a href="{{ route('admin.users.edit', $u) }}"
                                   class="text-xs px-2.5 py-1 border border-indigo-200 text-indigo-600 hover:bg-indigo-50 transition-colors">
                                    ✏️ Sửa
                                </a>

                                @if($u->id !== auth()->id())
                                    {{-- Toggle Active --}}
                                    <form method="POST" action="{{ route('admin.users.toggle', $u) }}">
                                        @csrf @method('PATCH')
                                        <button class="text-xs px-2.5 py-1 border transition-colors {{ $isActive ? 'border-orange-200 text-orange-600 hover:bg-orange-50' : 'border-green-200 text-green-600 hover:bg-green-50' }}">
                                            {{ $isActive ? '⏸ Khóa' : '▶ Mở' }}
                                        </button>
                                    </form>

                                    {{-- Reset Password --}}
                                    <button type="button"
                                            data-url="{{ route('admin.users.reset-password', $u) }}"
                                            data-email="{{ $u->email }}"
                                            onclick="openPwModal(this.dataset.url, this.dataset.email)"
                                            class="text-xs px-2.5 py-1 border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
                                        🔑 Mật khẩu
                                    </button>

                                    {{-- Delete --}}
                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                          onsubmit="return confirm('Xóa vĩnh viễn user {{ addslashes($u->email) }}?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs px-2.5 py-1 border border-red-200 text-red-500 hover:bg-red-50 transition-colors">
                                            🗑 Xóa
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-sm text-gray-400">Không tìm thấy user nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-5 py-4 border-t border-gray-100">{{ $users->links() }}</div>
        </div>
    </div>

    <script>
    function openPwModal(action, email) {
        document.getElementById('pwForm').action = action;
        document.getElementById('pwModalEmail').textContent = email;
        document.getElementById('pwModal').classList.remove('hidden');
    }
    function closePwModal() {
        document.getElementById('pwModal').classList.add('hidden');
        document.getElementById('pwForm').reset();
    }
    document.getElementById('pwModal').addEventListener('click', function(e) {
        if (e.target === this) closePwModal();
    });
    </script>
</x-app-layout>
