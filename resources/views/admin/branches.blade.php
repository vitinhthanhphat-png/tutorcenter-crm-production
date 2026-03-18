<x-app-layout>
    <x-slot name="heading">Quản lý Chi nhánh</x-slot>
    <x-slot name="subheading">{{ $branches->total() }} chi nhánh trên {{ \App\Models\Tenant::count() }} tenant</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.branches.create') }}" class="text-xs bg-indigo-600 text-white px-4 py-2 hover:bg-indigo-700 transition-colors">+ Thêm Chi nhánh</a>
    </x-slot>

    <div class="p-8 space-y-4">
        @if(session('success'))
        <div class="px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm">{{ session('success') }}</div>
        @endif

        {{-- Filter --}}
        <form method="GET" class="flex gap-3 items-center">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm tên chi nhánh..."
                   class="border border-gray-200 px-3 py-2 text-sm w-60 focus:outline-none focus:border-indigo-400">
            <select name="tenant_id" class="border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                <option value="">Tất cả Tenant</option>
                @foreach($tenants as $t)
                <option value="{{ $t->id }}" {{ request('tenant_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-gray-900 text-white px-4 py-2 text-sm hover:bg-gray-700 transition-colors">Lọc</button>
            <a href="{{ route('admin.branches') }}" class="text-sm text-gray-400 hover:text-gray-600">Reset</a>
        </form>

        {{-- Table --}}
        <div class="bg-white border border-gray-100">
            <table class="w-full">
                <thead><tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-2.5 text-left text-xs font-medium text-gray-400">Tên Chi nhánh</th>
                    <th class="px-6 py-2.5 text-left text-xs font-medium text-gray-400">Tenant</th>
                    <th class="px-6 py-2.5 text-left text-xs font-medium text-gray-400">Địa chỉ</th>
                    <th class="px-6 py-2.5 text-left text-xs font-medium text-gray-400">SĐT</th>
                    <th class="px-6 py-2.5 text-right text-xs font-medium text-gray-400">Tạo lúc</th>
                    <th class="px-6 py-2.5"></th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($branches as $b)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 font-medium text-sm text-gray-900">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-violet-100 text-violet-700 flex items-center justify-center text-xs font-bold">
                                    {{ mb_strtoupper(mb_substr($b->name,0,2)) }}
                                </div>
                                {{ $b->name }}
                            </div>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-500">{{ $b->tenant?->name }}</td>
                        <td class="px-6 py-3 text-sm text-gray-400">{{ $b->address ?? '—' }}</td>
                        <td class="px-6 py-3 text-sm text-gray-400">{{ $b->phone ?? '—' }}</td>
                        <td class="px-6 py-3 text-xs text-gray-400 text-right">{{ $b->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.branches.edit', $b) }}" class="text-xs text-indigo-600 hover:underline">Sửa</a>
                                <form method="POST" action="{{ route('admin.branches.destroy', $b) }}" onsubmit="return confirm('Xóa chi nhánh này?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-red-500 hover:underline">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-sm text-gray-400">Chưa có chi nhánh nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-100">{{ $branches->links() }}</div>
        </div>
    </div>
</x-app-layout>
