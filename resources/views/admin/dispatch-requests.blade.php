<x-app-layout>
    <x-slot name="heading">Phê duyệt Điều phối</x-slot>
    <x-slot name="subheading">Yêu cầu chờ phê duyệt · Quyền Super Admin</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.assignments') }}" class="text-xs text-gray-500 hover:text-indigo-600 border border-gray-200 px-3 py-1.5 hover:border-gray-300 transition-colors">
            📋 Tất cả Assignments
        </a>
    </x-slot>

    <div class="p-8 space-y-8">
        @if(session('success'))
        <div class="px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm">{{ session('success') }}</div>
        @endif

        {{-- Pending Requests --}}
        <div class="bg-white border border-gray-100">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-900">⏳ Chờ phê duyệt</h2>
                <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5">{{ $pending->count() }} yêu cầu</span>
            </div>

            @if($pending->isEmpty())
            <div class="px-6 py-10 text-center text-sm text-gray-400">Không có yêu cầu nào đang chờ duyệt.</div>
            @else
            <div class="divide-y divide-gray-50">
                @foreach($pending as $req)
                <div class="px-6 py-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 space-y-1">
                            <p class="text-sm font-medium text-gray-900">
                                {{ $req->requester->name ?? '?' }}
                                <span class="text-gray-400 font-normal">muốn điều phối</span>
                                <span class="text-indigo-700">{{ $req->user->name ?? '?' }}</span>
                                <span class="text-gray-400 font-normal">sang</span>
                                <span class="text-gray-900">{{ $req->targetTenant->name ?? '?' }}</span>
                                @if($req->targetBranch)
                                · <span class="text-gray-500">{{ $req->targetBranch->name }}</span>
                                @endif
                            </p>
                            @if($req->note)
                            <p class="text-xs text-gray-400 italic">"{{ $req->note }}"</p>
                            @endif
                            <p class="text-xs text-gray-300">{{ $req->created_at->format('d/m/Y H:i') }} · ID #{{ $req->id }}</p>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            {{-- Approve --}}
                            <form method="POST" action="{{ route('admin.dispatch-requests.approve', $req) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="text-xs px-3 py-1.5 bg-green-600 text-white hover:bg-green-700 transition-colors font-medium">
                                    ✅ Phê duyệt
                                </button>
                            </form>

                            {{-- Reject with note --}}
                            <form method="POST" action="{{ route('admin.dispatch-requests.reject', $req) }}"
                                  onsubmit="this.querySelector('[name=review_note]').value = prompt('Lý do từ chối:') || 'Không phê duyệt.'; return true;">
                                @csrf @method('PATCH')
                                <input type="hidden" name="review_note" value="">
                                <button type="submit"
                                        class="text-xs px-3 py-1.5 border border-red-200 text-red-600 hover:bg-red-50 transition-colors">
                                    ❌ Từ chối
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- History --}}
        @if($history->count())
        <div class="bg-white border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-900">📋 Lịch sử phê duyệt (50 gần nhất)</h2>
            </div>
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-400 font-medium">
                        <th class="px-6 py-3 text-left">Người đề xuất</th>
                        <th class="px-6 py-3 text-left">Nhân viên</th>
                        <th class="px-6 py-3 text-left">Trung tâm đích</th>
                        <th class="px-6 py-3 text-center">Trạng thái</th>
                        <th class="px-6 py-3 text-left">Người duyệt</th>
                        <th class="px-6 py-3 text-right">Thời gian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($history as $req)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 text-sm text-gray-700">{{ $req->requester->name ?? '—' }}</td>
                        <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $req->user->name ?? '—' }}</td>
                        <td class="px-6 py-3 text-sm text-gray-500">{{ $req->targetTenant->name ?? '—' }}</td>
                        <td class="px-6 py-3 text-center">
                            <span class="text-xs px-2 py-0.5 {{ $req->statusColor() }}">{{ $req->statusLabel() }}</span>
                        </td>
                        <td class="px-6 py-3 text-xs text-gray-400">{{ $req->reviewer->name ?? '—' }}</td>
                        <td class="px-6 py-3 text-xs text-gray-400 text-right">
                            {{ $req->reviewed_at?->format('d/m/Y H:i') ?? '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</x-app-layout>
