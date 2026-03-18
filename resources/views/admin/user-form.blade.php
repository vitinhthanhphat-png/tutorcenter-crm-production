<x-app-layout>
    <x-slot name="heading">{{ $user ? 'Sửa User' : 'Thêm User' }}</x-slot>
    <x-slot name="subheading">{{ $user ? $user->email : 'Tạo tài khoản mới trong hệ thống' }}</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.users') }}" class="text-sm text-gray-500 hover:text-gray-700">← Danh sách Users</a>
    </x-slot>

    <div class="p-8 max-w-2xl space-y-6">

        @if(session('success'))
        <div class="px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="px-4 py-3 bg-red-50 border border-red-200 text-red-600 text-sm">{{ session('error') }}</div>
        @endif

        @if($errors->any())
        <div class="p-3 bg-red-50 border border-red-200 text-red-600 text-sm">
            <ul>@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        {{-- ═══ MAIN USER FORM ═══ --}}
        <div class="bg-white border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-800">Thông tin tài khoản</h2>
            </div>

            <form method="POST"
                  action="{{ $user ? route('admin.users.update', $user) : route('admin.users.store') }}"
                  class="px-6 py-5 space-y-5">
                @csrf
                @if($user) @method('PUT') @endif

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Họ tên <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user?->name) }}" required
                               class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user?->email) }}" required
                               class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">
                            Mật khẩu {{ $user ? '(để trống = không đổi)' : '' }}
                            <span class="text-red-500">{{ $user ? '' : '*' }}</span>
                        </label>
                        <input type="password" name="password" {{ $user ? '' : 'required' }} minlength="6"
                               class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400"
                               placeholder="Tối thiểu 6 ký tự">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Role <span class="text-red-500">*</span></label>
                        <select name="role" required class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                            @foreach($roles as $r)
                            <option value="{{ $r }}" {{ old('role', $user?->role) === $r ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $r)) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">
                            Tenant chính
                            <span class="text-gray-300 font-normal">(home)</span>
                        </label>
                        <select name="tenant_id" class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                            <option value="">— Super Admin (không tenant) —</option>
                            @foreach($tenants as $t)
                            <option value="{{ $t->id }}" {{ old('tenant_id', $user?->tenant_id) == $t->id ? 'selected' : '' }}>
                                {{ $t->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="bg-indigo-600 text-white px-5 py-2 text-sm hover:bg-indigo-700 transition-colors font-medium">
                        {{ $user ? 'Cập nhật' : 'Tạo User' }}
                    </button>
                    <a href="{{ route('admin.users') }}"
                       class="px-5 py-2 text-sm border border-gray-200 hover:bg-gray-50 transition-colors">Hủy</a>
                </div>
            </form>
        </div>

        {{-- ═══ MULTI-TENANT ASSIGNMENTS (only on edit) ═══ --}}
        @if($user)
        <div class="bg-white border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-gray-800">Phân công đa trung tâm</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Ngoài Tenant chính, user còn có thể truy cập các trung tâm/chi nhánh bên dưới</p>
                </div>
                <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-0.5">{{ $assignments->count() }} phân công</span>
            </div>

            {{-- Current assignments list --}}
            <div class="divide-y divide-gray-50">
                {{-- Home tenant (implicit, always shown) --}}
                @if($user->tenant_id)
                <div class="px-6 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-indigo-400 flex-shrink-0"></span>
                        <div>
                            <span class="text-sm font-medium text-gray-900">{{ $user->tenant->name ?? '—' }}</span>
                            <span class="ml-2 text-xs bg-indigo-50 text-indigo-600 px-1.5 py-0.5">Tenant chính</span>
                            @if($user->branch_id)
                            <span class="text-xs text-gray-400 ml-1">· {{ $user->branch->name ?? '' }}</span>
                            @endif
                        </div>
                    </div>
                    <span class="text-xs text-gray-300 italic">Không thể xóa</span>
                </div>
                @endif

                {{-- Additional assignments --}}
                @forelse($assignments as $a)
                <div class="px-6 py-3 flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full {{ $a->status === 'active' ? 'bg-green-400' : 'bg-gray-300' }} flex-shrink-0"></span>
                        <div>
                            <span class="text-sm text-gray-800">{{ $a->tenant->name ?? '—' }}</span>
                            @if($a->branch_id)
                            <span class="text-xs text-gray-400 ml-1">· {{ $a->branch->name ?? '' }}</span>
                            @else
                            <span class="text-xs text-gray-300 ml-1">· Tất cả chi nhánh</span>
                            @endif
                            @if($a->note)
                            <span class="text-xs text-gray-300 ml-1 italic">{{ $a->note }}</span>
                            @endif
                        </div>
                    </div>
                    <form method="POST"
                          action="{{ route('admin.users.assignments.remove', [$user, $a]) }}"
                          onsubmit="return confirm('Xóa phân công này?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="text-xs text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition-all px-2 py-1 hover:bg-red-50">
                            × Xóa
                        </button>
                    </form>
                </div>
                @empty
                @if(!$user->tenant_id)
                <div class="px-6 py-3 text-xs text-gray-300 italic">Chưa có phân công nào.</div>
                @endif
                @endforelse
            </div>

            {{-- Add new assignment mini-form --}}
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                <p class="text-xs font-medium text-gray-500 mb-3">+ Thêm phân công mới</p>
                <form method="POST" action="{{ route('admin.users.assignments.add', $user) }}"
                      class="flex flex-wrap gap-2 items-end">
                    @csrf

                    <div class="flex-1 min-w-36">
                        <label class="block text-xs text-gray-400 mb-1">Trung tâm *</label>
                        <select name="tenant_id" required
                                class="w-full border border-gray-200 px-2 py-1.5 text-xs focus:outline-none focus:border-indigo-400 bg-white">
                            <option value="">— Chọn —</option>
                            @foreach($tenants as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex-1 min-w-36">
                        <label class="block text-xs text-gray-400 mb-1">Chi nhánh <span class="text-gray-300">(tùy chọn)</span></label>
                        <select name="branch_id"
                                class="w-full border border-gray-200 px-2 py-1.5 text-xs focus:outline-none focus:border-indigo-400 bg-white">
                            <option value="">— Tất cả chi nhánh —</option>
                            @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->tenant->name ?? '?' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex-1 min-w-32">
                        <label class="block text-xs text-gray-400 mb-1">Ghi chú</label>
                        <input type="text" name="note" placeholder="VD: Dạy thêm Q7..."
                               class="w-full border border-gray-200 px-2 py-1.5 text-xs focus:outline-none focus:border-indigo-400">
                    </div>

                    <button type="submit"
                            class="bg-indigo-600 text-white px-4 py-1.5 text-xs hover:bg-indigo-700 transition-colors font-medium flex-shrink-0">
                        Thêm
                    </button>
                </form>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>
