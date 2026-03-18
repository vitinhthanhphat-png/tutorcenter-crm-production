<x-app-layout>
    <x-slot name="heading">Tạo Yêu cầu Điều phối</x-slot>
    <x-slot name="subheading">Điều phối nhân viên sang trung tâm / chi nhánh khác</x-slot>

    <div class="p-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-indigo-50 border border-indigo-100 px-4 py-3 text-sm text-indigo-700 mb-6">
                💡 <strong>Lưu ý:</strong>
                Điều phối <strong>cùng trung tâm</strong> → tự động phê duyệt ngay.<br>
                Điều phối <strong>sang trung tâm khác</strong> → gửi yêu cầu, Super Admin sẽ phê duyệt.
            </div>

            <form method="POST" action="{{ route('dispatch-requests.store') }}" class="space-y-5">
                @csrf

                {{-- Staff member --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Nhân viên cần điều phối *</label>
                    <select name="user_id" required
                            class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400 bg-white">
                        <option value="">— Chọn nhân viên —</option>
                        @foreach($staff as $s)
                        <option value="{{ $s->id }}" {{ old('user_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }} · {{ $s->role }} · {{ $s->branch->name ?? 'Chưa có CN' }}
                        </option>
                        @endforeach
                    </select>
                    @error('user_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Target tenant --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Trung tâm đích *</label>
                    <select name="target_tenant_id" id="targetTenant" required
                            class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400 bg-white">
                        <option value="">— Chọn trung tâm —</option>
                        @foreach($tenants as $t)
                        <option value="{{ $t->id }}" data-tenant="{{ $t->id }}" {{ old('target_tenant_id') == $t->id ? 'selected' : '' }}>
                            {{ $t->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('target_tenant_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Target branch (optional) --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Chi nhánh đích <span class="text-gray-400">(tùy chọn)</span></label>
                    <select name="target_branch_id"
                            class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400 bg-white">
                        <option value="">— Tất cả chi nhánh —</option>
                        @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ old('target_branch_id') == $b->id ? 'selected' : '' }}>
                            {{ $b->name }} ({{ $b->tenant->name ?? '?' }})
                        </option>
                        @endforeach
                    </select>
                    @error('target_branch_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Role override --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Role tại nơi đến <span class="text-gray-400">(để trống = giữ nguyên)</span></label>
                    <select name="role_override"
                            class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400 bg-white">
                        <option value="">— Giữ nguyên role hiện tại —</option>
                        @foreach(['teacher','tutor','accountant','operations','branch_manager','center_manager'] as $r)
                        <option value="{{ $r }}" {{ old('role_override') === $r ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Note --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Ghi chú / Lý do</label>
                    <textarea name="note" rows="3"
                              class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-indigo-400 resize-none"
                              placeholder="Lý do điều phối...">{{ old('note') }}</textarea>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="bg-indigo-600 text-white px-6 py-2.5 text-sm font-medium hover:bg-indigo-700 transition-colors">
                        Gửi yêu cầu điều phối
                    </button>
                    <a href="{{ route('dispatch-requests.index') }}"
                       class="px-6 py-2.5 text-sm text-gray-500 hover:text-gray-700 border border-gray-200 hover:border-gray-300 transition-colors">
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
