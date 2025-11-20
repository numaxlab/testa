<article>
    <header class="container mx-auto px-4 mb-10">
        <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
            <li>
                <a href="">
                    Miga
                </a>
            </li>
            <li>
                <a href="">
                    De pan
                </a>
            </li>
        </x-numaxlab-atomic::molecules.breadcrumb>

        <h1 class="at-heading is-1">Kitchen Sink</h1>
    </header>

    <div class="container mx-auto px-4">
        <x-numaxlab-atomic::organisms.tier class="mb-10">
            <x-numaxlab-atomic::organisms.tier.header>
                <h2 class="at-heading is-2">Tipografía</h2>
            </x-numaxlab-atomic::organisms.tier.header>

            <div class="md:pr-[30%]">
                <h1 class="at-heading is-1 mb-5">Título 1 [at-heading is-1]</h1>
                <h2 class="at-heading is-2 mb-5">Título 2 [at-heading is-2]</h2>
                <h3 class="at-heading is-3 mb-5">Título 3 [at-heading is-3]</h3>
                <h4 class="at-heading is-4">Título 44[at-heading is-4]</h4>

                <div class="mt-5">
                    <div class="at-lead mb-5">
                        <p>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec viverra eros quis exenoration <a href="">pellentesque</a>. Suspendisse mauris mauris, ultricies id egestas. [at-lead] 
                        </p>
                    </div>

                    <div class="mb-5">
                        <p>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec viverra eros quis ex malesuada pellentesque. Suspendisse mauris mauris, ultricies id egestas. Lorem ipsum dolor, sit amet consectetur adipisicing elit. Eius repellat necessitatibus est eveniet sunt adipisci explicabo iure? Consequuntur nulla error unde, dolores reprehenderit nobis minus nam eveniet esse aliquam repellat?
                        </p>
                        <p>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec viverra eros quis ex malesuada pellentesque. <a href="">Suspendisse mauris mauris</a>, ultricies id egestas. [body]
                        </p>
                    </div>

                    <blockquote class="mb-5">
                        <br> Suspendisse mauris mauris, ultricies id egestas. [at-blockquote]
                    </blockquote>

                    <small class="at-small">
                        <br> Lorem ipsum dolor sit amet, sectetur ipsum do amet, consectetur. Lorem ipsum dolor sit amet, consectetur amet ipsum.[at-small]
                    </small>
                </div>
            </div>
        </x-numaxlab-atomic::organisms.tier>

        <x-numaxlab-atomic::organisms.tier class="mb-10">
            <x-numaxlab-atomic::organisms.tier.header>
                <h2 class="at-heading is-2">Colores</h2>
            </x-numaxlab-atomic::organisms.tier.header>

            <div class="flex gap-4">
                <div class="p-4 bg-primary">primary</div>
                <div class="p-4 bg-secondary">secondary</div>
                <div class="p-4 bg-success">success</div>
                <div class="p-4 bg-warning">warning</div>
                <div class="p-4 bg-danger">danger</div>
            </div>
        </x-numaxlab-atomic::organisms.tier>

        <x-numaxlab-atomic::organisms.tier class="mb-10">
            <x-numaxlab-atomic::organisms.tier.header>
                <h2 class="at-heading is-2">Libros</h2>
                <a href="" class="at-small">
                    
                </a>
            </x-numaxlab-atomic::organisms.tier.header>

            <div class="overflow-x-auto">
                <ul class="grid grid-flow-col auto-cols-[50%] md:auto-cols-[25%] lg:auto-cols-[16.666%] gap-6">
                @foreach ($products as $product)
                <li>
                    <x-trafikrak::products.summary
                    :product="$product"
                    :href="route('trafikrak.storefront.bookshop.products.show', $product->defaultUrl->slug)"
                />
                </li>
                @endforeach
            </ul>
            </div>

        </x-numaxlab-atomic::organisms.tier>
    </div>

    <div class="mb-10">
        <div class="container mx-auto px-4 mb-5">
            <h2 class="at-heading is-2">
                Banners
            </h2>
        </div>

        <x-trafikrak::banners.contained :banner="$banner"/>

        <x-trafikrak::banners.full-width :banner="$banner"/>
    </div>

    <div class="container mx-auto px-4">
        <x-numaxlab-atomic::organisms.tier class="mb-10">
            <x-numaxlab-atomic::organisms.tier.header>
                <h2 class="at-heading is-2">Cursos</h2>
            </x-numaxlab-atomic::organisms.tier.header>

            <ul class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                @foreach($courses as $course)
                    <li>
                        <x-trafikrak::courses.summary :course="$course"/>
                    </li>
                @endforeach
            </ul>
        </x-numaxlab-atomic::organisms.tier>
    </div>


    <div id="app" class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Barra Superior (Controles) -->
        <div class="bg-white shadow-lg rounded-xl mb-6 p-4 flex flex-wrap items-center justify-between space-y-4 sm:space-y-0 sm:space-x-4">
            <h1 class="text-3xl font-bold text-gray-800">Actividades</h1>

            <!-- Selectores de Mes y Año (REEMPLAZO DE BOTONES) -->
            <div class="flex items-center space-x-3">
                <!-- Selector de Mes -->
                <select id="monthSelector" onchange="updateDateAndRender()" class="p-3 border border-primary rounded-lg shadow-sm focus:ring-primary focus:border-primary transition duration-150 text-gray-700">
                    <!-- Opciones inyectadas por JS -->
                </select>
                
                <!-- Selector de Año -->
                <select id="yearSelector" onchange="updateDateAndRender()" class="p-3 border border-primary rounded-lg shadow-sm focus:ring-primary focus:border-primary transition duration-150 text-gray-700">
                    <!-- Opciones inyectadas por JS -->
                </select>
            </div>

            <!-- Selector de Tipo de Actividad -->
            <select id="activityTypeFilter" onchange="renderCalendar()" class="p-3 border border-primary rounded-lg shadow-sm focus:ring-primary focus:border-primary transition duration-150 text-gray-700">
                <option value="todos">Todos los Tipos</option>
                <option value="Trabajo">Trabajo</option>
                <option value="Personal">Personal</option>
            </select>
        </div>

        <!-- Vista de Escritorio (Calendario de Cuadrícula detallada) -->
        <div id="calendarGridContainer" class="hidden sm:block bg-white shadow-lg rounded-xl overflow-hidden">
            <div class="grid grid-cols-7 text-center">
                <div class="bg-primary text-white py-2 font-semibold rounded-tl-xl">Lun</div>
                <div class="bg-primary text-white py-2 font-semibold">Mar</div>
                <div class="bg-primary text-white py-2 font-semibold">Mié</div>
                <div class="bg-primary text-white py-2 font-semibold">Jue</div>
                <div class="bg-primary text-white py-2 font-semibold">Vie</div>
                <div class="bg-primary text-white py-2 font-semibold">Sáb</div>
                <div class="bg-primary text-white py-2 font-semibold rounded-tr-xl">Dom</div>
            </div>
            <div id="calendarGrid" class="grid grid-cols-7 border-t border-l border-gray-200">
                <!-- Días del calendario inyectados por JavaScript -->
            </div>
        </div>

        <!-- Vista de Móvil (Cuadrícula simplificada y Panel de detalles) -->
        <div id="mobileViewContainer" class="sm:hidden mt-6 px-2">
            <!-- Encabezado de la cuadrícula móvil -->
            <div class="grid grid-cols-7 text-center rounded-t-xl overflow-hidden shadow">
                <div class="bg-primary text-white py-1 text-xs font-semibold">L</div>
                <div class="bg-primary text-white py-1 text-xs font-semibold">M</div>
                <div class="bg-primary text-white py-1 text-xs font-semibold">X</div>
                <div class="bg-primary text-white py-1 text-xs font-semibold">J</div>
                <div class="bg-primary text-white py-1 text-xs font-semibold">V</div>
                <div class="bg-primary text-white py-1 text-xs font-semibold">S</div>
                <div class="bg-primary text-white py-1 text-xs font-semibold">D</div>
            </div>
            
            <!-- Cuadrícula del Calendario Móvil (solo números y puntos) -->
            <div id="mobileCalendarGrid" class="grid grid-cols-7 border-t border-l border-gray-200 bg-white shadow-xl rounded-b-xl overflow-hidden">
                <!-- Días inyectados por JS -->
            </div>

            <!-- Panel de Detalles de Día (Se muestra al hacer clic en un día con actividad) -->
            <div id="dayDetailPanel" class="mt-4 bg-white p-4 rounded-xl shadow-lg border-l-4 border-primary hidden">
                <h3 class="text-xl font-bold text-gray-800 mb-3">Actividades del día <span id="detailDayNumber"></span></h3>
                <div id="detailActivityList" class="space-y-2">
                    <!-- Detalles de actividades inyectados por JS -->
                </div>
                <button onclick="closeDayDetails()" class="mt-4 w-full bg-primary hover:bg-primary-hover text-white font-semibold py-2 rounded-lg transition duration-150">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <script>
        // Clases de utilidad
        const DAY_CELL_CLASSES = 'min-h-[120px] border-b border-r p-2 transition duration-150 ease-in-out';
        const OTHER_MONTH_DAY_CLASSES = 'bg-gray-50 text-gray-400';
        // Nuevas clases para la cuadrícula móvil simplificada
        const MOBILE_CELL_CLASSES = 'h-16 flex flex-col items-center justify-center border-b border-r text-center transition duration-150 ease-in-out relative hover:bg-gray-100 cursor-pointer';

        // --- 1. DATOS Y CONFIGURACIÓN INICIAL ---
        // Inicializar con la fecha actual
        const today = new Date();
        let currentYear = today.getFullYear(); 
        let currentMonth = today.getMonth(); // 0 = Enero, 11 = Diciembre

        const months = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

        // Actividades de ejemplo para Diciembre de 2025 (31 días).
        // Adaptamos la fecha para que el ejemplo sea visible al iniciar. Usaremos la fecha actual para el ejemplo si es Diciembre, si no, usaremos Diciembre 2025
        if (currentYear !== 2025 || currentMonth !== 11) {
             currentYear = 2025;
             currentMonth = 11;
        }

        const allActivities = [
            { day: 2, time: "10:00", location: "Oficina Central", title: "Reunión de Planificación Q1", type: "Trabajo", link: "https://www.google.com/search?q=planificacion+anual" },
            { day: 5, time: "19:30", location: "Teatro Nacional", title: "Concierto de Jazz", type: "Personal", link: "https://www.google.com/search?q=concierto+de+jazz" },
            { day: 10, time: "14:00", location: "Restaurante Italia", title: "Almuerzo con Cliente VIP", type: "Trabajo", link: "https://www.google.com/search?q=almuerzo+vip" },
            { day: 15, time: "17:00", location: "Gimnasio", title: "Clase de Yoga", type: "Personal", link: "https://www.google.com/search?q=clase+de+yoga" },
            { day: 15, time: "19:00", location: "Casa", title: "Cena con Amigos", type: "Personal", link: "https://www.google.com/search?q=cena+con+amigos" },
            { day: 24, time: "20:00", location: "Casa Familiar", title: "Cena de Nochebuena", type: "Personal", link: "https://www.google.com/search?q=nochebuena" },
            { day: 26, time: "09:00", location: "Remoto", title: "Revisión de Proyecto Final", type: "Trabajo", link: "https://www.google.com/search?q=revision+proyecto" },
            { day: 31, time: "23:00", location: "Casa de Ana", title: "Fiesta de Nochevieja", type: "Personal", link: "https://www.google.com/search?q=fiesta+nochevieja" },
        ];

        // --- 2. FUNCIONES DE FECHA ---
        function getDaysInMonth(year, month) {
            return new Date(year, month + 1, 0).getDate();
        }

        function getFirstDayOfMonth(year, month) {
            // Devuelve 0 para Domingo, 1 para Lunes, etc.
            const dayIndex = new Date(year, month, 1).getDay();
            // Ajuste para que Lunes sea 0 (según el calendario de cuadrícula)
            return (dayIndex === 0) ? 6 : dayIndex - 1;
        }

        // --- 3. MANEJO DE SELECTORES ---
        function populateSelectors() {
            const monthSelector = document.getElementById('monthSelector');
            const yearSelector = document.getElementById('yearSelector');
            
            // 3.1. Rellenar Meses
            monthSelector.innerHTML = months.map((month, index) => 
                `<option value="${index}" ${index === currentMonth ? 'selected' : ''}>${month}</option>`
            ).join('');

            // 3.2. Rellenar Años (rango de 2020 a 2030)
            yearSelector.innerHTML = '';
            const startYear = 2020;
            const endYear = 2030;
            for (let year = startYear; year <= endYear; year++) {
                const option = document.createElement('option');
                option.value = year;
                option.textContent = year;
                if (year === currentYear) {
                    option.selected = true;
                }
                yearSelector.appendChild(option);
            }
        }

        function updateDateAndRender() {
            const monthSelector = document.getElementById('monthSelector');
            const yearSelector = document.getElementById('yearSelector');

            currentMonth = parseInt(monthSelector.value);
            currentYear = parseInt(yearSelector.value);
            
            renderCalendar();
        }

        // --- 4. RENDERIZADO DEL CALENDARIO (VISTA DE ESCRITORIO) ---
        function renderCalendarGrid() {
            const grid = document.getElementById('calendarGrid');
            grid.innerHTML = '';
            closeDayDetails(); 
            
            const totalDays = getDaysInMonth(currentYear, currentMonth);
            const firstDayIndex = getFirstDayOfMonth(currentYear, currentMonth); // 0 = Lunes

            const filterType = document.getElementById('activityTypeFilter').value;
            
            // Determinar los días del mes anterior para rellenar
            const prevMonthDays = getDaysInMonth(currentYear, currentMonth - 1);
            
            // Días de relleno del mes anterior
            for (let i = firstDayIndex; i > 0; i--) {
                const day = document.createElement('div');
                day.className = `${DAY_CELL_CLASSES} ${OTHER_MONTH_DAY_CLASSES} text-right`;
                if ((firstDayIndex - i + 1) % 7 === 0) {
                    day.className = day.className.replace('border-r', 'border-r-0');
                }
                day.innerHTML = `<div class="text-sm font-light">${prevMonthDays - i + 1}</div>`;
                grid.appendChild(day);
            }

            // Días del mes actual
            for (let dayNumber = 1; dayNumber <= totalDays; dayNumber++) {
                const day = document.createElement('div');
                
                let dayClasses = `${DAY_CELL_CLASSES} bg-white text-right relative hover:bg-gray-50`;
                
                // Eliminar borde derecho si es Domingo (posición 7)
                if ((firstDayIndex + dayNumber) % 7 === 0) {
                    dayClasses = dayClasses.replace('border-r', 'border-r-0');
                }

                // Manejo de bordes redondeados en la última fila
                const isLastDay = dayNumber === totalDays;
                const isLastCellInGrid = totalDays + firstDayIndex > 35 && (firstDayIndex + dayNumber) > (35);
                
                if (isLastDay && isLastCellInGrid) {
                     dayClasses += ' rounded-br-xl'; 
                }

                day.className = dayClasses;
                
                day.innerHTML = `<div class="text-lg font-bold text-gray-800">${dayNumber}</div>`;

                // Actividades para este día
                const dayActivities = allActivities.filter(
                    act => act.day === dayNumber && (filterType === 'todos' || act.type === filterType)
                );

                const activitiesHtml = dayActivities.map(act => `
                    <a href="${act.link}" target="_blank" class="block mt-1 p-1 rounded-lg text-left truncate text-xs font-medium transition duration-150 ease-in-out 
                        ${act.type === 'Trabajo' ? 'bg-primary/20 text-primary hover:bg-primary/30' : 'bg-secondary/20 text-secondary hover:bg-secondary/30'}">
                        <span class="font-bold">${act.time}</span> ${act.title}
                        <span class="text-gray-600 hidden sm:inline-block">(${act.location})</span>
                    </a>
                `).join('');

                day.innerHTML += activitiesHtml;
                grid.appendChild(day);
            }

            // Días de relleno del mes siguiente
            const totalCells = firstDayIndex + totalDays;
            const cellsToFill = (7 - (totalCells % 7)) % 7;
            
            for (let i = 1; i <= cellsToFill; i++) {
                const day = document.createElement('div');
                day.className = `${DAY_CELL_CLASSES} ${OTHER_MONTH_DAY_CLASSES} text-right`;
                
                if ((totalCells + i) % 7 === 0) {
                    day.className = day.className.replace('border-r', 'border-r-0');
                }
                
                day.innerHTML = `<div class="text-sm font-light">${i}</div>`;
                grid.appendChild(day);
            }
        }
        
        // --- 5. RENDERIZADO DE LA CUADRÍCULA MÓVIL (VISTA DE MÓVIL) ---
        function renderMobileGrid() {
            const grid = document.getElementById('mobileCalendarGrid');
            grid.innerHTML = '';
            closeDayDetails(); 
            
            const totalDays = getDaysInMonth(currentYear, currentMonth);
            const firstDayIndex = getFirstDayOfMonth(currentYear, currentMonth); 

            const filterType = document.getElementById('activityTypeFilter').value;
            // Mapa para contar actividades por día (usado para el punto indicador)
            const activitiesByDay = allActivities.reduce((acc, act) => {
                if (filterType === 'todos' || act.type === filterType) {
                    acc[act.day] = (acc[act.day] || 0) + 1;
                }
                return acc;
            }, {});

            // Días de relleno del mes anterior
            const prevMonthDays = getDaysInMonth(currentYear, currentMonth - 1);
            for (let i = firstDayIndex; i > 0; i--) {
                const day = document.createElement('div');
                day.className = `${MOBILE_CELL_CLASSES} ${OTHER_MONTH_DAY_CLASSES} bg-gray-50 cursor-default hover:bg-gray-50`;
                if ((firstDayIndex - i + 1) % 7 === 0) {
                    day.className = day.className.replace('border-r', 'border-r-0');
                }
                day.innerHTML = `<div class="text-xs font-light">${prevMonthDays - i + 1}</div>`;
                grid.appendChild(day);
            }

            // Días del mes actual
            for (let dayNumber = 1; dayNumber <= totalDays; dayNumber++) {
                const day = document.createElement('div');
                
                let dayClasses = `${MOBILE_CELL_CLASSES} bg-white`;
                
                if ((firstDayIndex + dayNumber) % 7 === 0) {
                    dayClasses = dayClasses.replace('border-r', 'border-r-0');
                }
                
                day.className = dayClasses;
                day.setAttribute('data-day', dayNumber);
                day.onclick = () => showDayDetails(dayNumber);

                let content = `<div class="text-sm font-bold text-gray-800">${dayNumber}</div>`;

                // Agregar punto si hay actividades
                if (activitiesByDay[dayNumber] > 0) {
                    const activityType = allActivities.find(act => act.day === dayNumber)?.type;
                    const dotColor = activityType === 'Trabajo' ? 'bg-primary' : 'bg-secondary';
                    content += `<div class="w-2 h-2 rounded-full ${dotColor} absolute top-1 right-1"></div>`;
                }

                day.innerHTML = content;
                grid.appendChild(day);
            }

            // Días de relleno del mes siguiente
            const totalCells = firstDayIndex + totalDays;
            const cellsToFill = (7 - (totalCells % 7)) % 7;
            
            for (let i = 1; i <= cellsToFill; i++) {
                const day = document.createElement('div');
                day.className = `${MOBILE_CELL_CLASSES} ${OTHER_MONTH_DAY_CLASSES} bg-gray-50 cursor-default hover:bg-gray-50`;
                
                if ((totalCells + i) % 7 === 0) {
                    day.className = day.className.replace('border-r', 'border-r-0');
                }
                
                day.innerHTML = `<div class="text-xs font-light">${i}</div>`;
                grid.appendChild(day);
            }
        }
        
        // --- 6. PANEL DE DETALLES DE DÍA ---
        function showDayDetails(dayNumber) {
            const panel = document.getElementById('dayDetailPanel');
            const list = document.getElementById('detailActivityList');
            const filterType = document.getElementById('activityTypeFilter').value;

            // 1. Filtrar actividades
            const dayActivities = allActivities
                .filter(act => act.day === dayNumber && (filterType === 'todos' || act.type === filterType))
                .sort((a, b) => a.time.localeCompare(b.time));

            document.getElementById('detailDayNumber').textContent = `${dayNumber} de ${months[currentMonth]}`;
            panel.classList.add('hidden');

            if (dayActivities.length === 0) {
                 list.innerHTML = `<p class="text-gray-500 italic text-center">No hay actividades programadas para este día con el filtro actual.</p>`;
            } else {
                // 2. Generar HTML para el listado
                list.innerHTML = dayActivities.map(act => `
                    <a href="${act.link}" target="_blank" class="block p-3 rounded-lg border-l-4 
                        ${act.type === 'Trabajo' ? 'border-primary bg-primary/10 hover:bg-primary/20' : 'border-secondary bg-secondary/10 hover:bg-secondary/20'} 
                        transition duration-150 shadow-sm">
                        <div class="flex justify-between items-start">
                            <span class="font-bold text-sm text-gray-800">${act.title}</span>
                            <span class="text-xs font-mono text-primary-hover">${act.time}</span>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">${act.location}</p>
                    </a>
                `).join('');
            }
            
            // 3. Mostrar panel
            panel.classList.remove('hidden');
            panel.scrollIntoView({ behavior: 'smooth' });
        }

        function closeDayDetails() {
            document.getElementById('dayDetailPanel').classList.add('hidden');
        }
        
        // --- 7. RENDERIZADO GLOBAL ---
        function renderCalendar() {
            // Rellenar selectores si es la primera carga
            const monthSelector = document.getElementById('monthSelector');
            if (monthSelector.options.length === 0) {
                 populateSelectors();
            }
            
            // Renderiza ambas vistas.
            renderCalendarGrid();
            renderMobileGrid(); 
        }

        // --- 8. INICIALIZACIÓN ---
        document.addEventListener('DOMContentLoaded', renderCalendar);
    </script>


    
    


</article>

