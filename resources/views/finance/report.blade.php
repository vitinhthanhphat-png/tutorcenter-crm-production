<x-app-layout>
    <x-slot name="heading">Báo cáo Doanh thu</x-slot>
    <x-slot name="subheading">Thống kê thu nhập {{ now()->year }} · <span class="text-gray-400 font-normal">12 tháng gần nhất</span></x-slot>
    <x-slot name="actions">
        <a href="{{ route('finance.invoices') }}" class="text-sm text-gray-500 hover:text-gray-700">← Phiếu thu</a>
    </x-slot>

    <div class="p-8 space-y-6">

        {{-- ===== KPI Cards ===== --}}
        <div class="grid grid-cols-4 gap-4">
            @php
            $cards = [
                ['Doanh thu tháng này',     number_format($stats['total_this_month']).'đ',  'text-green-600',  '#E9F7EF'],
                ['Doanh thu năm '.$year=now()->year, number_format($stats['total_revenue_ytd']).'đ', 'text-blue-600', '#EBF5FB'],
                ['Học sinh đang học',       $stats['active_students'].' học sinh',          'text-indigo-600', '#EAECF8'],
                ['Tổng công nợ',            number_format($stats['total_debt']).'đ',         'text-red-500',    '#FEF2F2'],
            ];
            @endphp
            @foreach($cards as [$label, $value, $color, $bg])
            <div class="bg-white border border-gray-100 p-5">
                <p class="text-xs text-gray-400 mb-1">{{ $label }}</p>
                <p class="text-xl font-bold {{ $color }}" style="font-family:'Space Grotesk',sans-serif">{{ $value }}</p>
            </div>
            @endforeach
        </div>

        {{-- ===== Revenue Chart ===== --}}
        <div class="bg-white border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-sm font-semibold text-gray-900" style="font-family:'Space Grotesk',sans-serif">
                        Doanh thu 12 tháng gần nhất
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">Tổng phiếu thu theo tháng (VNĐ)</p>
                </div>
            </div>
            <canvas id="revenueChart" height="100"></canvas>
        </div>

        {{-- ===== Monthly Breakdown Table ===== --}}
        <div class="bg-white border border-gray-100">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-900" style="font-family:'Space Grotesk',sans-serif">Chi tiết theo tháng</h2>
            </div>
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">Tháng</th>
                        <th class="px-5 py-2.5 text-right text-xs font-medium text-gray-400">Doanh thu</th>
                        <th class="px-5 py-2.5 text-right text-xs font-medium text-gray-400">% so với năm</th>
                        <th class="px-5 py-2.5 pr-6 text-right text-xs font-medium text-gray-400">Biểu đồ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @php $maxRev = max(collect($months)->pluck('revenue')->toArray() ?: [1]); @endphp
                    @foreach($months as $m)
                    @php
                        $pct  = $stats['total_revenue_ytd'] > 0 ? round($m['revenue'] / $stats['total_revenue_ytd'] * 100, 1) : 0;
                        $barW = $maxRev > 0 ? round($m['revenue'] / $maxRev * 100) : 0;
                        $isThisMonth = $m['label'] === now()->format('m/Y');
                    @endphp
                    <tr class="{{ $isThisMonth ? 'bg-red-50' : 'hover:bg-gray-50' }} transition-colors">
                        <td class="px-5 py-3 text-sm {{ $isThisMonth ? 'font-semibold text-red-700' : 'text-gray-700' }}">
                            {{ $m['label'] }}
                            @if($isThisMonth)<span class="ml-1 text-xs bg-red-100 text-red-600 px-1.5 py-0.5">hiện tại</span>@endif
                        </td>
                        <td class="px-5 py-3 text-sm text-right font-medium {{ $m['revenue'] > 0 ? 'text-gray-900' : 'text-gray-300' }}">
                            {{ $m['revenue'] > 0 ? number_format($m['revenue']).'đ' : '—' }}
                        </td>
                        <td class="px-5 py-3 text-sm text-right text-gray-400">
                            {{ $m['revenue'] > 0 ? $pct.'%' : '—' }}
                        </td>
                        <td class="px-5 py-3 pr-6">
                            <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-red-500 rounded-full transition-all"
                                     style="width: <?= $barW ?>%"></div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Chart.js (CDN) --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script id="chart-data" type="application/json">
    { "labels": {!! $chartLabels !!}, "revenue": {!! $chartRevenue !!} }
    </script>
    <script>
        (function () {
            var d = JSON.parse(document.getElementById('chart-data').textContent);
            var ctx = document.getElementById('revenueChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: d.labels,
                    datasets: [{
                        label: 'Doanh thu (đ)',
                        data: d.revenue,
                        backgroundColor: d.revenue.map(function(_, i, a) {
                            return i === a.length - 1 ? 'rgba(220,38,38,0.85)' : 'rgba(220,38,38,0.2)';
                        }),
                        borderColor: 'rgba(220,38,38,0.7)',
                        borderWidth: 1.5,
                        borderRadius: 3,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    return new Intl.NumberFormat('vi-VN', {
                                        style: 'currency', currency: 'VND'
                                    }).format(ctx.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(v) {
                                    return new Intl.NumberFormat('vi-VN', {
                                        notation: 'compact', compactDisplay: 'short'
                                    }).format(v) + 'đ';
                                }
                            },
                            grid: { color: 'rgba(0,0,0,0.04)' }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        })();
    </script>
    @endpush
</x-app-layout>
