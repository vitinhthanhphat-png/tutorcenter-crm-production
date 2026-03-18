<x-app-layout>
    <x-slot name="heading">{{ $tenant ? 'Sửa Tenant' : 'Thêm Tenant mới' }}</x-slot>
    <x-slot name="subheading">{{ $tenant ? 'Cập nhật thông tin trung tâm' : 'Tạo một trung tâm mới trong hệ thống' }}</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.tenants') }}" class="text-sm text-gray-500 hover:text-gray-700">← Danh sách Tenant</a>
    </x-slot>

    <div class="p-8 max-w-2xl">
        @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 text-sm">
            <ul>@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <div class="bg-white border border-gray-100">
            {{-- Form header --}}
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-100 flex items-center justify-center text-lg">🏢</div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-900" style="font-family:'Space Grotesk',sans-serif">
                        {{ $tenant ? $tenant->name : 'Trung tâm mới' }}
                    </h2>
                    <p class="text-xs text-gray-400">Điền đầy đủ thông tin bên dưới</p>
                </div>
            </div>

            <form method="POST" action="{{ $tenant ? route('admin.tenants.update', $tenant) : route('admin.tenants.store') }}"
                  class="px-6 py-6 space-y-5">
                @csrf
                @if($tenant) @method('PUT') @endif

                {{-- Row 1: Name + Domain --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tên Trung tâm <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $tenant?->name) }}" required
                               class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-200"
                               placeholder="Trung tâm Gia sư ABC">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Domain <span class="text-red-500">*</span></label>
                        <input type="text" name="domain" value="{{ old('domain', $tenant?->domain) }}" required
                               class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-200 font-mono"
                               placeholder="abc.tutorcenter.vn">
                        <p class="text-xs text-gray-400 mt-1">Subdomain phân biệt tenant (không trùng lặp)</p>
                    </div>
                </div>

                {{-- Row 2: Phone + Email --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Số điện thoại</label>
                        <input type="text" name="phone" value="{{ old('phone', $tenant?->phone) }}"
                               class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400"
                               placeholder="028.xxxx.xxxx">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Email liên hệ</label>
                        <input type="email" name="email" value="{{ old('email', $tenant?->email) }}"
                               class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400"
                               placeholder="info@trungtam.vn">
                    </div>
                </div>

                {{-- Row 3: Address --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Địa chỉ</label>
                    <input type="text" name="address" value="{{ old('address', $tenant?->address) }}"
                           class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400"
                           placeholder="123 Đường ABC, Quận X, TP.HCM">
                </div>

                {{-- Row 4: Status --}}
                <div class="w-1/2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Trạng thái ban đầu</label>
                    <select name="status" class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                        @foreach(['active' => '✅ Hoạt động', 'inactive' => '⊘ Không hoạt động', 'suspended' => '⏸ Tạm khóa'] as $val => $label)
                        <option value="{{ $val }}" {{ old('status', $tenant?->status ?? 'active') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Divider --}}
                <div class="border-t border-gray-100 pt-4 flex items-center gap-3">
                    <button type="submit"
                            class="flex items-center gap-2 bg-indigo-600 text-white px-6 py-2.5 text-sm hover:bg-indigo-700 transition-colors font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tenant ? 'M5 13l4 4L19 7' : 'M12 4v16m8-8H4' }}"/>
                        </svg>
                        {{ $tenant ? 'Lưu thay đổi' : 'Tạo Tenant' }}
                    </button>
                    <a href="{{ route('admin.tenants') }}"
                       class="px-5 py-2.5 text-sm border border-gray-200 hover:bg-gray-50 transition-colors text-gray-600">
                        Hủy
                    </a>
                    @if($tenant)
                    <form method="POST" action="{{ route('admin.tenants.toggle', $tenant) }}" class="ml-auto">
                        @csrf @method('PATCH')
                        <button class="text-xs px-4 py-2 border transition-colors {{ $tenant->status === 'active' ? 'border-orange-200 text-orange-600 hover:bg-orange-50' : 'border-green-200 text-green-600 hover:bg-green-50' }}">
                            {{ $tenant->status === 'active' ? '⏸ Tạm khóa Tenant' : '▶ Kích hoạt Tenant' }}
                        </button>
                    </form>
                    @endif
                </div>
            </form>
        </div>

        @if($tenant)
        {{-- Danger zone --}}
        <div class="mt-6 bg-white border border-red-100">
            <div class="px-6 py-4 border-b border-red-100">
                <h3 class="text-sm font-semibold text-red-700">⚠️ Danger Zone</h3>
            </div>
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-800">Xóa vĩnh viễn tenant này</p>
                    <p class="text-xs text-gray-400 mt-0.5">Toàn bộ dữ liệu (users, học sinh, lớp, hóa đơn) sẽ bị xóa. Không thể hoàn tác.</p>
                </div>
                <form method="POST" action="{{ route('admin.tenants.destroy', $tenant) }}"
                      onsubmit="return confirm('⚠️ XÁC NHẬN XÓA VĨNH VIỄN?\n\nTenant: {{ addslashes($tenant->name) }}\nDomain: {{ $tenant->domain }}\n\nTất cả dữ liệu liên quan sẽ BỊ XÓA HOÀN TOÀN!')">
                    @csrf @method('DELETE')
                    <button class="text-xs px-4 py-2 bg-red-600 text-white hover:bg-red-700 transition-colors">
                        🗑 Xóa Tenant
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
