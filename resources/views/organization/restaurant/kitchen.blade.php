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
    <header class="bg-gray-900 border-b border-gray-800 px-6 py-3 flex flex-wrap justify-between items-center shrink-0 gap-4">
        <div class="flex items-center gap-4">
            <h1 class="text-2xl font-black tracking-tight text-white uppercase">KDS Dashboard</h1>
            <span class="bg-blue-600 text-xs font-bold px-2.5 py-1 rounded-lg">{{ session('active_location_id') ? \App\Models\Location::find(session('active_location_id'))->name : '' }}</span>
            <div id="connection-status" class="flex items-center text-xs font-medium text-green-400 gap-1.5 ml-2">
                <div class="w-2 h-2 rounded-full bg-green-400"></div> Connected
            </div>
        </div>

        <!-- Audio Alert & Sound Settings Controls -->
        <div class="flex items-center gap-3 text-xs font-medium flex-wrap">
            <span id="clock" class="font-bold text-gray-300 text-base mr-2 font-mono"></span>
            
            <!-- Sound Alert Mode Selector -->
            <div class="flex items-center gap-2 bg-gray-800 border border-gray-700 px-3 py-1.5 rounded-xl">
                <span class="text-gray-400 font-bold">Sound Alert:</span>
                <select id="sound-mode-select" onchange="setSoundMode(this.value)" class="bg-gray-900 text-white font-bold text-xs rounded-lg px-2 py-1 border border-gray-700 focus:outline-none focus:border-indigo-500">
                    <option value="zomato">🛵 Zomato / Swiggy Partner Ringtone</option>
                    <option value="iphone">📱 iPhone Radar Signature Tune</option>
                    <option value="fanfare">🎺 Upbeat Food Fanfare Jingle</option>
                    <option value="hotel_bell">🛎️ Hotel Service Counter Bell</option>
                    <option value="voice">🗣️ Voice Announcement (AI Speech)</option>
                    <option value="siren">🚨 Loud Emergency Siren</option>
                    <option value="bell">🔔 Ding-Dong Bell Chime</option>
                    <option value="ping">🎵 Soft Digital Ping</option>
                </select>


            </div>

            <!-- Custom Voice Message Template Input -->
            <div id="custom-speech-container" class="flex items-center gap-2 bg-gray-800 border border-gray-700 px-3 py-1.5 rounded-xl">
                <span class="text-gray-400 font-bold">Voice Msg:</span>
                <input type="text" id="speech-template-input" onchange="saveSpeechTemplate(this.value)" class="bg-gray-900 text-white font-medium text-xs rounded-lg px-2.5 py-1 border border-gray-700 w-60 focus:outline-none focus:border-indigo-500" placeholder="Naya Order Aaya Hai! {table_name}">
            </div>

            <!-- Continuous Ringing Loop Toggle -->
            <label class="flex items-center gap-2 bg-gray-800 border border-gray-700 px-3 py-1.5 rounded-xl cursor-pointer hover:bg-gray-750 transition">
                <input type="checkbox" id="repeat-ring-checkbox" onchange="toggleRepeatRinging(this.checked)" class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4 bg-gray-900 border-gray-700">
                <span class="text-gray-300 font-bold text-xs">🔁 Repeat Until Received</span>
            </label>

            <!-- Active Ringing Status & Silence Button -->
            <button id="silence-ring-btn" onclick="silenceRingingLoop()" class="hidden bg-rose-600 hover:bg-rose-700 text-white px-3 py-1.5 rounded-xl font-bold transition flex items-center gap-1.5 shadow-md blink">
                ⏹️ Silence Alarm
            </button>

            <!-- Test Sound Button -->
            <button onclick="testSoundAlert()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-xl font-bold transition flex items-center gap-1 shadow-sm">
                🔊 Test Alert
            </button>

            <!-- Mute / Unmute Toggle -->
            <button onclick="toggleAudio()" id="audio-toggle" class="bg-gray-800 hover:bg-gray-700 px-3 py-1.5 rounded-xl border border-gray-700 transition flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M18.364 5.636a9 9 0 010 12.728M12 18l-4-4H5a2 2 0 01-2-2V10a2 2 0 012-2h3l4-4v16z"></path></svg>
                Sound: <span id="audio-status" class="text-green-400 font-bold">ON</span>
            </button>

            <a href="{{ route('organization.dashboard') }}" class="text-gray-400 hover:text-white transition font-bold">Exit KDS</a>
        </div>
    </header>

    <!-- Kanban Board -->
    <div class="flex-grow flex gap-4 p-4 overflow-x-auto overflow-y-hidden bg-gray-900">
        
        <!-- RECEIVED COLUMN -->
        <div class="flex-1 min-w-[320px] bg-gray-800 rounded-xl flex flex-col border border-gray-700 overflow-hidden">
            <div class="bg-gray-700 border-b border-gray-600 px-4 py-3 flex justify-between items-center">
                <h2 class="font-bold text-lg text-white">RECEIVED</h2>
                <span id="count-received" class="bg-gray-900 text-gray-300 text-xs font-bold px-2.5 py-1 rounded-full">0</span>
            </div>
            <div id="col-received" class="flex-1 p-3 overflow-y-auto space-y-3">
                <!-- Cards injected here -->
            </div>
        </div>

        <!-- PREPARING COLUMN -->
        <div class="flex-1 min-w-[320px] bg-gray-800 rounded-xl flex flex-col border border-gray-700 overflow-hidden">
            <div class="bg-yellow-900 border-b border-yellow-700 px-4 py-3 flex justify-between items-center">
                <h2 class="font-bold text-lg text-yellow-100">PREPARING</h2>
                <span id="count-preparing" class="bg-yellow-800 text-yellow-100 text-xs font-bold px-2.5 py-1 rounded-full">0</span>
            </div>
            <div id="col-preparing" class="flex-1 p-3 overflow-y-auto space-y-3">
                <!-- Cards injected here -->
            </div>
        </div>

        <!-- READY COLUMN -->
        <div class="flex-1 min-w-[320px] bg-gray-800 rounded-xl flex flex-col border border-gray-700 overflow-hidden">
            <div class="bg-green-900 border-b border-green-700 px-4 py-3 flex justify-between items-center">
                <h2 class="font-bold text-lg text-green-100">READY</h2>
                <span id="count-ready" class="bg-green-800 text-green-100 text-xs font-bold px-2.5 py-1 rounded-full">0</span>
            </div>
            <div id="col-ready" class="flex-1 p-3 overflow-y-auto space-y-3">
                <!-- Cards injected here -->
            </div>
        </div>
    </div>

<script>
    let knownOrderIds = new Set();
    let isInitialLoad = true;
    let audioEnabled = true;

    // Ringing Loop State
    let repeatRingingEnabled = localStorage.getItem('kds_repeat_ring') !== 'false'; // default true
    let ringingLoopInterval = null;
    let currentlyRingingOrder = null;

    // Load user preferences from localStorage
    let soundMode = localStorage.getItem('kds_sound_mode') || 'zomato';

    let customSpeechTemplate = localStorage.getItem('kds_speech_template') || 'Naya Order Aaya Hai! {table_name}, Order {order_number}';

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('sound-mode-select').value = soundMode;
        document.getElementById('speech-template-input').value = customSpeechTemplate;
        document.getElementById('repeat-ring-checkbox').checked = repeatRingingEnabled;
        document.getElementById('custom-speech-container').style.display = soundMode === 'voice' ? 'flex' : 'none';
    });

    function setSoundMode(mode) {
        soundMode = mode;
        localStorage.setItem('kds_sound_mode', mode);
        document.getElementById('custom-speech-container').style.display = mode === 'voice' ? 'flex' : 'none';
    }

    function saveSpeechTemplate(text) {
        customSpeechTemplate = text || 'Naya Order Aaya Hai! {table_name}';
        localStorage.setItem('kds_speech_template', customSpeechTemplate);
    }

    function toggleRepeatRinging(checked) {
        repeatRingingEnabled = checked;
        localStorage.setItem('kds_repeat_ring', checked ? 'true' : 'false');
        if (!checked) {
            stopRingingLoop();
        }
    }

    function toggleAudio() {
        audioEnabled = !audioEnabled;
        document.getElementById('audio-status').innerText = audioEnabled ? 'ON' : 'OFF';
        document.getElementById('audio-status').className = audioEnabled ? 'text-green-400 font-bold' : 'text-red-400 font-bold';
        if (!audioEnabled) {
            stopRingingLoop();
        }
    }

    // Play Selected Sound Alert Option
    function playOrderAlert(latestOrder = null) {
        if (!audioEnabled) return;

        if (soundMode === 'zomato') {
            playZomatoStyleChime();
        } else if (soundMode === 'iphone') {
            playIphoneRadarTune();
        } else if (soundMode === 'fanfare') {
            playFanfareJingle();
        } else if (soundMode === 'hotel_bell') {
            playHotelServiceBell();
        } else if (soundMode === 'voice') {
            playVoiceAlert(latestOrder);
        } else if (soundMode === 'bell') {
            playBellChime();
        } else if (soundMode === 'siren') {
            playEmergencySiren();
        } else if (soundMode === 'ping') {
            playDigitalPing();
        }
    }

    // 🛵 Zomato / Swiggy Partner App Signature Order Alert Ringtone
    function playZomatoStyleChime() {
        try {
            let ctx = new (window.AudioContext || window.webkitAudioContext)();
            let now = ctx.currentTime;

            // Fast-paced 2-burst upbeat Food Delivery Partner Chime
            let notes = [
                { f: 1318.51, t: 0.0,  d: 0.12 }, // E6
                { f: 1567.98, t: 0.12, d: 0.12 }, // G6
                { f: 1975.53, t: 0.24, d: 0.14 }, // B6
                { f: 2637.02, t: 0.38, d: 0.35 }, // E7 Peak
                { f: 1567.98, t: 0.70, d: 0.12 }, // G6 Repeat
                { f: 1975.53, t: 0.82, d: 0.12 }, // B6 Repeat
                { f: 2637.02, t: 0.94, d: 0.45 }  // E7 Long Peak
            ];

            notes.forEach(note => {
                let osc = ctx.createOscillator();
                let gain = ctx.createGain();
                osc.type = 'triangle'; // Bright mobile app tone
                osc.frequency.setValueAtTime(note.f, now + note.t);

                gain.gain.setValueAtTime(1.0, now + note.t);
                gain.gain.exponentialRampToValueAtTime(0.01, now + note.t + note.d);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start(now + note.t);
                osc.stop(now + note.t + note.d);
            });
        } catch(e) {}
    }

    // 📱 iPhone Radar / Apex Signature Arpeggio Tune
    function playIphoneRadarTune() {
        try {
            let ctx = new (window.AudioContext || window.webkitAudioContext)();
            let now = ctx.currentTime;

            let notes = [
                { f: 880.00,  t: 0.0,  d: 0.15 }, // A5
                { f: 1108.73, t: 0.12, d: 0.15 }, // C#6
                { f: 1318.51, t: 0.24, d: 0.15 }, // E6
                { f: 1760.00, t: 0.36, d: 0.30 }, // A6 Peak
                { f: 1318.51, t: 0.60, d: 0.15 }, // E6 Repeat
                { f: 1760.00, t: 0.72, d: 0.45 }  // A6 Final Peak
            ];

            notes.forEach(note => {
                let osc = ctx.createOscillator();
                let gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(note.f, now + note.t);

                gain.gain.setValueAtTime(1.0, now + note.t);
                gain.gain.exponentialRampToValueAtTime(0.01, now + note.t + note.d);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start(now + note.t);
                osc.stop(now + note.t + note.d);
            });
        } catch(e) {}
    }

    // 🎺 Upbeat Food Fanfare Victory Jingle
    function playFanfareJingle() {
        try {
            let ctx = new (window.AudioContext || window.webkitAudioContext)();
            let now = ctx.currentTime;

            let notes = [
                { f: 523.25,  t: 0.0,  d: 0.12 }, // C5
                { f: 659.25,  t: 0.12, d: 0.12 }, // E5
                { f: 783.99,  t: 0.24, d: 0.14 }, // G5
                { f: 1046.50, t: 0.38, d: 0.30 }, // C6 Peak
                { f: 783.99,  t: 0.62, d: 0.12 }, // G5 Repeat
                { f: 1046.50, t: 0.74, d: 0.50 }  // C6 Long
            ];

            notes.forEach(note => {
                let osc = ctx.createOscillator();
                let gain = ctx.createGain();
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(note.f, now + note.t);

                gain.gain.setValueAtTime(1.0, now + note.t);
                gain.gain.exponentialRampToValueAtTime(0.01, now + note.t + note.d);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start(now + note.t);
                osc.stop(now + note.t + note.d);
            });
        } catch(e) {}
    }

    // 🛎️ Rich Metallic Hotel Reception Desk Service Bell
    function playHotelServiceBell() {
        try {
            let ctx = new (window.AudioContext || window.webkitAudioContext)();
            let now = ctx.currentTime;

            [1046.50, 2093.00, 3139.50].forEach((freq, idx) => {
                let osc = ctx.createOscillator();
                let gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(freq, now);

                let vol = idx === 0 ? 1.0 : (0.4 / idx);
                gain.gain.setValueAtTime(vol, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 1.2);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start(now);
                osc.stop(now + 1.2);
            });
        } catch(e) {}
    }



    // 🔁 Continuous Ringing Loop Engine
    function startRingingLoop(unreceivedOrder) {
        if (!repeatRingingEnabled || !audioEnabled) return;

        currentlyRingingOrder = unreceivedOrder;
        document.getElementById('silence-ring-btn').classList.remove('hidden');

        // Play immediately
        playOrderAlert(currentlyRingingOrder);

        // Start repeating loop every 4.5 seconds until received/acknowledged
        if (!ringingLoopInterval) {
            ringingLoopInterval = setInterval(() => {
                if (currentlyRingingOrder && repeatRingingEnabled && audioEnabled) {
                    playOrderAlert(currentlyRingingOrder);
                } else {
                    stopRingingLoop();
                }
            }, 4500);
        }
    }

    function stopRingingLoop() {
        if (ringingLoopInterval) {
            clearInterval(ringingLoopInterval);
            ringingLoopInterval = null;
        }
        currentlyRingingOrder = null;
        if (window.speechSynthesis) window.speechSynthesis.cancel();
        document.getElementById('silence-ring-btn').classList.add('hidden');
    }

    function silenceRingingLoop() {
        stopRingingLoop();
    }

    // 🗣️ Voice AI Text-To-Speech (Max Loudness & High-Pitch Clarity)
    function playVoiceAlert(order = null) {
        if (!('speechSynthesis' in window)) {
            playBellChime();
            return;
        }

        window.speechSynthesis.cancel(); // Stop ongoing speech

        let tableName = order && order.table ? ('Table ' + order.table.name) : 'Walk in';
        let orderNum = order ? order.order_number : '1001';
        let orderType = order ? order.order_type : 'Dine-in';

        let speechText = customSpeechTemplate
            .replace('{table_name}', tableName)
            .replace('{order_number}', orderNum)
            .replace('{order_type}', orderType);

        let utterance = new SpeechSynthesisUtterance(speechText);
        utterance.volume = 1.0; // 100% Max Volume
        utterance.rate = 0.95;   // Clear, unhurried pronunciation
        utterance.pitch = 1.25;  // High pitch to cut through background kitchen noise
        utterance.lang = 'hi-IN'; // Supports Hindi / Hinglish & English
        
        window.speechSynthesis.speak(utterance);
    }

    // 🔔 Loud Dual-Harmonic Bell Chime
    function playBellChime() {
        try {
            let ctx = new (window.AudioContext || window.webkitAudioContext)();
            let now = ctx.currentTime;

            // Primary Bell Tone
            let osc1 = ctx.createOscillator();
            let gain1 = ctx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(987.77, now); // B5 note (high crisp bell)
            osc1.frequency.exponentialRampToValueAtTime(493.88, now + 0.5);
            gain1.gain.setValueAtTime(1.0, now); // MAX VOLUME 1.0
            gain1.gain.exponentialRampToValueAtTime(0.01, now + 0.5);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(now);
            osc1.stop(now + 0.5);

            // Secondary Octave Harmonic (Adds Volume & Punch)
            let osc2 = ctx.createOscillator();
            let gain2 = ctx.createGain();
            osc2.type = 'triangle';
            osc2.frequency.setValueAtTime(1318.51, now + 0.1); // E6 note
            osc2.frequency.exponentialRampToValueAtTime(659.25, now + 0.6);
            gain2.gain.setValueAtTime(0.8, now + 0.1);
            gain2.gain.exponentialRampToValueAtTime(0.01, now + 0.6);
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(now + 0.1);
            osc2.stop(now + 0.6);
        } catch(e) {}
    }

    // 🚨 Piercing High-Intensity Emergency Siren (Dual Sawtooth/Square Wave)
    function playEmergencySiren() {
        try {
            let ctx = new (window.AudioContext || window.webkitAudioContext)();
            let now = ctx.currentTime;

            [0, 0.2, 0.4].forEach(offset => {
                let osc = ctx.createOscillator();
                let gain = ctx.createGain();
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(1200, now + offset);
                osc.frequency.linearRampToValueAtTime(750, now + offset + 0.15);

                gain.gain.setValueAtTime(1.0, now + offset); // MAX VOLUME 1.0
                gain.gain.exponentialRampToValueAtTime(0.01, now + offset + 0.18);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start(now + offset);
                osc.stop(now + offset + 0.18);
            });
        } catch(e) {}
    }

    // 🎵 Loud Digital Double Ping
    function playDigitalPing() {
        try {
            let ctx = new (window.AudioContext || window.webkitAudioContext)();
            let now = ctx.currentTime;

            [0, 0.12].forEach(offset => {
                let osc = ctx.createOscillator();
                let gain = ctx.createGain();
                osc.type = 'square';
                osc.frequency.setValueAtTime(1400 + (offset * 1000), now + offset);
                gain.gain.setValueAtTime(0.9, now + offset);
                gain.gain.exponentialRampToValueAtTime(0.01, now + offset + 0.1);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start(now + offset);
                osc.stop(now + offset + 0.1);
            });
        } catch(e) {}
    }


    function testSoundAlert() {
        playOrderAlert({
            order_number: 'ORD-999',
            table: { name: '1' },
            order_type: 'Dine-in'
        });
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
        let latestNewOrder = null;
        
        let cols = {
            'Received': [],
            'Preparing': [],
            'Ready': []
        };

        orders.forEach(order => {
            if (!knownOrderIds.has(order.id)) {
                if (!isInitialLoad) {
                    newOrdersDetected = true;
                    latestNewOrder = order;
                }
                knownOrderIds.add(order.id);
            }
            if(cols[order.status]) {
                cols[order.status].push(order);
            }
        });

        // Check if there are unhandled orders waiting in 'Received' column (not yet moved to Preparation)
        if (cols['Received'].length > 0) {
            let topUnreceivedOrder = cols['Received'][0];
            if (!ringingLoopInterval && repeatRingingEnabled) {
                startRingingLoop(topUnreceivedOrder);
            }
        } else {
            // No pending orders in Received column (all moved to Preparation)! Stop ringing loop.
            stopRingingLoop();
        }


        isInitialLoad = false;

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

            // Stop ringing if updating a received order
            stopRingingLoop();
            fetchOrders(); // immediate refresh
        } catch(e) {
            alert('Failed to update status.');
        }
    }

    // Polling Loop
    setInterval(fetchOrders, 5000); // Poll every 5 seconds
    fetchOrders(); // Initial fetch
</script>
</body>
</html>
