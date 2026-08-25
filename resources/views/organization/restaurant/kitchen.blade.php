<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kitchen Display System (KDS)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #111827; }
        .kds-card { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
        .blink { animation: blinker 1.5s linear infinite; }
        @keyframes blinker { 50% { opacity: 0; } }
    </style>
</head>
<body class="h-screen flex flex-col overflow-hidden text-white">

    <!-- Top Bar -->
    <header class="bg-gray-900 border-b border-gray-800 px-6 py-4 flex justify-between items-center shrink-0">
        <div class="flex items-center gap-4">
            <h1 class="text-2xl font-black tracking-tight text-white uppercase">KDS Dashboard</h1>
            <span class="bg-blue-600 text-xs font-bold px-2 py-1 rounded">{{ session('active_location_id') ? \App\Models\Location::find(session('active_location_id'))->name : '' }}</span>
            <div id="connection-status" class="flex items-center text-xs font-medium text-green-400 gap-1.5 ml-4">
                <div class="w-2 h-2 rounded-full bg-green-400"></div> Connected
            </div>
        </div>
        <div class="flex items-center gap-4 text-sm font-medium">
            <span id="clock" class="font-bold text-gray-300 text-lg mr-4"></span>
            <button onclick="toggleAudio()" id="audio-toggle" class="bg-gray-800 hover:bg-gray-700 px-3 py-1.5 rounded-lg border border-gray-700 transition flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M18.364 5.636a9 9 0 010 12.728M12 18l-4-4H5a2 2 0 01-2-2V10a2 2 0 012-2h3l4-4v16z"></path></svg>
                Sound: <span id="audio-status" class="text-green-400">ON</span>
            </button>
            <a href="{{ route('organization.dashboard') }}" class="text-gray-400 hover:text-white transition">Exit KDS</a>
        </div>
    </header>

    <!-- Kanban Board -->
    <div class="flex-grow flex gap-4 p-4 overflow-x-auto overflow-y-hidden bg-gray-900">
        
        <!-- RECEIVED COLUMN -->
        <div class="flex-1 min-w-[320px] bg-gray-800 rounded-xl flex flex-col border border-gray-700 overflow-hidden">
            <div class="bg-gray-700 border-b border-gray-600 px-4 py-3 flex justify-between items-center">
                <h2 class="font-bold text-lg text-white">RECEIVED</h2>
                <span id="count-received" class="bg-gray-900 text-gray-300 text-xs font-bold px-2 py-1 rounded-full">0</span>
            </div>
            <div id="col-received" class="flex-1 p-3 overflow-y-auto space-y-3">
                <!-- Cards injected here -->
            </div>
        </div>

        <!-- PREPARING COLUMN -->
        <div class="flex-1 min-w-[320px] bg-gray-800 rounded-xl flex flex-col border border-gray-700 overflow-hidden">
            <div class="bg-yellow-900 border-b border-yellow-700 px-4 py-3 flex justify-between items-center">
                <h2 class="font-bold text-lg text-yellow-100">PREPARING</h2>
                <span id="count-preparing" class="bg-yellow-800 text-yellow-100 text-xs font-bold px-2 py-1 rounded-full">0</span>
            </div>
            <div id="col-preparing" class="flex-1 p-3 overflow-y-auto space-y-3">
                <!-- Cards injected here -->
            </div>
        </div>

        <!-- READY COLUMN -->
        <div class="flex-1 min-w-[320px] bg-gray-800 rounded-xl flex flex-col border border-gray-700 overflow-hidden">
            <div class="bg-green-900 border-b border-green-700 px-4 py-3 flex justify-between items-center">
                <h2 class="font-bold text-lg text-green-100">READY</h2>
                <span id="count-ready" class="bg-green-800 text-green-100 text-xs font-bold px-2 py-1 rounded-full">0</span>
            </div>
            <div id="col-ready" class="flex-1 p-3 overflow-y-auto space-y-3">
                <!-- Cards injected here -->
            </div>
        </div>
    </div>

<script>
    let knownOrderIds = new Set();
    let audioEnabled = true;

    // A tiny beep data URI (sine wave)
    const beepSound = new Audio("data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YU"+Array(100).join("12345"));

    function toggleAudio() {
        audioEnabled = !audioEnabled;
        document.getElementById('audio-status').innerText = audioEnabled ? 'ON' : 'OFF';
        document.getElementById('audio-status').className = audioEnabled ? 'text-green-400' : 'text-red-400';
    }

    function playBeep() {
        if (!audioEnabled) return;
        try {
            let ctx = new (window.AudioContext || window.webkitAudioContext)();
            let osc = ctx.createOscillator();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, ctx.currentTime);
            osc.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.2);
        } catch (e) {
            console.log("Audio play failed: ", e);
        }
    }

    // Update Clock
    setInterval(() => {
        const now = new Date();
        document.getElementById('clock').innerText = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    }, 1000);

    // Fetch and render orders
    async function fetchOrders() {
        try {
            const response = await fetch('{{ route('organization.menu.kitchen.orders.fetch') }}');
            const orders = await response.json();
            renderOrders(orders);
        } catch (error) {
            console.error('KDS Fetch Error', error);
            document.getElementById('connection-status').innerHTML = '<div class="w-2 h-2 rounded-full bg-red-500 blink"></div> Offline';
            document.getElementById('connection-status').className = "flex items-center text-xs font-medium text-red-500 gap-1.5 ml-4";
        }
    }

    function renderOrders(orders) {
        document.getElementById('connection-status').innerHTML = '<div class="w-2 h-2 rounded-full bg-green-400"></div> Connected';
        document.getElementById('connection-status').className = "flex items-center text-xs font-medium text-green-400 gap-1.5 ml-4";

        let newOrdersDetected = false;
        
        let cols = {
            'Received': [],
            'Preparing': [],
            'Ready': []
        };

        orders.forEach(order => {
            if (!knownOrderIds.has(order.id)) {
                newOrdersDetected = true;
                knownOrderIds.add(order.id);
            }
            if(cols[order.status]) {
                cols[order.status].push(order);
            }
        });

        if (newOrdersDetected) {
            playBeep();
        }

        // Render Received
        renderCol('col-received', 'count-received', cols['Received'], 'Received');
        // Render Preparing
        renderCol('col-preparing', 'count-preparing', cols['Preparing'], 'Preparing');
        // Render Ready
        renderCol('col-ready', 'count-ready', cols['Ready'], 'Ready');
    }

    function renderCol(colId, countId, orders, type) {
        const col = document.getElementById(colId);
        document.getElementById(countId).innerText = orders.length;
        
        col.innerHTML = '';
        orders.forEach(order => {
            col.appendChild(createOrderCard(order, type));
        });
    }

    function createOrderCard(order, type) {
        const div = document.createElement('div');
        div.className = "bg-gray-100 rounded-lg p-3 text-gray-900 kds-card flex flex-col";
        
        // Header
        let header = `<div class="flex justify-between items-start mb-2 border-b border-gray-300 pb-2">
            <div>
                <div class="font-black text-xl">${order.order_number}</div>
                <div class="text-xs font-bold text-gray-600 uppercase tracking-wide mt-1">${order.order_type}</div>
            </div>
            <div class="text-right">`;
            
        if (order.table) {
            header += `<div class="bg-blue-600 text-white font-bold text-sm px-2 py-1 rounded">Table ${order.table.name}</div>`;
        }
        
        header += `<div class="text-xs text-gray-500 mt-1">${new Date(order.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
            </div>
        </div>`;

        // Body
        let body = `<div class="flex-grow space-y-1 mb-3">`;
        order.items.forEach(item => {
            body += `<div class="flex justify-between text-sm font-medium">
                <span><span class="font-bold text-blue-600 mr-1">${item.quantity}x</span> ${item.name_snapshot}</span>
            </div>`;
        });
        body += `</div>`;

        // Notes
        if (order.special_notes) {
            body += `<div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-900 text-xs font-bold p-2 mb-3 rounded-r">
                NOTE: ${order.special_notes}
            </div>`;
        }

        // Actions
        let actions = `<div class="flex gap-2 mt-auto border-t border-gray-300 pt-2">`;
        if (type === 'Received') {
            actions += `<button onclick="updateStatus(${order.id}, 'Preparing')" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 rounded shadow transition">Start Prep</button>`;
        } else if (type === 'Preparing') {
            actions += `<button onclick="updateStatus(${order.id}, 'Ready')" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded shadow transition">Mark Ready</button>`;
        } else if (type === 'Ready') {
            actions += `<button onclick="updateStatus(${order.id}, 'Served')" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded shadow transition">Serve / Clear</button>`;
        }
        actions += `</div>`;

        div.innerHTML = header + body + actions;
        return div;
    }

    async function updateStatus(orderId, newStatus) {
        try {
            await fetch(`{{ url('organization/menu/kitchen/api/orders') }}/${orderId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: newStatus })
            });
            fetchOrders(); // immediate refresh
        } catch(e) {
            alert('Failed to update status.');
        }
    }

    // Polling Loop
    setInterval(fetchOrders, 10000); // Poll every 10 seconds
    fetchOrders(); // Initial fetch
</script>
</body>
</html>
