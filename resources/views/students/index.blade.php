<x-app-layout>
    <x-slot name="heading">Học sinh & CRM</x-slot>
    <x-slot name="subheading">Danh sách học sinh và khách hàng tiềm năng</x-slot>
    <x-slot name="actions">
        <a href="{{ route('students.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm học sinh
        </a>
    </x-slot>

    <div class="p-8">

        @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm">{{ session('success') }}</div>
        @endif

        {{-- Filters --}}
        <form method="GET" action="{{ route('students.index') }}" class="flex items-center gap-3 mb-5">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Tìm tên, SĐT..."
                   class="border border-gray-200 px-3 py-2 text-sm w-60 focus:outline-none focus:border-gray-400">
            <select name="status" class="border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                <option value="">Tất cả trạng thái</option>
                @foreach(['lead'=>'Lead/CRM','studying'=>'Đang học','dropped'=>'Nghỉ học','reserved'=>'Bảo lưu','graduated'=>'Tốt nghiệp'] as $v=>$l)
                <option value="{{ $v }}" {{ request('status')==$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 border border-gray-300 text-sm text-gray-600 hover:bg-gray-50">Lọc</button>
            @if(request('search') || request('status'))
            <a href="{{ route('students.index') }}" class="text-xs text-red-600 hover:underline">Xóa bộ lọc</a>
            @endif
        </form>

        {{-- Table --}}
        <div class="bg-white border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Tên</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">SĐT</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Nguồn</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Trạng thái</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Chi nhánh</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Ngày tạo</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($students as $student)
                    @php
                    $statusMap = [
                        'lead'       => ['Lead',       'bg-amber-50 text-amber-700'],
                        'studying'   => ['Đang học',   'bg-green-50 text-green-700'],
                        'dropped'    => ['Nghỉ học',   'bg-gray-100 text-gray-500'],
                        'reserved'   => ['Bảo lưu',    'bg-blue-50 text-blue-700'],
                        'graduated'  => ['Tốt nghiệp', 'bg-purple-50 text-purple-700'],
                    ];
                    $s = $statusMap[$student->status] ?? ['?', 'bg-gray-100 text-gray-500'];
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 bg-gray-100 flex items-center justify-center text-xs font-semibold text-gray-600 flex-shrink-0">
                                    {{ mb_substr($student->name, 0, 1) }}
                                </div>
                                <p class="text-sm font-medium text-gray-900">{{ $student->name }}</p>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-sm text-gray-600">{{ $student->phone ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-sm text-gray-400">{{ ucfirst($student->lead_source ?? '—') }}</td>
                        <td class="px-5 py-3.5">
                            <span class="px-2 py-0.5 text-xs font-medium {{ $s[1] }}">{{ $s[0] }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-sm text-gray-500">{{ $student->branch?->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-xs text-gray-400">{{ $student->created_at->format('d/m/Y') }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('students.edit', $student) }}" class="text-gray-400 hover:text-gray-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <a href="{{ route('students.show', $student) }}" class="text-gray-400 hover:text-blue-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-gray-400 text-sm">
                            Chưa có học sinh. <a href="{{ route('students.create') }}" class="text-red-600 hover:underline">Thêm ngay</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $students->links() }}</div>
    </div>
</x-app-layout>
