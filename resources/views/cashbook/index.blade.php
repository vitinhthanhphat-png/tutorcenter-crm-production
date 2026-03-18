<x-app-layout>
    <x-slot name="heading">Sổ Thu Chi</x-slot>
    <x-slot name="subheading">Ghi nhận thu nhập và chi phí vận hành</x-slot>

    <div class="p-6 space-y-5">

        @if(session('success'))
        <div class="px-4 py-2 bg-green-50 border border-green-200 text-green-700 text-sm">{{ session('success') }}</div>
        @endif

        {{-- KPI Summary --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white border border-gray-100 p-4">
                <p class="text-xs text-gray-400 mb-1">Thu</p>
                <p class="text-xl font-bold text-green-600">{{ number_format($income) }}đ</p>
            </div>
            <div class="bg-white border border-gray-100 p-4">
                <p class="text-xs text-gray-400 mb-1">Chi</p>
                <p class="text-xl font-bold text-red-500">{{ number_format($expense) }}đ</p>
            </div>
            <div class="bg-white border border-gray-100 p-4">
                <p class="text-xs text-gray-400 mb-1">Cân đối</p>
                <p class="text-xl font-bold {{ ($income - $expense) >= 0 ? 'text-gray-800' : 'text-red-600' }}">
                    {{ number_format($income - $expense) }}đ
                </p>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-5">

            {{-- Left: Add entry form --}}
            <div class="lg:w-72 bg-white border border-gray-100 p-5 space-y-4 self-start">
                <h3 class="text-sm font-semibold text-gray-700">Ghi nhận giao dịch mới</h3>
                <form method="POST" action="{{ route('cashbook.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Loại</label>
                        <select name="type" id="cbType" required
                                class="w-full border border-gray-200 px-2 py-1.5 text-sm focus:outline-none focus:border-red-400"
                                data-categories="{{ htmlspecialchars(json_encode($categories), ENT_QUOTES) }}"
                                onchange="updateCategories(this.value)">

                            <option value="income">💰 Thu</option>
                            <option value="expense">💸 Chi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Danh mục</label>
                        <select name="category" id="cbCategory" required
                                class="w-full border border-gray-200 px-2 py-1.5 text-sm focus:outline-none focus:border-red-400">
                            @foreach($categories['income'] as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Mô tả</label>
                        <input type="text" name="description" required
                               class="w-full border border-gray-200 px-2 py-1.5 text-sm focus:outline-none focus:border-red-400">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Số tiền (VND)</label>
                        <input type="number" name="amount" min="1" required
                               class="w-full border border-gray-200 px-2 py-1.5 text-sm focus:outline-none focus:border-red-400">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Ngày</label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                               class="w-full border border-gray-200 px-2 py-1.5 text-sm focus:outline-none focus:border-red-400">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Số tham chiếu</label>
                        <input type="text" name="reference" placeholder="Số HĐ, phiếu..."
                               class="w-full border border-gray-200 px-2 py-1.5 text-sm focus:outline-none focus:border-red-400">
                    </div>
                    <button type="submit"
                            class="w-full bg-red-600 text-white py-2 text-sm hover:bg-red-700 transition-colors">
                        Ghi nhận
                    </button>
                </form>
            </div>

            {{-- Right: Entries table --}}
            <div class="flex-1 space-y-3">
                {{-- Filter --}}
                <form method="GET" class="flex gap-2 items-center">
                    <select name="type" class="border border-gray-200 px-2 py-1.5 text-sm focus:outline-none">
                        <option value="">Tất cả loại</option>
                        <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Thu</option>
                        <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Chi</option>
                    </select>
                    <input type="month" name="month" value="{{ request('month', date('Y-m')) }}"
                           class="border border-gray-200 px-2 py-1.5 text-sm focus:outline-none">
                    <button type="submit" class="bg-gray-100 border border-gray-200 px-4 py-1.5 text-sm">Lọc</button>
                </form>

                <div class="bg-white border border-gray-100 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100 text-xs text-gray-400 uppercase tracking-wide">
                            <tr>
                                <th class="px-4 py-3 text-left">Ngày</th>
                                <th class="px-4 py-3 text-left">Loại</th>
                                <th class="px-4 py-3 text-left">Danh mục</th>
                                <th class="px-4 py-3 text-left">Mô tả</th>
                                <th class="px-4 py-3 text-right">Số tiền</th>
                                <th class="px-4 py-3 text-left">Người ghi</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($entries as $e)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2.5 text-gray-500 text-xs">{{ $e->transaction_date->format('d/m') }}</td>
                                <td class="px-4 py-2.5">
                                    <span class="text-xs px-1.5 py-0.5 {{ $e->type === 'income' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                                        {{ $e->type === 'income' ? 'Thu' : 'Chi' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-gray-500 text-xs">{{ $e->category }}</td>
                                <td class="px-4 py-2.5 text-gray-800">{{ $e->description }}</td>
                                <td class="px-4 py-2.5 text-right font-medium {{ $e->type === 'income' ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $e->type === 'expense' ? '-' : '+' }}{{ number_format($e->amount) }}
                                </td>
                                <td class="px-4 py-2.5 text-gray-400 text-xs">{{ $e->recorder?->name ?? '—' }}</td>
                                <td class="px-4 py-2.5 text-right">
                                    <form method="POST" action="{{ route('cashbook.destroy', $e) }}"
                                          onsubmit="return confirm('Xóa giao dịch này?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-600">×</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-300">Chưa có giao dịch nào</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $entries->links() }}
            </div>
        </div>
    </div>

    <script>
    const categories = JSON.parse(document.getElementById('cbType').dataset.categories || '{}');

    function updateCategories(type) {
        const sel = document.getElementById('cbCategory');
        sel.innerHTML = '';
        (categories[type] || []).forEach(c => {
            const o = document.createElement('option');
            o.value = o.textContent = c;
            sel.appendChild(o);
        });
    }
    </script>
</x-app-layout>
