<x-app-layout>
    <x-slot name="heading">Quản lý Tenants</x-slot>
    <x-slot name="subheading">Tổng quan các trung tâm trong hệ thống</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.tenants.create') }}" class="text-xs bg-indigo-600 text-white px-4 py-2 hover:bg-indigo-700 transition-colors">+ Thêm Tenant</a>
    </x-slot>

    <div class="p-8">
        @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-600 text-sm">{{ session('error') }}</div>
        @endif

        {{-- Prominent create action --}}
        <div class="flex items-center justify-between mb-5">
            <p class="text-sm text-gray-500">{{ $tenants->count() }} tenant trong hệ thống</p>
            <a href="{{ route('admin.tenants.create') }}"
               class="flex items-center gap-2 bg-indigo-600 text-white px-5 py-2.5 text-sm hover:bg-indigo-700 transition-colors font-medium shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Thêm Tenant mới
            </a>
        </div>

        {{-- Table --}}
        <div class="bg-white border border-gray-100">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">Tên Tenant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">Domain</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-400">Users</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-400">Học sinh</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-400">Lớp</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-400">Chi nhánh</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-400">Trạng thái</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-400">Tạo lúc</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-400 min-w-[220px]">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($tenants as $t)
                    <tr class="hover:bg-gray-50 transition-colors {{ $t->status === 'suspended' ? 'opacity-60' : '' }}">
                        <td class="px-6 py-3 font-medium text-sm text-gray-900">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                    {{ mb_strtoupper(mb_substr($t->name, 0, 2)) }}
                                </div>
                                <a href="{{ route('admin.tenants.show', $t) }}" class="hover:text-indigo-600 transition-colors">
                                    {{ $t->name }}
                                </a>
                            </div>
                        </td>
                        <td class="px-6 py-3 text-xs text-gray-400 font-mono">{{ $t->domain }}</td>
                        <td class="px-6 py-3 text-sm text-right">{{ $t->users_count }}</td>
                        <td class="px-6 py-3 text-sm text-right">{{ $t->students_count }}</td>
                        <td class="px-6 py-3 text-sm text-right">{{ $t->classrooms_count }}</td>
                        <td class="px-6 py-3 text-sm text-right">{{ $t->branches_count }}</td>
                        <td class="px-6 py-3 text-center">
                            <span class="text-xs px-2 py-0.5 {{ $t->status === 'active' ? 'bg-green-50 text-green-700' : ($t->status === 'suspended' ? 'bg-red-50 text-red-600' : 'bg-gray-100 text-gray-400') }}">
                                {{ ['active' => 'Hoạt động', 'inactive' => 'Không HĐ', 'suspended' => 'Tạm khóa'][$t->status] ?? $t->status }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-xs text-gray-400 text-right">{{ $t->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-3">
                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                <a href="{{ route('admin.tenants.show', $t) }}"
                                   class="text-xs px-2.5 py-1 border border-indigo-200 text-indigo-600 hover:bg-indigo-50 transition-colors">
                                    🔍 Detail
                                </a>
                                <a href="{{ route('admin.tenants.edit', $t) }}"
                                   class="text-xs px-2.5 py-1 border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
                                    ✏️ Sửa
                                </a>
                                <form method="POST" action="{{ route('admin.tenants.toggle', $t) }}">
                                    @csrf @method('PATCH')
                                    <button class="text-xs px-2.5 py-1 border transition-colors
                                        {{ $t->status === 'active'
                                            ? 'border-orange-200 text-orange-600 hover:bg-orange-50'
                                            : 'border-green-200 text-green-600 hover:bg-green-50' }}">
                                        {{ $t->status === 'active' ? '⏸ Khóa' : '▶ Mở' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.tenants.destroy', $t) }}"
                                      onsubmit="return confirm('⚠️ Xóa vĩnh viễn tenant \'{{ addslashes($t->name) }}\'?\nToàn bộ dữ liệu của tenant sẽ bị xóa!')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs px-2.5 py-1 border border-red-200 text-red-500 hover:bg-red-50 transition-colors">
                                        🗑 Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 bg-gray-100 flex items-center justify-center text-2xl">🏢</div>
                                <p class="text-sm text-gray-400">Chưa có tenant nào.</p>
                                <a href="{{ route('admin.tenants.create') }}" class="text-xs text-indigo-600 hover:underline">Tạo tenant đầu tiên →</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Quick stats --}}
        <div class="mt-4 flex gap-6 text-xs text-gray-400">
            <span>✅ Hoạt động: {{ $tenants->where('status','active')->count() }}</span>
            <span>⏸ Tạm khóa: {{ $tenants->where('status','suspended')->count() }}</span>
            <span>⊘ Không HĐ: {{ $tenants->where('status','inactive')->count() }}</span>
        </div>
    </div>
</x-app-layout>
