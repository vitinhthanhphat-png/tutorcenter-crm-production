<x-app-layout>
    <x-slot name="heading">{{ $tenant->name }}</x-slot>
    <x-slot name="subheading">Chi tiết Tenant · {{ $tenant->domain }}</x-slot>
    <x-slot name="actions">
        <div class="flex gap-2">
            <a href="{{ route('admin.tenants.edit', $tenant) }}" class="text-xs border border-gray-200 px-4 py-2 hover:bg-gray-50 transition-colors">Sửa</a>
            <form method="POST" action="{{ route('admin.tenants.toggle', $tenant) }}">
                @csrf @method('PATCH')
                <button class="text-xs px-4 py-2 transition-colors {{ $tenant->status === 'active' ? 'bg-orange-50 text-orange-700 border border-orange-200 hover:bg-orange-100' : 'bg-green-50 text-green-700 border border-green-200 hover:bg-green-100' }}">
                    {{ $tenant->status === 'active' ? '⏸ Tạm khóa' : '▶ Kích hoạt' }}
                </button>
            </form>
            <a href="{{ route('admin.tenants') }}" class="text-xs text-gray-500 px-4 py-2 hover:text-gray-700">← Tenants</a>
        </div>
    </x-slot>

    <div class="p-8 space-y-6">
        @if(session('success'))
        <div class="px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm">{{ session('success') }}</div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-4 gap-4">
            @php
            $tstats = [
                ['Chi nhánh',   $stats['branches'],    'indigo'],
                ['Users',       $stats['users'],       'blue'],
                ['Học sinh',    $stats['students'],    'green'],
                ['Lớp học',     $stats['classes'],     'amber'],
                ['Enrollments', $stats['enrollments'], 'rose'],
                ['Doanh thu',   number_format($stats['revenue']).'đ', 'emerald'],
                ['Công nợ',     number_format($stats['debt']).'đ',    'red'],
            ];
            $cmap = ['indigo'=>'bg-indigo-50 text-indigo-600','blue'=>'bg-blue-50 text-blue-600','green'=>'bg-green-50 text-green-600','amber'=>'bg-amber-50 text-amber-600','rose'=>'bg-rose-50 text-rose-600','emerald'=>'bg-emerald-50 text-emerald-600','red'=>'bg-red-50 text-red-600'];
            @endphp
            @foreach($tstats as [$label, $val, $color])
            <div class="bg-white border border-gray-100 p-4">
                <p class="text-xs text-gray-400">{{ $label }}</p>
                <p class="text-xl font-bold {{ $cmap[$color] }} bg-transparent" style="font-family:'Space Grotesk',sans-serif">{{ $val }}</p>
            </div>
            @endforeach
        </div>

        {{-- Branches + Users side-by-side --}}
        <div class="grid grid-cols-2 gap-6">
            {{-- Branches --}}
            <div class="bg-white border border-gray-100">
                <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-900">Chi nhánh</h3>
                    <a href="{{ route('admin.branches.create') }}?tenant_id={{ $tenant->id }}" class="text-xs text-indigo-600 hover:underline">+ Thêm</a>
                </div>
                @forelse($tenant->branches as $b)
                <div class="flex items-center justify-between px-5 py-3 border-b border-gray-50 hover:bg-gray-50">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $b->name }}</p>
                        <p class="text-xs text-gray-400">{{ $b->address }}</p>
                    </div>
                    <a href="{{ route('admin.branches.edit', $b) }}" class="text-xs text-gray-400 hover:text-indigo-600">Sửa</a>
                </div>
                @empty
                <p class="px-5 py-4 text-sm text-gray-400">Chưa có chi nhánh.</p>
                @endforelse
            </div>

            {{-- Users --}}
            <div class="bg-white border border-gray-100">
                <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-900">Users</h3>
                    <a href="{{ route('admin.users.create') }}" class="text-xs text-indigo-600 hover:underline">+ Thêm</a>
                </div>
                @forelse($tenant->users as $u)
                @php $rc = ['super_admin'=>'text-purple-700 bg-purple-50','center_manager'=>'text-blue-700 bg-blue-50','accountant'=>'text-amber-700 bg-amber-50','teacher'=>'text-green-700 bg-green-50'][$u->role] ?? 'text-gray-500 bg-gray-100'; @endphp
                <div class="flex items-center gap-3 px-5 py-3 border-b border-gray-50 hover:bg-gray-50">
                    <div class="w-8 h-8 rounded-full bg-gray-900 flex items-center justify-center text-white text-xs flex-shrink-0">{{ strtoupper(mb_substr($u->name,0,2)) }}</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $u->name }}</p>
                        <p class="text-xs text-gray-400">{{ $u->email }}</p>
                    </div>
                    <span class="text-xs px-2 py-0.5 {{ $rc }}">{{ str_replace('_',' ',ucfirst($u->role)) }}</span>
                </div>
                @empty
                <p class="px-5 py-4 text-sm text-gray-400">Chưa có user.</p>
                @endforelse
            </div>
        </div>

        {{-- Classes --}}
        <div class="bg-white border border-gray-100">
            <div class="px-6 py-3 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-900">Lớp học ({{ $classes->count() }})</h3>
            </div>
            <table class="w-full">
                <thead><tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-2 text-left text-xs font-medium text-gray-400">Tên lớp</th>
                    <th class="px-6 py-2 text-left text-xs font-medium text-gray-400">Khóa học</th>
                    <th class="px-6 py-2 text-left text-xs font-medium text-gray-400">Giáo viên</th>
                    <th class="px-6 py-2 text-center text-xs font-medium text-gray-400">Trạng thái</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($classes as $c)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-2.5 text-sm font-medium text-gray-900">{{ $c->name }}</td>
                        <td class="px-6 py-2.5 text-sm text-gray-500">{{ $c->course?->name }}</td>
                        <td class="px-6 py-2.5 text-sm text-gray-500">{{ $c->teacher?->name }}</td>
                        <td class="px-6 py-2.5 text-center">
                            <span class="text-xs px-2 py-0.5 {{ $c->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-400' }}">{{ ucfirst($c->status) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-4 text-sm text-gray-400 text-center">Chưa có lớp học.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Recent students --}}
        <div class="bg-white border border-gray-100">
            <div class="px-6 py-3 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-900">Học sinh mới nhất (20)</h3>
            </div>
            <table class="w-full">
                <thead><tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-2 text-left text-xs font-medium text-gray-400">Tên</th>
                    <th class="px-6 py-2 text-left text-xs font-medium text-gray-400">SĐT</th>
                    <th class="px-6 py-2 text-center text-xs font-medium text-gray-400">Trạng thái</th>
                    <th class="px-6 py-2 text-left text-xs font-medium text-gray-400">Nguồn</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($students as $s)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-2.5 text-sm font-medium text-gray-800">{{ $s->name }}</td>
                        <td class="px-6 py-2.5 text-sm text-gray-500">{{ $s->phone }}</td>
                        <td class="px-6 py-2.5 text-center"><span class="text-xs px-2 py-0.5 {{ $s->status === 'studying' ? 'bg-green-50 text-green-700' : ($s->status === 'lead' ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-400') }}">{{ ucfirst($s->status) }}</span></td>
                        <td class="px-6 py-2.5 text-xs text-gray-400">{{ $s->lead_source }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-4 text-sm text-gray-400 text-center">Chưa có học sinh.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
