<x-app-layout>
    <x-slot name="heading">CRM — Danh sách Lead</x-slot>
    <x-slot name="subheading">Quản lý khách hàng tiềm năng và pipeline chuyển đổi</x-slot>
    <x-slot name="actions">
        <a href="{{ route('leads.create') }}"
           class="bg-red-600 text-white px-4 py-1.5 text-sm hover:bg-red-700 transition-colors">+ Thêm Lead</a>
    </x-slot>

    <div class="p-6 space-y-5">

        @if(session('success'))
        <div class="px-4 py-2 bg-green-50 border border-green-200 text-green-700 text-sm">{{ session('success') }}</div>
        @endif

        {{-- Pipeline Summary --}}
        <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
            @foreach(\App\Models\Lead::statuses() as $key => $s)
            <a href="{{ route('leads.index', ['status' => $key]) }}"
               class="border border-gray-100 bg-white p-3 text-center hover:shadow-sm transition-shadow {{ request('status') === $key ? 'ring-2 ring-red-300' : '' }}">
                <div class="text-lg font-bold text-gray-800">{{ $summary[$key] ?? 0 }}</div>
                <div class="text-xs text-gray-400 mt-0.5">{{ $s['label'] }}</div>
            </a>
            @endforeach
        </div>

        {{-- Filter bar --}}
        <form method="GET" class="flex gap-2 items-center">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tên, SĐT, Email..."
                   class="border border-gray-200 px-3 py-1.5 text-sm w-56 focus:outline-none focus:border-red-400">
            <select name="status" class="border border-gray-200 px-3 py-1.5 text-sm focus:outline-none focus:border-red-400">
                <option value="">Tất cả trạng thái</option>
                @foreach(\App\Models\Lead::statuses() as $key => $s)
                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $s['label'] }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-gray-100 border border-gray-200 px-4 py-1.5 text-sm hover:bg-gray-200">Lọc</button>
            @if(request()->filled('status') || request()->filled('q'))
            <a href="{{ route('leads.index') }}" class="text-xs text-gray-400 hover:text-gray-600">× Xóa lọc</a>
            @endif
        </form>

        {{-- Table --}}
        <div class="bg-white border border-gray-100 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="text-xs text-gray-400 uppercase tracking-wide">
                        <th class="px-4 py-3 text-left">Tên</th>
                        <th class="px-4 py-3 text-left">SĐT</th>
                        <th class="px-4 py-3 text-left">Nguồn</th>
                        <th class="px-4 py-3 text-left">Khóa quan tâm</th>
                        <th class="px-4 py-3 text-left">Trạng thái</th>
                        <th class="px-4 py-3 text-left">Hẹn gặp</th>
                        <th class="px-4 py-3 text-left">Phụ trách</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($leads as $lead)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900">
                            <a href="{{ route('leads.show', $lead) }}" class="hover:text-red-600">{{ $lead->name }}</a>
                            @if($lead->converted_to_student_id)
                            <span class="ml-1 text-xs text-green-600">✓ HS</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $lead->phone ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $lead->source ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $lead->interested_course ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-xs {{ $lead->statusColor() }}">
                                {{ $lead->statusLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            @if($lead->follow_up_at)
                                <span class="{{ $lead->follow_up_at->isPast() ? 'text-red-500 font-medium' : '' }}">
                                    {{ $lead->follow_up_at->format('d/m') }}
                                </span>
                            @else — @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $lead->assignedTo?->name ?? '—' }}</td>
                        <td class="px-4 py-3 flex gap-2 items-center justify-end">
                            <a href="{{ route('leads.edit', $lead) }}" class="text-xs text-indigo-500 hover:text-indigo-700">Sửa</a>
                            @if(!$lead->converted_to_student_id && $lead->status !== 'lost')
                            <form method="POST" action="{{ route('leads.convert', $lead) }}">
                                @csrf
                                <button type="submit" class="text-xs text-green-600 hover:text-green-700"
                                        onclick="return confirm('Chuyển thành Học sinh?')">→ HS</button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('leads.destroy', $lead) }}"
                                  onsubmit="return confirm('Xóa lead này?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-600">Xóa</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-300">Chưa có lead nào</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $leads->links() }}
    </div>
</x-app-layout>
