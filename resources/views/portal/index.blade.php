<x-app-layout>
    <x-slot name="heading">Trang của tôi</x-slot>
    <x-slot name="subheading">Xem thời khóa biểu, điểm danh và thông báo học phí của bạn</x-slot>

    <div class="p-6 space-y-6">

        {{-- Upcoming sessions --}}
        <div class="bg-white border border-gray-100">
            <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                <span>📅</span>
                <h3 class="text-sm font-semibold text-gray-700">Lịch học sắp tới (14 ngày)</h3>
            </div>
            @if($upcoming->isEmpty())
                <p class="px-5 py-6 text-sm text-gray-400 text-center">Không có buổi học nào trong 14 ngày tới.</p>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($upcoming as $s)
                    <div class="flex items-center justify-between px-5 py-3">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $s->classroom->course->name ?? $s->classroom->name ?? '—' }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($s->date)->format('d/m/Y (l)') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">{{ substr($s->start_time, 0, 5) }} – {{ substr($s->end_time, 0, 5) }}</p>
                            <span class="inline-block text-xs px-2 py-0.5 mt-0.5
                                {{ $s->status === 'cancelled' ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-700' }}">
                                {{ $s->status === 'cancelled' ? 'Đã hủy' : 'Có lịch' }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Recent Attendance --}}
            <div class="bg-white border border-gray-100">
                <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span>✅</span>
                        <h3 class="text-sm font-semibold text-gray-700">Điểm danh gần đây</h3>
                    </div>
                    <a href="{{ route('portal.attendance') }}" class="text-xs text-red-500 hover:underline">Xem tất cả →</a>
                </div>
                @forelse($recentAttendance as $a)
                <div class="flex items-center justify-between px-5 py-2 border-b border-gray-50 last:border-0">
                    <div>
                        <p class="text-xs font-medium text-gray-700">{{ $a->session->classroom->name ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($a->session->date ?? now())->format('d/m/Y') }}</p>
                    </div>
                    @php
                        $badge = match($a->status) {
                            'present' => ['bg-green-50 text-green-700', '✓ Có mặt'],
                            'absent'  => ['bg-red-50 text-red-600', '✗ Vắng'],
                            'late'    => ['bg-yellow-50 text-yellow-700', '⏰ Muộn'],
                            'excused' => ['bg-blue-50 text-blue-600', '~ Vắng phép'],
                            default   => ['bg-gray-50 text-gray-600', $a->status],
                        };
                    @endphp
                    <span class="text-xs px-2 py-0.5 {{ $badge[0] }}">{{ $badge[1] }}</span>
                </div>
                @empty
                <p class="px-5 py-4 text-sm text-gray-400 text-center">Chưa có dữ liệu điểm danh.</p>
                @endforelse
            </div>

            {{-- Recent Invoices --}}
            <div class="bg-white border border-gray-100">
                <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span>💳</span>
                        <h3 class="text-sm font-semibold text-gray-700">Thanh toán gần đây</h3>
                    </div>
                    <a href="{{ route('portal.invoices') }}" class="text-xs text-red-500 hover:underline">Xem tất cả →</a>
                </div>
                @forelse($recentInvoices as $inv)
                <div class="flex items-center justify-between px-5 py-2 border-b border-gray-50 last:border-0">
                    <div>
                        <p class="text-xs font-medium text-gray-700">{{ $inv->notes ?? 'Học phí' }}</p>
                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($inv->transaction_date)->format('d/m/Y') }}</p>
                    </div>
                    <span class="text-sm font-semibold text-green-600">{{ number_format($inv->amount) }}đ</span>
                </div>
                @empty
                <p class="px-5 py-6 text-sm text-gray-400 text-center">Chưa có thanh toán nào.</p>
                @endforelse
            </div>
        </div>

        {{-- Current Enrollments --}}
        <div class="bg-white border border-gray-100">
            <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                <span>📚</span>
                <h3 class="text-sm font-semibold text-gray-700">Lớp đang học</h3>
            </div>
            @forelse($enrollments as $e)
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-50 last:border-0">
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $e->classroom->name ?? '—' }}</p>
                    <p class="text-xs text-gray-400">{{ $e->classroom->course->name ?? '' }}
                        @if($e->classroom->teacher)
                        · GV: {{ $e->classroom->teacher->name }}
                        @endif
                    </p>
                </div>
                <span class="text-xs px-2 py-0.5 bg-green-50 text-green-700">● Đang học</span>
            </div>
            @empty
            <p class="px-5 py-6 text-sm text-gray-400 text-center">Chưa đăng ký lớp nào.</p>
            @endforelse
        </div>

    </div>
</x-app-layout>
