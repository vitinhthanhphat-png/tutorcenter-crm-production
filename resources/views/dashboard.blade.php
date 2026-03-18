<x-app-layout>
    <x-slot name="heading">Dashboard</x-slot>
    <x-slot name="subheading">Tổng quan hoạt động hôm nay</x-slot>

    <div class="p-8 space-y-8">

        {{-- ===== KPI Cards ===== --}}
        <div class="grid grid-cols-4 gap-5">
            @php
            $cards = [
                ['label' => 'Học sinh', 'value' => number_format($stats['totalStudents']),  'sub' => 'Đang học', 'color' => 'text-blue-600', 'bg' => 'bg-blue-50'],
                ['label' => 'Lớp mở',  'value' => number_format($stats['activeClasses']),   'sub' => 'Đang hoạt động', 'color' => 'text-green-600', 'bg' => 'bg-green-50'],
                ['label' => 'Doanh thu', 'value' => number_format($stats['monthlyRevenue']), 'sub' => 'Tháng này (VNĐ)', 'color' => 'text-red-600', 'bg' => 'bg-red-50'],
                ['label' => 'Leads mới', 'value' => number_format($stats['newLeads']),       'sub' => 'Chờ tư vấn', 'color' => 'text-amber-600', 'bg' => 'bg-amber-50'],
            ];
            @endphp
            @foreach($cards as $card)
            <div class="bg-white border border-gray-100 p-5 hover:shadow-sm transition-shadow">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">{{ $card['label'] }}</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1" style="font-family:'Space Grotesk',sans-serif">{{ $card['value'] }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $card['sub'] }}</p>
                    </div>
                    <div class="w-9 h-9 {{ $card['bg'] }} flex items-center justify-center flex-shrink-0">
                        <div class="w-2.5 h-2.5 {{ $card['color'] }} bg-current"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="grid grid-cols-5 gap-5">

            {{-- ===== Lịch học hôm nay ===== --}}
            <div class="col-span-3 bg-white border border-gray-100">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-900" style="font-family:'Space Grotesk',sans-serif">Lịch học hôm nay</h2>
                    <span class="text-xs text-gray-400">{{ \Carbon\Carbon::today()->format('l, d/m/Y') }}</span>
                </div>
                @if($todayClasses->isEmpty())
                    <div class="px-5 py-10 text-center text-gray-400 text-sm">Không có lớp học hôm nay.</div>
                @else
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-50">
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">Lớp</th>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">Giờ</th>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">Giáo viên</th>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">Phòng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($todayClasses as $class)
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3">
                                <p class="text-sm font-medium text-gray-900">{{ $class->name }}</p>
                                <p class="text-xs text-gray-400">{{ $class->course?->name }}</p>
                            </td>
                            <td class="px-5 py-3 text-sm text-gray-600">
                                {{ $class->schedule_rule['start_time'] ?? '—' }} – {{ $class->schedule_rule['end_time'] ?? '—' }}
                            </td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $class->teacher?->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $class->room_name ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>

            {{-- ===== Leads mới ===== --}}
            <div class="col-span-2 bg-white border border-gray-100">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-900" style="font-family:'Space Grotesk',sans-serif">Leads mới nhất</h2>
                    <a href="{{ route('students.index') }}" class="text-xs text-red-600 hover:underline">Xem tất cả</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($recentLeads as $lead)
                    <div class="flex items-center gap-3 px-5 py-3">
                        <div class="w-7 h-7 bg-gray-100 flex-shrink-0 flex items-center justify-center text-xs font-semibold text-gray-600">
                            {{ strtoupper(mb_substr($lead->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $lead->name }}</p>
                            <p class="text-xs text-gray-400">{{ $lead->lead_source ?? 'Chưa rõ nguồn' }}</p>
                        </div>
                        <span class="text-xs px-2 py-0.5 bg-amber-50 text-amber-600 font-medium">
                            {{ ucfirst($lead->lead_status ?? 'new') }}
                        </span>
                    </div>
                    @empty
                    <div class="px-5 py-8 text-center text-gray-400 text-sm">Chưa có lead mới.</div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ===== Analytics Row ===== --}}
        <div class="grid grid-cols-3 gap-5">

            {{-- Attendance Rate --}}
            <div class="bg-white border border-gray-100 p-5">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Tỷ lệ đi học (tháng này)</p>
                <p class="text-3xl font-bold text-gray-900 mt-2" style="font-family:'Space Grotesk',sans-serif">
                    {{ $attendanceRate }}%
                </p>
                <div class="mt-3 flex gap-3 text-xs text-gray-400">
                    <span class="text-green-600">✓ {{ $attendanceStats->present ?? 0 }} có mặt</span>
                    <span class="text-red-500">✗ {{ $attendanceStats->absent ?? 0 }} vắng</span>
                </div>
                <div class="mt-3 h-1.5 bg-gray-100">
                    <div class="h-1.5 bg-green-500 transition-all" id="attendanceBar"
                         data-rate="{{ $attendanceRate }}"></div>
                </div>
                <script>
                document.getElementById('attendanceBar').style.width =
                    (document.getElementById('attendanceBar').dataset.rate || 0) + '%';
                </script>
            </div>

            {{-- Dropout this month --}}
            <div class="bg-white border border-gray-100 p-5">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Nghỉ học tháng này</p>
                <p class="text-3xl font-bold {{ $droppedThisMonth > 0 ? 'text-red-500' : 'text-gray-300' }} mt-2" style="font-family:'Space Grotesk',sans-serif">
                    {{ $droppedThisMonth }}
                </p>
                <p class="text-xs text-gray-400 mt-1">học sinh ngừng học</p>
                @if($droppedThisMonth > 0)
                <p class="text-xs text-red-500 mt-3">⚠ Xem lại nguyên nhân nghỉ học</p>
                @else
                <p class="text-xs text-green-600 mt-3">✓ Không có học sinh nghỉ học</p>
                @endif
            </div>

            {{-- Overdue Invoices --}}
            <div class="bg-white border border-gray-100">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-900">Học phí quá hạn</h2>
                    <a href="{{ route('finance.invoices') }}" class="text-xs text-red-500 hover:underline">Xem tất cả →</a>
                </div>
                @forelse($overdueInvoices as $inv)
                <div class="flex items-center justify-between px-5 py-2.5 border-b border-gray-50 last:border-0">
                    <div>
                        <p class="text-xs font-medium text-gray-800">{{ $inv->student->name ?? '—' }}</p>
                        <p class="text-xs text-gray-400">Hạn: {{ $inv->due_date ? \Carbon\Carbon::parse($inv->due_date)->format('d/m') : '—' }}</p>
                    </div>
                    <span class="text-xs font-semibold text-red-600">{{ number_format($inv->amount) }}đ</span>
                </div>
                @empty
                <p class="px-5 py-6 text-sm text-gray-400 text-center">✅ Không có học phí quá hạn.</p>
                @endforelse
            </div>
        </div>

        {{-- ===== Revenue Trend (6 months) ===== --}}
        @if(!empty($revenueTrend))
        <div class="bg-white border border-gray-100 p-5">
            <h2 class="text-sm font-semibold text-gray-900 mb-4">Xu hướng Doanh thu (6 tháng)</h2>
            <canvas id="revenueTrendChart" height="60"
                    data-labels="{{ htmlspecialchars(json_encode(array_keys($revenueTrend)), ENT_QUOTES) }}"
                    data-values="{{ htmlspecialchars(json_encode(array_values($revenueTrend)), ENT_QUOTES) }}"></canvas>
        </div>
        @endif

    </div>

    @if(!empty($revenueTrend))
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
    (function() {
        const el = document.getElementById('revenueTrendChart');
        const trendLabels = JSON.parse(el.dataset.labels || '[]');
        const trendData   = JSON.parse(el.dataset.values || '[]');
        new Chart(el, {
            type: 'bar',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: trendData,
                    backgroundColor: 'rgba(239,68,68,0.15)',
                    borderColor: 'rgb(239,68,68)',
                    borderWidth: 1.5,
                    borderRadius: 2,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 } } },
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                }
            }
        });
    })();
    </script>
    @endpush
    @endif
</x-app-layout>
