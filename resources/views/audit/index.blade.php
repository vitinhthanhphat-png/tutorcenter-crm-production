<x-app-layout>
    <x-slot name="heading">Nhật ký hệ thống</x-slot>
    <x-slot name="subheading">Toàn bộ hoạt động trong hệ thống</x-slot>

    <div class="p-6 space-y-4">

        {{-- Filter --}}
        <form method="GET" class="flex gap-2 items-center">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm mô tả..."
                   class="border border-gray-200 px-3 py-1.5 text-sm w-52 focus:outline-none focus:border-indigo-400">
            <select name="event" class="border border-gray-200 px-3 py-1.5 text-sm focus:outline-none">
                <option value="">Tất cả sự kiện</option>
                @foreach($events as $e)
                <option value="{{ $e }}" {{ request('event') === $e ? 'selected' : '' }}>{{ $e }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-gray-100 border border-gray-200 px-4 py-1.5 text-sm hover:bg-gray-200">Lọc</button>
            @if(request('q') || request('event'))
            <a href="{{ route('admin.audit') }}" class="text-xs text-gray-400 hover:text-gray-600">× Xóa lọc</a>
            @endif
        </form>

        <div class="bg-white border border-gray-100 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100 text-xs text-gray-400 uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-left">Thời gian</th>
                        <th class="px-4 py-3 text-left">Người thực hiện</th>
                        <th class="px-4 py-3 text-left">Sự kiện</th>
                        <th class="px-4 py-3 text-left">Đối tượng</th>
                        <th class="px-4 py-3 text-left">Mô tả</th>
                        <th class="px-4 py-3 text-left">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2.5 text-gray-400 text-xs whitespace-nowrap">
                            {{ $log->created_at?->format('d/m H:i') }}
                        </td>
                        <td class="px-4 py-2.5 text-gray-700 text-xs">
                            {{ $log->user?->name ?? 'System' }}
                        </td>
                        <td class="px-4 py-2.5">
                            <span class="text-xs px-2 py-0.5 font-mono
                                @match($log->event)
                                    @case('deleted') bg-red-50 text-red-600 @break
                                    @case('created') bg-green-50 text-green-700 @break
                                    @case('updated') bg-blue-50 text-blue-600 @break
                                    @case('transfer') bg-yellow-50 text-yellow-700 @break
                                    @default bg-gray-50 text-gray-500
                                @endmatch
                            ">{{ $log->event }}</span>
                        </td>
                        <td class="px-4 py-2.5 text-gray-500 text-xs">
                            @if($log->auditable_type)
                            {{ class_basename($log->auditable_type) }}
                            @if($log->auditable_id) #{{ $log->auditable_id }} @endif
                            @else —
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-gray-700 text-xs max-w-xs truncate">
                            {{ $log->description ?: '—' }}
                        </td>
                        <td class="px-4 py-2.5 text-gray-400 text-xs font-mono">
                            {{ $log->ip_address ?? '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-300">Chưa có log nào</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $logs->links() }}
    </div>
</x-app-layout>
