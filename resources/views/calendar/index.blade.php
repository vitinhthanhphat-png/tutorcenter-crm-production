<x-app-layout>
    <x-slot name="heading">📅 Thời Khóa Biểu</x-slot>
    <x-slot name="subheading">Lịch dạy tổng hợp tất cả lớp học theo tuần và tháng</x-slot>

    <div class="p-6 space-y-4">

        {{-- Filter bar --}}
        <div class="flex flex-wrap gap-3 items-center bg-white border border-gray-100 px-4 py-3">
            <label class="text-xs text-gray-400">Lọc lớp:</label>
            <select id="classFilter"
                    class="border border-gray-200 px-3 py-1.5 text-sm focus:outline-none focus:border-red-400"
                    onchange="calApp.filterByClass(this.value)">
                <option value="">Tất cả lớp</option>
                @foreach($classes as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
            <div class="flex gap-2 ml-auto">
                <button onclick="calApp.changeView('dayGridMonth')"
                        class="text-xs px-3 py-1.5 border border-gray-200 hover:bg-gray-50 transition-colors" id="btnMonth">Tháng</button>
                <button onclick="calApp.changeView('timeGridWeek')"
                        class="text-xs px-3 py-1.5 border border-gray-200 hover:bg-gray-50 transition-colors" id="btnWeek">Tuần</button>
                <button onclick="calApp.changeView('timeGridDay')"
                        class="text-xs px-3 py-1.5 border border-gray-200 hover:bg-gray-50 transition-colors" id="btnDay">Ngày</button>
                <button onclick="calApp.today()"
                        class="text-xs px-3 py-1.5 bg-red-600 text-white hover:bg-red-700 transition-colors">Hôm nay</button>
            </div>
        </div>

        {{-- Calendar container --}}
        <div class="bg-white border border-gray-100">
            <div id="calendar" class="p-4 min-h-[600px]"
                 data-events-url="{{ route('calendar.events') }}"></div>

        </div>

        {{-- Event detail popup --}}
        <div id="eventPopup"
             class="hidden fixed z-50 bg-white border border-gray-200 shadow-xl p-4 w-72 text-sm rounded-none"
             style="top:0;left:0">
            <div class="flex justify-between items-center mb-3">
                <h4 id="popTitle" class="font-semibold text-gray-800 text-sm"></h4>
                <button onclick="document.getElementById('eventPopup').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600">×</button>
            </div>
            <div class="space-y-1 text-xs text-gray-500">
                <p><span class="text-gray-400">Lớp:</span> <span id="popClass"></span></p>
                <p><span class="text-gray-400">Giáo viên:</span> <span id="popTeacher"></span></p>
                <p><span class="text-gray-400">Trạng thái:</span> <span id="popStatus"></span></p>
                <p id="popNoteWrap" class="hidden"><span class="text-gray-400">Ghi chú:</span> <span id="popNotes"></span></p>
            </div>
            <a id="popUrl" href="#"
               class="mt-3 block text-center bg-red-600 text-white text-xs py-1.5 hover:bg-red-700 transition-colors">
                Điểm danh →
            </a>
        </div>
    </div>

    {{-- FullCalendar v6 via CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script>
    const EVENTS_URL = document.getElementById('calendar').dataset.eventsUrl;


    const calApp = (() => {
        let calendar;
        let currentClassFilter = '';

        const fetchEvents = (info, successCb, failureCb) => {
            const url = new URL(EVENTS_URL, location.href);
            url.searchParams.set('start', info.startStr);
            url.searchParams.set('end',   info.endStr);
            if (currentClassFilter) url.searchParams.set('class_id', currentClassFilter);

            fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(successCb)
                .catch(failureCb);
        };

        const showPopup = (info) => {
            const p = info.event.extendedProps;
            const rect = info.el.getBoundingClientRect();
            const popup = document.getElementById('eventPopup');

            document.getElementById('popTitle').textContent   = info.event.title;
            document.getElementById('popClass').textContent   = p.class_name;
            document.getElementById('popTeacher').textContent = p.teacher;
            document.getElementById('popStatus').textContent  = p.status;

            if (p.notes) {
                document.getElementById('popNotes').textContent = p.notes;
                document.getElementById('popNoteWrap').classList.remove('hidden');
            } else {
                document.getElementById('popNoteWrap').classList.add('hidden');
            }
            document.getElementById('popUrl').href = p.url;

            const left = Math.min(rect.right + 8, window.innerWidth - 295);
            const top  = Math.min(rect.top,        window.innerHeight - 220);
            popup.style.left = left + 'px';
            popup.style.top  = top  + 'px';
            popup.classList.remove('hidden');
        };

        document.addEventListener('click', (e) => {
            const popup = document.getElementById('eventPopup');
            if (!popup.contains(e.target)) popup.classList.add('hidden');
        });

        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(el, {
                locale: 'vi',
                initialView: 'timeGridWeek',
                headerToolbar: {
                    left:   'prev,next',
                    center: 'title',
                    right:  '',
                },
                slotMinTime:    '06:00:00',
                slotMaxTime:    '22:00:00',
                allDaySlot:     false,
                nowIndicator:   true,
                height:         'auto',
                events:         fetchEvents,
                eventClick:     showPopup,
                eventTimeFormat: { hour: '2-digit', minute: '2-digit', meridiem: false },
            });
            calendar.render();
        });

        return {
            changeView: (v) => calendar.changeView(v),
            today:      ()  => calendar.today(),
            filterByClass: (id) => {
                currentClassFilter = id;
                calendar.refetchEvents();
            },
        };
    })();
    </script>
</x-app-layout>
