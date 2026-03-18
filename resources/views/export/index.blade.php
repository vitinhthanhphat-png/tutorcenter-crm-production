<x-app-layout>
    <x-slot name="heading">Xuất dữ liệu</x-slot>
    <x-slot name="subheading">Tải xuống báo cáo và dữ liệu dưới dạng CSV (mở được bằng Excel)</x-slot>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl">

            {{-- Students --}}
            <div class="bg-white border border-gray-100 p-5 space-y-3">
                <div class="flex items-center gap-2">
                    <span class="text-xl">👥</span>
                    <h3 class="font-semibold text-gray-800">Danh sách Học sinh</h3>
                </div>
                <p class="text-xs text-gray-400">Xuất danh sách học sinh kèm thông tin liên lạc, chi nhánh, trạng thái.</p>
                <form method="GET" action="{{ route('export.students') }}" class="space-y-2">
                    <select name="status" class="w-full border border-gray-200 px-2 py-1.5 text-xs">
                        <option value="">Tất cả trạng thái</option>
                        <option value="studying">Đang học</option>
                        <option value="lead">Lead</option>
                        <option value="inactive">Nghỉ học</option>
                    </select>
                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 text-xs hover:bg-indigo-700 transition-colors">
                        ↓ Tải xuống CSV
                    </button>
                </form>
            </div>

            {{-- Cashbook --}}
            <div class="bg-white border border-gray-100 p-5 space-y-3">
                <div class="flex items-center gap-2">
                    <span class="text-xl">💰</span>
                    <h3 class="font-semibold text-gray-800">Sổ Thu Chi</h3>
                </div>
                <p class="text-xs text-gray-400">Xuất toàn bộ giao dịch thu/chi của tháng chọn.</p>
                <form method="GET" action="{{ route('export.cashbook') }}" class="space-y-2">
                    <div class="grid grid-cols-2 gap-2">
                        <input type="month" name="month" value="{{ date('Y-m') }}"
                               class="border border-gray-200 px-2 py-1.5 text-xs">
                        <select name="type" class="border border-gray-200 px-2 py-1.5 text-xs">
                            <option value="">Tất cả loại</option>
                            <option value="income">Thu</option>
                            <option value="expense">Chi</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-green-600 text-white py-2 text-xs hover:bg-green-700 transition-colors">
                        ↓ Tải xuống CSV
                    </button>
                </form>
            </div>

            {{-- Payroll --}}
            <div class="bg-white border border-gray-100 p-5 space-y-3">
                <div class="flex items-center gap-2">
                    <span class="text-xl">📋</span>
                    <h3 class="font-semibold text-gray-800">Bảng Lương</h3>
                </div>
                <p class="text-xs text-gray-400">Xuất bảng lương tất cả giáo viên trong tháng.</p>
                <form method="GET" action="{{ route('export.payroll') }}" class="space-y-2">
                    <input type="month" name="month" value="{{ date('Y-m') }}"
                           class="w-full border border-gray-200 px-2 py-1.5 text-xs">
                    <button type="submit" class="w-full bg-yellow-500 text-white py-2 text-xs hover:bg-yellow-600 transition-colors">
                        ↓ Tải xuống CSV
                    </button>
                </form>
            </div>

            {{-- Attendance --}}
            <div class="bg-white border border-gray-100 p-5 space-y-3">
                <div class="flex items-center gap-2">
                    <span class="text-xl">✅</span>
                    <h3 class="font-semibold text-gray-800">Điểm danh</h3>
                </div>
                <p class="text-xs text-gray-400">Xuất toàn bộ dữ liệu điểm danh học sinh theo tháng.</p>
                <form method="GET" action="{{ route('export.attendance') }}" class="space-y-2">
                    <input type="month" name="month" value="{{ date('Y-m') }}"
                           class="w-full border border-gray-200 px-2 py-1.5 text-xs">
                    <button type="submit" class="w-full bg-red-600 text-white py-2 text-xs hover:bg-red-700 transition-colors">
                        ↓ Tải xuống CSV
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
