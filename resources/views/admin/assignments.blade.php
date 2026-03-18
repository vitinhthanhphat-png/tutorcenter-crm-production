<x-app-layout>
    <x-slot name="heading">Staff Assignments</x-slot>
    <x-slot name="subheading">Tất cả nhân viên đang được điều phối đa trung tâm</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.dispatch-requests') }}" class="text-xs text-gray-500 hover:text-indigo-600 border border-gray-200 px-3 py-1.5 hover:border-gray-300 transition-colors">
            ⏳ Yêu cầu chờ duyệt
        </a>
    </x-slot>

    <div class="p-8">
        @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm">{{ session('success') }}</div>
        @endif

        @if($assignments->isEmpty())
        <div class="bg-white border border-gray-100 p-12 text-center">
            <p class="text-gray-400 text-sm">Chưa có nhân viên nào được điều phối đa trung tâm.</p>
            <p class="text-gray-300 text-xs mt-2">Assignments sẽ xuất hiện khi Super Admin phê duyệt yêu cầu điều phối.</p>
        </div>
        @else
        <div class="bg-white border border-gray-100">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-400 font-medium">
                        <th class="px-6 py-3 text-left">Nhân viên</th>
                        <th class="px-6 py-3 text-left">Home Tenant</th>
                        <th class="px-6 py-3 text-left">Được giao tới</th>
                        <th class="px-6 py-3 text-left">Chi nhánh</th>
                        <th class="px-6 py-3 text-left">Role đặc biệt</th>
                        <th class="px-6 py-3 text-center">Trạng thái</th>
                        <th class="px-6 py-3 text-left">Người giao</th>
                        <th class="px-6 py-3 text-right">Hết hạn</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($assignments as $a)
                    <tr class="hover:bg-gray-50 transition-colors {{ $a->status === 'suspended' ? 'opacity-60' : '' }}">
                        <td class="px-6 py-3 text-sm font-medium text-gray-900">
                            {{ $a->user->name ?? '—' }}
                            <span class="block text-xs text-gray-400">{{ $a->user->role ?? '' }}</span>
                        </td>
                        <td class="px-6 py-3 text-xs text-gray-500">{{ $a->user->tenant->name ?? '—' }}</td>
                        <td class="px-6 py-3 text-sm text-indigo-700 font-medium">{{ $a->tenant->name ?? '—' }}</td>
                        <td class="px-6 py-3 text-xs text-gray-500">{{ $a->branch->name ?? 'Tất cả CN' }}</td>
                        <td class="px-6 py-3 text-xs text-gray-400">{{ $a->role_override ?? '—' }}</td>
                        <td class="px-6 py-3 text-center">
                            @if($a->status === 'active')
                            <span class="text-xs px-2 py-0.5 bg-green-50 text-green-700">● Hoạt động</span>
                            @else
                            <span class="text-xs px-2 py-0.5 bg-amber-50 text-amber-700">⏸ Tạm khóa</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-xs text-gray-400">{{ $a->assignedBy->name ?? '—' }}</td>
                        <td class="px-6 py-3 text-xs text-gray-400 text-right">
                            {{ $a->ends_at?->format('d/m/Y') ?? '♾ Vĩnh viễn' }}
                        </td>
                        <td class="px-6 py-3 text-right">
                            <form method="POST" action="{{ route('admin.assignments.revoke', $a) }}"
                                  data-name="{{ $a->user->name ?? '' }}"
                                  onsubmit="return confirm('Thu hồi quyền truy cập của ' + this.dataset.name + '?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-500 hover:underline">Thu hồi</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-3 border-t border-gray-100">
                {{ $assignments->links() }}
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
