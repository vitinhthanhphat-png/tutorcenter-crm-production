<x-app-layout>
    <x-slot name="heading">⚙️ System Overview</x-slot>
    <x-slot name="subheading">Toàn hệ thống · {{ now()->format('d/m/Y') }}</x-slot>

    <div class="p-8 space-y-8">

        {{-- KPI Grid --}}
        <div class="grid grid-cols-4 gap-4">
            @php
            $kpis = [
                ['Tenants',      $stats['tenants'],     'indigo',  'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                ['Chi nhánh',    $stats['branches'],    'violet',  'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z'],
                ['Users',        $stats['users'],       'blue',    'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['Học sinh',     $stats['students'],    'green',   'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                ['Lớp học',      $stats['classrooms'],  'amber',   'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                ['Buổi học',     $stats['sessions'],    'cyan',    'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['Enrollments',  $stats['enrollments'], 'rose',    'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                ['Tổng D.Thu',   number_format($stats['revenue']).'đ', 'emerald', 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
            $colorMap = ['indigo'=>'text-indigo-600 bg-indigo-50','violet'=>'text-violet-600 bg-violet-50','blue'=>'text-blue-600 bg-blue-50','green'=>'text-green-600 bg-green-50','amber'=>'text-amber-600 bg-amber-50','cyan'=>'text-cyan-600 bg-cyan-50','rose'=>'text-rose-600 bg-rose-50','emerald'=>'text-emerald-600 bg-emerald-50'];
            @endphp
            @foreach($kpis as [$label, $value, $color, $icon])
            <div class="bg-white border border-gray-100 p-4 flex items-center gap-3 hover:border-gray-200 transition-colors">
                <div class="w-10 h-10 flex items-center justify-center flex-shrink-0 {{ explode(' ',$colorMap[$color])[1] }}">
                    <svg class="w-5 h-5 {{ explode(' ',$colorMap[$color])[0] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $icon }}"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400">{{ $label }}</p>
                    <p class="text-lg font-bold text-gray-900" style="font-family:'Space Grotesk',sans-serif">{{ $value }}</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Revenue Chart + Recent Users --}}
        <div class="grid grid-cols-3 gap-6">
            {{-- Chart --}}
            <div class="col-span-2 bg-white border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4" style="font-family:'Space Grotesk',sans-serif">Doanh thu 6 tháng gần nhất</h3>
                <canvas id="revenueChart" height="90"></canvas>
            </div>

            {{-- Recent Users --}}
            <div class="bg-white border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4" style="font-family:'Space Grotesk',sans-serif">User mới nhất</h3>
                <div class="space-y-3">
                    @foreach($recentUsers as $u)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gray-900 flex items-center justify-center text-white text-xs flex-shrink-0">
                            {{ strtoupper(mb_substr($u->name,0,2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $u->name }}</p>
                            <p class="text-xs text-gray-400">{{ $u->role }} · {{ $u->tenant?->name ?? 'System' }}</p>
                        </div>
                        <p class="text-xs text-gray-300 flex-shrink-0 ml-auto">{{ $u->created_at->diffForHumans() }}</p>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('admin.users') }}" class="mt-4 block text-xs text-indigo-600 hover:underline text-center">Xem tất cả →</a>
            </div>
        </div>

        {{-- Tenants Table --}}
        <div class="bg-white border border-gray-100">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-900" style="font-family:'Space Grotesk',sans-serif">Tenants</h2>
                <a href="{{ route('admin.tenants.create') }}" class="text-xs bg-indigo-600 text-white px-3 py-1.5 hover:bg-indigo-700 transition-colors">+ Thêm Tenant</a>
            </div>
            <table class="w-full">
                <thead><tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-2.5 text-left text-xs font-medium text-gray-400">Tenant</th>
                    <th class="px-6 py-2.5 text-right text-xs font-medium text-gray-400">Users</th>
                    <th class="px-6 py-2.5 text-right text-xs font-medium text-gray-400">Students</th>
                    <th class="px-6 py-2.5 text-right text-xs font-medium text-gray-400">Lớp</th>
                    <th class="px-6 py-2.5 text-right text-xs font-medium text-gray-400">Doanh thu</th>
                    <th class="px-6 py-2.5 text-center text-xs font-medium text-gray-400">Status</th>
                    <th class="px-6 py-2.5"></th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($tenants as $t)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 font-medium text-sm text-gray-900">{{ $t->name }}</td>
                        <td class="px-6 py-3 text-sm text-right">{{ $t->users_count }}</td>
                        <td class="px-6 py-3 text-sm text-right">{{ $t->students_count }}</td>
                        <td class="px-6 py-3 text-sm text-right">{{ $t->classrooms_count }}</td>
                        <td class="px-6 py-3 text-sm text-right font-medium text-emerald-600">{{ number_format($t->revenue) }}đ</td>
                        <td class="px-6 py-3 text-center">
                            <span class="text-xs px-2 py-0.5 {{ $t->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">{{ ucfirst($t->status) }}</span>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('admin.tenants.show', $t) }}" class="text-xs text-indigo-600 hover:underline mr-2">Detail</a>
                            <a href="{{ route('admin.tenants.edit', $t) }}" class="text-xs text-gray-500 hover:underline">Sửa</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script id="revenue-data" type="application/json">
    {
        "labels": {!! json_encode($monthlyRevenue->pluck('label')) !!},
        "values": {!! json_encode($monthlyRevenue->pluck('value')) !!}
    }
    </script>
    <script>
    (function () {
        var d = JSON.parse(document.getElementById('revenue-data').textContent);
        new Chart(document.getElementById('revenueChart'), {
            type: 'bar',
            data: {
                labels: d.labels,
                datasets: [{
                    label: 'Doanh thu (đ)',
                    data: d.values,
                    backgroundColor: '#6366f1',
                    borderRadius: 4,
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    y: { ticks: { callback: function(v) { return (v/1000000).toFixed(1)+'M'; } }, grid: { color:'#f3f4f6' } },
                    x: { grid: { display: false } }
                }
            }
        });
    })();
    </script>
</x-app-layout>
