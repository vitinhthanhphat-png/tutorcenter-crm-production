<x-app-layout>
    <x-slot name="heading">{{ $branch ? 'Sửa Chi nhánh' : 'Thêm Chi nhánh' }}</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.branches') }}" class="text-sm text-gray-500 hover:text-gray-700">← Danh sách Chi nhánh</a>
    </x-slot>

    <div class="p-8 max-w-lg">
        @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 text-sm">
            <ul>@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <div class="bg-white border border-gray-100 p-6 space-y-5">
            <form method="POST" action="{{ $branch ? route('admin.branches.update', $branch) : route('admin.branches.store') }}">
                @csrf
                @if($branch) @method('PUT') @endif

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tenant <span class="text-red-500">*</span></label>
                    <select name="tenant_id" required class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                        <option value="">Chọn tenant...</option>
                        @foreach($tenants as $t)
                        <option value="{{ $t->id }}" {{ old('tenant_id', $branch?->tenant_id ?? request('tenant_id')) == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tên Chi nhánh <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $branch?->name) }}" required
                           class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400"
                           placeholder="Cơ sở Quận 3">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Địa chỉ</label>
                    <input type="text" name="address" value="{{ old('address', $branch?->address) }}"
                           class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400"
                           placeholder="123 Đường ABC, Quận X, TP.HCM">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Số điện thoại</label>
                    <input type="text" name="phone" value="{{ old('phone', $branch?->phone) }}"
                           class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400"
                           placeholder="028.xxxx.xxxx">
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="submit" class="bg-indigo-600 text-white px-5 py-2 text-sm hover:bg-indigo-700 transition-colors">
                        {{ $branch ? 'Cập nhật' : 'Tạo Chi nhánh' }}
                    </button>
                    <a href="{{ route('admin.branches') }}" class="px-5 py-2 text-sm border border-gray-200 hover:bg-gray-50 transition-colors">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
