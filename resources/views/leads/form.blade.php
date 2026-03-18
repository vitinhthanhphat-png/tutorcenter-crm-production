<x-app-layout>
    <x-slot name="heading">{{ $lead ? 'Sửa Lead' : 'Thêm Lead mới' }}</x-slot>
    <x-slot name="actions">
        <a href="{{ route('leads.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Danh sách Leads</a>
    </x-slot>

    <div class="p-8 max-w-lg">
        @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 text-sm">
            <ul>@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <div class="bg-white border border-gray-100 p-6 space-y-5">
            <form method="POST"
                  action="{{ $lead ? route('leads.update', $lead) : route('leads.store') }}">
                @csrf @if($lead) @method('PUT') @endif

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Họ tên <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $lead?->name) }}" required
                               class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-red-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">SĐT</label>
                        <input type="text" name="phone" value="{{ old('phone', $lead?->phone) }}"
                               class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-red-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $lead?->email) }}"
                               class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-red-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Tên phụ huynh</label>
                        <input type="text" name="parent_name" value="{{ old('parent_name', $lead?->parent_name) }}"
                               class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-red-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Nguồn</label>
                        <input type="text" name="source" value="{{ old('source', $lead?->source) }}"
                               placeholder="Facebook, Zalo, Giới thiệu..."
                               class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-red-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Trạng thái <span class="text-red-500">*</span></label>
                        <select name="status" required
                                class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-red-400">
                            @foreach($statuses as $key => $s)
                            <option value="{{ $key }}" {{ old('status', $lead?->status) === $key ? 'selected' : '' }}>
                                {{ $s['label'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Khóa học quan tâm</label>
                        <input type="text" name="interested_course" value="{{ old('interested_course', $lead?->interested_course) }}"
                               class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-red-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Chi nhánh</label>
                        <select name="branch_id" class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-red-400">
                            <option value="">— Tất cả —</option>
                            @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ old('branch_id', $lead?->branch_id) == $b->id ? 'selected' : '' }}>
                                {{ $b->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Hẹn gặp lại</label>
                        <input type="date" name="follow_up_at" value="{{ old('follow_up_at', $lead?->follow_up_at?->format('Y-m-d')) }}"
                               class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-red-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Nhân viên phụ trách</label>
                        <select name="assigned_to" class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-red-400">
                            <option value="">— Chưa gán —</option>
                            @foreach($staff as $u)
                            <option value="{{ $u->id }}" {{ old('assigned_to', $lead?->assigned_to) == $u->id ? 'selected' : '' }}>
                                {{ $u->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Ghi chú</label>
                        <textarea name="note" rows="3"
                                  class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-red-400">{{ old('note', $lead?->note) }}</textarea>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-red-600 text-white px-5 py-2 text-sm hover:bg-red-700 transition-colors">
                        {{ $lead ? 'Cập nhật' : 'Thêm Lead' }}
                    </button>
                    <a href="{{ route('leads.index') }}"
                       class="px-5 py-2 text-sm border border-gray-200 hover:bg-gray-50">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
