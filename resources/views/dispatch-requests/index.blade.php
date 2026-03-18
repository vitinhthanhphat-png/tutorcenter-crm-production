<x-app-layout>
    <x-slot name="heading">Yêu cầu điều phối</x-slot>
    <x-slot name="subheading">Yêu cầu bạn đã tạo · Chờ Super Admin phê duyệt</x-slot>
    <x-slot name="actions">
        <a href="{{ route('dispatch-requests.create') }}"
           class="flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 text-sm hover:bg-indigo-700 transition-colors font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tạo yêu cầu mới
        </a>
    </x-slot>

    <div class="p-8 space-y-6">
        @if(session('success'))
        <div class="px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm">{{ session('success') }}</div>
        @endif

        @if($requests->isEmpty())
        <div class="bg-white border border-gray-100 p-12 text-center">
            <p class="text-gray-400 text-sm mb-4">Bạn chưa có yêu cầu điều phối nào.</p>
            <a href="{{ route('dispatch-requests.create') }}" class="text-indigo-600 text-sm hover:underline">
                + Tạo yêu cầu điều phối đầu tiên
            </a>
        </div>
        @else
        <div class="bg-white border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-900">Danh sách yêu cầu</h2>
            </div>
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-400 font-medium">
                        <th class="px-6 py-3 text-left">Nhân viên</th>
                        <th class="px-6 py-3 text-left">Trung tâm đích</th>
                        <th class="px-6 py-3 text-left">Chi nhánh đích</th>
                        <th class="px-6 py-3 text-center">Trạng thái</th>
                        <th class="px-6 py-3 text-right">Ngày tạo</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($requests as $req)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 text-sm font-medium text-gray-900">
                            {{ $req->user->name ?? '—' }}
                            <span class="text-xs text-gray-400 font-normal block">{{ $req->user->role ?? '' }}</span>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $req->targetTenant->name ?? '—' }}</td>
                        <td class="px-6 py-3 text-sm text-gray-400">{{ $req->targetBranch->name ?? 'Tất cả chi nhánh' }}</td>
                        <td class="px-6 py-3 text-center">
                            <span class="text-xs px-2 py-0.5 {{ $req->statusColor() }}">
                                {{ $req->statusLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-xs text-gray-400 text-right">
                            {{ $req->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-3 text-right">
                            @if($req->isPending())
                            <form method="POST" action="{{ route('dispatch-requests.cancel', $req) }}"
                                  onsubmit="return confirm('Hủy yêu cầu này?')">
                                @csrf @method('PATCH')
                                <button class="text-xs text-red-500 hover:underline">Hủy</button>
                            </form>
                            @elseif($req->review_note)
                            <span class="text-xs text-gray-400 italic">{{ $req->review_note }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-3 border-t border-gray-100">
                {{ $requests->links() }}
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
