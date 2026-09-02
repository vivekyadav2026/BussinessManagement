<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kitchen Display System (KDS) - Commercial Screen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0b0f19; }
        .kds-card { 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2); 
            backdrop-filter: blur(8px);
        }
        .blink { animation: blinker 1.2s linear infinite; }
        @keyframes blinker { 50% { opacity: 0.3; } }
        /* Custom Scrollbars */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="h-screen flex flex-col overflow-hidden text-slate-100 antialiased selection:bg-indigo-500 selection:text-white">

    <!-- Header Navigation Bar -->
    <header class="bg-slate-900/90 border-b border-slate-800/80 px-5 py-2 flex flex-wrap justify-between items-center shrink-0 gap-3 backdrop-blur-md">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-500 flex items-center justify-center font-black text-white text-base shadow-sm">
                🍳
            </div>
            <div>
                <h1 class="text-base font-black tracking-wider text-white uppercase flex items-center gap-2">
                    <span>Kitchen KDS</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded-full bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 uppercase tracking-widest">LIVE</span>
                </h1>
            </div>
            
            <span class="bg-slate-800 text-slate-300 border border-slate-700 text-xs font-bold px-2.5 py-1 rounded-xl">
                📍 {{ session('active_location_id') ? \App\Models\Location::find(session('active_location_id'))->name : 'Kitchen' }}
            </span>
            <div id="connection-status" class="flex items-center text-xs font-bold text-emerald-400 gap-1.5 bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-1 rounded-xl">
                <div class="w-2 h-2 rounded-full bg-emerald-400 blink"></div> Connected
            </div>
        </div>

        <!-- Audio Alert & Sound Settings Controls (Compact & Clean) -->
        <div class="flex items-center gap-2 text-xs font-medium flex-wrap">
            <span id="clock" class="font-extrabold text-slate-200 text-sm font-mono tracking-tight bg-slate-800/80 px-2.5 py-1 rounded-xl border border-slate-700/60"></span>
            
            <!-- Sound Alert Mode Selector -->
            <div class="flex items-center gap-1.5 bg-slate-800/90 border border-slate-700/80 px-2.5 py-1 rounded-xl shadow-xs">
                <span class="text-slate-400 font-bold text-[11px]">Ringtone:</span>
                <select id="sound-mode-select" onchange="setSoundMode(this.value)" class="bg-slate-950 text-white font-bold text-xs rounded-lg px-2 py-0.5 border border-slate-700 focus:outline-none focus:border-indigo-500 cursor-pointer">
                    <option value="zomato">🛵 Zomato / Swiggy Partner</option>
                    <option value="iphone">📱 iPhone Radar Tune</option>
                    <option value="fanfare">🎺 Food Fanfare Jingle</option>
                    <option value="hotel_bell">🛎️ Hotel Desk Bell</option>
                    <option value="voice">🗣️ Voice AI Speech</option>
                    <option value="siren">🚨 Emergency Siren</option>
                    <option value="bell">🔔 Bell Chime</option>
                    <option value="ping">🎵 Digital Ping</option>
                </select>
            </div>

            <!-- Custom Voice Message Template Input -->
            <div id="custom-speech-container" class="flex items-center gap-1.5 bg-slate-800/90 border border-slate-700/80 px-2.5 py-1 rounded-xl shadow-xs">
                <span class="text-slate-400 font-bold text-[11px]">Voice:</span>
                <input type="text" id="speech-template-input" onchange="saveSpeechTemplate(this.value)" class="bg-slate-950 text-white font-medium text-xs rounded-lg px-2 py-0.5 border border-slate-700 w-44 focus:outline-none focus:border-indigo-500" placeholder="Naya Order {table_name}">
            </div>

            <!-- Continuous Ringing Loop Toggle -->
            <label class="flex items-center gap-1.5 bg-slate-800/90 border border-slate-700/80 px-2.5 py-1 rounded-xl cursor-pointer hover:bg-slate-750 transition shadow-xs">
                <input type="checkbox" id="repeat-ring-checkbox" onchange="toggleRepeatRinging(this.checked)" class="rounded text-indigo-600 focus:ring-indigo-500 w-3.5 h-3.5 bg-slate-950 border-slate-700">
                <span class="text-slate-300 font-bold text-xs">🔁 Repeat</span>
            </label>

            <!-- Active Ringing Status & Silence Button -->
            <button id="silence-ring-btn" onclick="silenceRingingLoop()" class="hidden bg-rose-600 hover:bg-rose-700 text-white px-2.5 py-1 rounded-xl font-bold transition flex items-center gap-1 shadow-lg border border-rose-500 blink">
                ⏹️ Silence
            </button>

            <!-- Test Sound Button -->
            <button onclick="testSoundAlert()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-2.5 py-1 rounded-xl font-bold transition flex items-center gap-1 shadow-md border border-indigo-500">
                🔊 Test
            </button>

            <!-- Mute / Unmute Toggle -->
            <button onclick="toggleAudio()" id="audio-toggle" class="bg-slate-800 hover:bg-slate-700 px-2.5 py-1 rounded-xl border border-slate-700 transition flex items-center gap-1 shadow-xs">
                Sound: <span id="audio-status" class="text-emerald-400 font-bold">ON</span>
            </button>

            <a href="{{ route('organization.dashboard') }}" class="text-slate-400 hover:text-white transition font-bold px-1 text-xs">Exit</a>
        </div>
    </header>


    <!-- KDS Live Quick Order Summary Ribbon -->
    <div class="bg-slate-900/60 border-b border-slate-800/60 px-6 py-2 flex items-center justify-between gap-4 text-xs font-semibold shrink-0">
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
                <span class="text-slate-400 uppercase text-[10px] font-bold">New Pending Orders:</span>
                <span id="stat-received" class="px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30 font-extrabold font-mono">0</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400 uppercase text-[10px] font-bold">Currently Cooking:</span>
                <span id="stat-preparing" class="px-2.5 py-0.5 rounded-full bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 font-extrabold font-mono">0</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400 uppercase text-[10px] font-bold">Ready to Serve:</span>
                <span id="stat-ready" class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 font-extrabold font-mono">0</span>
            </div>
        </div>

        <div class="text-slate-400 text-[11px] font-medium hidden md:block">
            ⚡ Automatic real-time status update enabled (5s sync interval)
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="flex-grow flex gap-5 p-5 overflow-x-auto overflow-y-hidden bg-[#0b0f19]">
        
        <!-- RECEIVED COLUMN -->
        <div class="flex-1 min-w-[340px] bg-slate-900/80 rounded-2xl flex flex-col border border-slate-800/90 overflow-hidden shadow-xl">
            <div class="bg-gradient-to-r from-amber-950/80 to-amber-900/40 border-b border-amber-700/50 px-5 py-3.5 flex justify-between items-center">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-amber-400 shadow-sm blink"></span>
                    <h2 class="font-extrabold text-base text-amber-100 uppercase tracking-wider">1. RECEIVED</h2>
                </div>
                <span id="count-received" class="bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-black px-3 py-1 rounded-full font-mono">0</span>
            </div>
            <div id="col-received" class="flex-1 p-4 overflow-y-auto space-y-4">
                <!-- Cards injected here -->
            </div>
        </div>

        <!-- PREPARING COLUMN -->
        <div class="flex-1 min-w-[340px] bg-slate-900/80 rounded-2xl flex flex-col border border-slate-800/90 overflow-hidden shadow-xl">
            <div class="bg-gradient-to-r from-indigo-950/80 to-indigo-900/40 border-b border-indigo-700/50 px-5 py-3.5 flex justify-between items-center">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-indigo-400 shadow-sm"></span>
                    <h2 class="font-extrabold text-base text-indigo-100 uppercase tracking-wider">2. PREPARING</h2>
                </div>
                <span id="count-preparing" class="bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xs font-black px-3 py-1 rounded-full font-mono">0</span>
            </div>
            <div id="col-preparing" class="flex-1 p-4 overflow-y-auto space-y-4">
                <!-- Cards injected here -->
            </div>
        </div>

        <!-- READY COLUMN -->
        <div class="flex-1 min-w-[340px] bg-slate-900/80 rounded-2xl flex flex-col border border-slate-800/90 overflow-hidden shadow-xl">
            <div class="bg-gradient-to-r from-emerald-950/80 to-emerald-900/40 border-b border-emerald-700/50 px-5 py-3.5 flex justify-between items-center">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-400 shadow-sm"></span>
                    <h2 class="font-extrabold text-base text-emerald-100 uppercase tracking-wider">3. READY FOR PICKUP</h2>
                </div>
                <span id="count-ready" class="bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-black px-3 py-1 rounded-full font-mono">0</span>
            </div>
            <div id="col-ready" class="flex-1 p-4 overflow-y-auto space-y-4">
                <!-- Cards injected here -->
            </div>
        </div>
    </div>

<script>
    let knownOrderIds = new Set();
    let isInitialLoad = true;
    let audioEnabled = true;

    // Ringing Loop State
    let repeatRingingEnabled = localStorage.getItem('kds_repeat_ring') !== 'false';
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
        document.getElementById('audio-status').className = audioEnabled ? 'text-emerald-400 font-bold' : 'text-rose-400 font-bold';
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

            let notes = [
                { f: 1318.51, t: 0.0,  d: 0.12 },
                { f: 1567.98, t: 0.12, d: 0.12 },
                { f: 1975.53, t: 0.24, d: 0.14 },
                { f: 2637.02, t: 0.38, d: 0.35 },
                { f: 1567.98, t: 0.70, d: 0.12 },
                { f: 1975.53, t: 0.82, d: 0.12 },
                { f: 2637.02, t: 0.94, d: 0.45 }
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

    // 📱 iPhone Radar / Apex Signature Arpeggio Tune
    function playIphoneRadarTune() {
        try {
            let ctx = new (window.AudioContext || window.webkitAudioContext)();
            let now = ctx.currentTime;

            let notes = [
                { f: 880.00,  t: 0.0,  d: 0.15 },
                { f: 1108.73, t: 0.12, d: 0.15 },
                { f: 1318.51, t: 0.24, d: 0.15 },
                { f: 1760.00, t: 0.36, d: 0.30 },
                { f: 1318.51, t: 0.60, d: 0.15 },
                { f: 1760.00, t: 0.72, d: 0.45 }
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
                { f: 523.25,  t: 0.0,  d: 0.12 },
                { f: 659.25,  t: 0.12, d: 0.12 },
                { f: 783.99,  t: 0.24, d: 0.14 },
                { f: 1046.50, t: 0.38, d: 0.30 },
                { f: 783.99,  t: 0.62, d: 0.12 },
                { f: 1046.50, t: 0.74, d: 0.50 }
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

    // 🗣️ Voice AI Text-To-Speech
    function playVoiceAlert(order = null) {
        if (!('speechSynthesis' in window)) {
            playBellChime();
            return;
        }

        window.speechSynthesis.cancel();

        let tableName = order && order.table ? ('Table ' + order.table.name) : 'Walk in';
        let orderNum = order ? order.order_number : '1001';
        let orderType = order ? order.order_type : 'Dine-in';

        let speechText = customSpeechTemplate
            .replace('{table_name}', tableName)
            .replace('{order_number}', orderNum)
            .replace('{order_type}', orderType);

        let utterance = new SpeechSynthesisUtterance(speechText);
        utterance.volume = 1.0;
        utterance.rate = 0.95;
        utterance.pitch = 1.25;
        utterance.lang = 'hi-IN';
        
        window.speechSynthesis.speak(utterance);
    }

    // 🔔 Ding-Dong Bell Chime
    function playBellChime() {
        try {
            let ctx = new (window.AudioContext || window.webkitAudioContext)();
            let now = ctx.currentTime;

            let osc1 = ctx.createOscillator();
            let gain1 = ctx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(987.77, now);
            osc1.frequency.exponentialRampToValueAtTime(493.88, now + 0.5);
            gain1.gain.setValueAtTime(1.0, now);
            gain1.gain.exponentialRampToValueAtTime(0.01, now + 0.5);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(now);
            osc1.stop(now + 0.5);
        } catch(e) {}
    }

    // 🚨 Piercing Emergency Siren
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

                gain.gain.setValueAtTime(1.0, now + offset);
                gain.gain.exponentialRampToValueAtTime(0.01, now + offset + 0.18);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start(now + offset);
                osc.stop(now + offset + 0.18);
            });
        } catch(e) {}
    }

    // 🎵 Soft Digital Ping
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

    // 🔁 Continuous Ringing Loop Engine
    function startRingingLoop(unreceivedOrder) {
        if (!repeatRingingEnabled || !audioEnabled) return;

        currentlyRingingOrder = unreceivedOrder;
        document.getElementById('silence-ring-btn').classList.remove('hidden');

        playOrderAlert(currentlyRingingOrder);

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
        document.getElementById('clock').innerText = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', second:'2-digit'});
    }, 1000);

    // Fetch and render orders
    async function fetchOrders() {
        try {
            const response = await fetch('{{ route('organization.menu.kitchen.orders.fetch') }}');
            const orders = await response.json();
            renderOrders(orders);
        } catch (error) {
            console.error('KDS Fetch Error', error);
            document.getElementById('connection-status').innerHTML = '<div class="w-2 h-2 rounded-full bg-rose-500 blink"></div> Offline';
            document.getElementById('connection-status').className = "flex items-center text-xs font-bold text-rose-500 gap-2 bg-rose-500/10 border border-rose-500/20 px-3 py-1.5 rounded-xl";
        }
    }

    function renderOrders(orders) {
        cacheOrdersForModal(orders);
        document.getElementById('connection-status').innerHTML = '<div class="w-2 h-2 rounded-full bg-emerald-400 blink"></div> Connected';

        document.getElementById('connection-status').className = "flex items-center text-xs font-bold text-emerald-400 gap-2 bg-emerald-500/10 border border-emerald-500/20 px-3 py-1.5 rounded-xl";

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

        // Update Quick Stats Ribbon
        document.getElementById('stat-received').innerText = cols['Received'].length;
        document.getElementById('stat-preparing').innerText = cols['Preparing'].length;
        document.getElementById('stat-ready').innerText = cols['Ready'].length;

        // Check if there are unhandled orders waiting in 'Received' column (not yet moved to Preparation)
        if (cols['Received'].length > 0) {
            let topUnreceivedOrder = cols['Received'][0];
            if (!ringingLoopInterval && repeatRingingEnabled) {
                startRingingLoop(topUnreceivedOrder);
            }
        } else {
            stopRingingLoop();
        }

        isInitialLoad = false;

        // Render Columns
        renderCol('col-received', 'count-received', cols['Received'], 'Received');
        renderCol('col-preparing', 'count-preparing', cols['Preparing'], 'Preparing');
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

    // Helper: Elapsed Time Calculation (e.g. 4m ago)
    function getElapsedText(dateStr) {
        const orderTime = new Date(dateStr);
        const diffMs = new Date() - orderTime;
        const diffMins = Math.floor(diffMs / 60000);
        if (diffMins < 1) return '⏱️ Just now';
        return `⏱️ ${diffMins}m ago`;
    }

    function createOrderCard(order, type) {
        const div = document.createElement('div');
        
        // Dynamic Border Glow based on column status & delay
        let borderClasses = "border-slate-700/80 hover:border-slate-600";
        if (type === 'Received') borderClasses = "border-amber-500/50 hover:border-amber-400";
        else if (type === 'Preparing') borderClasses = "border-indigo-500/50 hover:border-indigo-400";
        else if (type === 'Ready') borderClasses = "border-emerald-500/50 hover:border-emerald-400";

        div.className = `bg-slate-800/95 border ${borderClasses} rounded-2xl p-4 text-white kds-card flex flex-col transition duration-200`;
        
        // Header
        let header = `<div class="flex justify-between items-start mb-3 border-b border-slate-700/70 pb-3">
            <div>
                <div class="font-black text-xl text-white tracking-tight flex items-center gap-2 cursor-pointer hover:text-indigo-400 transition" onclick="openOrderDetailsModal(${order.id})">
                    <span>${order.order_number}</span>
                    <span class="text-xs font-semibold text-indigo-400 bg-indigo-500/10 px-1.5 py-0.5 rounded border border-indigo-500/20">🔍 Details</span>
                </div>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-md ${
                        order.order_type === 'Dine-in' ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' :
                        order.order_type === 'Takeaway' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' :
                        'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30'
                    }">${order.order_type}</span>
                    <span class="text-[11px] font-bold text-slate-400 font-mono">${getElapsedText(order.created_at)}</span>
                </div>
            </div>
            <div class="text-right">`;
            
        if (order.table) {
            let tableNameStr = order.table.name.toLowerCase().startsWith('table') ? order.table.name : `Table ${order.table.name}`;
            header += `<div class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-black text-xs px-3 py-1 rounded-xl shadow-md uppercase tracking-wider">${tableNameStr}</div>`;
        } else {
            header += `<div class="bg-slate-700 text-slate-300 font-bold text-xs px-2.5 py-1 rounded-xl">Walk-in</div>`;
        }
        
        header += `<div class="text-[10px] font-mono text-slate-400 mt-1">${new Date(order.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
            </div>
        </div>`;


        // Body Items
        let body = `<div class="flex-grow space-y-2 mb-3">`;
        order.items.forEach(item => {
            body += `<div class="flex items-center justify-between text-sm font-semibold border-b border-slate-700/30 pb-1.5">
                <div class="flex items-center gap-2">
                    <span class="font-black text-indigo-400 bg-indigo-950/80 border border-indigo-500/30 px-2 py-0.5 rounded-md text-xs font-mono">${item.quantity}x</span>
                    <span class="text-slate-100">${item.name_snapshot}</span>
                </div>
            </div>`;
        });
        body += `</div>`;

        // Notes
        if (order.special_notes) {
            body += `<div class="bg-amber-500/10 border border-amber-500/30 text-amber-200 text-xs font-bold p-2.5 mb-3 rounded-xl flex items-start gap-1.5">
                <span>📝</span>
                <span>NOTE: ${order.special_notes}</span>
            </div>`;
        }

        // Action Buttons
        let actions = `<div class="flex gap-2 mt-auto border-t border-slate-700/70 pt-3">`;
        if (type === 'Received') {
            actions += `<button onclick="updateStatus(${order.id}, 'Preparing')" class="flex-1 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black py-2.5 rounded-xl shadow-lg transition flex items-center justify-center gap-1.5 text-xs uppercase tracking-wider">
                <span>🔥 Start Cooking</span>
            </button>`;
        } else if (type === 'Preparing') {
            actions += `<button onclick="updateStatus(${order.id}, 'Ready')" class="flex-1 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white font-black py-2.5 rounded-xl shadow-lg transition flex items-center justify-center gap-1.5 text-xs uppercase tracking-wider">
                <span>✅ Mark Ready</span>
            </button>`;
        } else if (type === 'Ready') {
            actions += `<button onclick="updateStatus(${order.id}, 'Served')" class="flex-1 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-black py-2.5 rounded-xl shadow-lg transition flex items-center justify-center gap-1.5 text-xs uppercase tracking-wider">
                <span>🛎️ Serve & Clear</span>
            </button>`;
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

            stopRingingLoop();
            fetchOrders();
        } catch(e) {
            alert('Failed to update status.');
        }
    }

    // Global Order Cache for Modal View
    let currentOrdersMap = new Map();

    // Store orders in map inside renderOrders
    function cacheOrdersForModal(orders) {
        currentOrdersMap.clear();
        orders.forEach(o => currentOrdersMap.set(o.id, o));
    }

    function openOrderDetailsModal(orderId) {
        const order = currentOrdersMap.get(orderId);
        if (!order) return;

        document.getElementById('modal-order-number').innerText = `Order ${order.order_number}`;
        document.getElementById('modal-order-date').innerText = `${new Date(order.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})} • ${new Date(order.created_at).toLocaleDateString()}`;
        
        document.getElementById('modal-customer-name').innerText = order.customer_name || 'Walk-in Customer';
        document.getElementById('modal-customer-phone').innerText = order.customer_phone ? `📞 ${order.customer_phone}` : '-';

        let tName = order.table ? (order.table.name.toLowerCase().startsWith('table') ? order.table.name : `Table ${order.table.name}`) : 'Walk-in / Takeaway';
        document.getElementById('modal-table-type').innerText = `${order.order_type} (${tName})`;

        let payBadge = order.payment_status === 'Paid' 
            ? `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Payment: PAID</span>`
            : `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">Payment: PENDING</span>`;
        document.getElementById('modal-payment-status').innerHTML = payBadge;

        // Render Items List
        let itemsHtml = '';
        order.items.forEach(item => {
            let unitPrice = item.price_snapshot || (item.total / (item.quantity || 1));
            itemsHtml += `<div class="p-3 flex justify-between items-center text-xs font-semibold">
                <div class="flex items-center gap-2">
                    <span class="font-black text-indigo-400 bg-indigo-950 px-2 py-0.5 rounded border border-indigo-500/30 font-mono">${item.quantity}x</span>
                    <span class="text-white font-bold">${item.name_snapshot}</span>
                </div>
                <div class="text-right font-mono">
                    <div class="text-slate-200 font-bold">₹${parseFloat(item.total).toFixed(2)}</div>
                    <div class="text-[10px] text-slate-500">₹${parseFloat(unitPrice).toFixed(2)} each</div>
                </div>
            </div>`;
        });
        document.getElementById('modal-items-list').innerHTML = itemsHtml;

        // Special Notes
        if (order.special_notes) {
            document.getElementById('modal-notes-box').classList.remove('hidden');
            document.getElementById('modal-notes-text').innerText = order.special_notes;
        } else {
            document.getElementById('modal-notes-box').classList.add('hidden');
        }

        // Financials
        document.getElementById('modal-subtotal').innerText = `₹${parseFloat(order.subtotal || 0).toFixed(2)}`;
        document.getElementById('modal-tax').innerText = `₹${parseFloat(order.tax || 0).toFixed(2)}`;
        document.getElementById('modal-total').innerText = `₹${parseFloat(order.total || 0).toFixed(2)}`;

        activeModalOrderId = order.id;
        document.getElementById('order-details-modal').classList.remove('hidden');
        document.getElementById('order-details-modal').classList.add('flex');
    }

    let activeModalOrderId = null;

    function printKotSlip() {
        if (activeModalOrderId) {
            window.open('/organization/menu/pos/orders/' + activeModalOrderId + '/print-kot', '_blank');
        }
    }

    function closeOrderDetailsModal() {
        document.getElementById('order-details-modal').classList.add('hidden');
        document.getElementById('order-details-modal').classList.remove('flex');
    }

    // Polling Loop
    setInterval(fetchOrders, 5000);
    fetchOrders();
</script>

<!-- Order Details Interactive Modal -->
<div id="order-details-modal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md hidden items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-700/80 rounded-3xl max-w-lg w-full shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <!-- Modal Header -->
        <div class="bg-slate-800/80 border-b border-slate-700/70 p-5 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-600/30 border border-indigo-500/40 flex items-center justify-center text-indigo-400 font-black text-xl">
                    📋
                </div>
                <div>
                    <h3 id="modal-order-number" class="text-xl font-black text-white tracking-tight">Order Details</h3>
                    <p id="modal-order-date" class="text-xs text-slate-400 font-mono"></p>
                </div>
            </div>
            <button onclick="closeOrderDetailsModal()" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center font-bold text-xl transition">&times;</button>
        </div>

        <!-- Modal Content Body -->
        <div class="p-6 overflow-y-auto space-y-4 text-xs">
            <!-- Customer Info & Payment Status -->
            <div class="grid grid-cols-2 gap-4 bg-slate-800/60 p-4 rounded-2xl border border-slate-700/50">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] block mb-0.5">Customer Info</span>
                    <div id="modal-customer-name" class="font-extrabold text-white text-sm">Walk-in</div>
                    <div id="modal-customer-phone" class="text-slate-400 font-mono text-[11px] mt-0.5">-</div>
                </div>
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] block mb-0.5">Dining & Status</span>
                    <div id="modal-table-type" class="font-extrabold text-indigo-300 text-sm">Dine-in</div>
                    <div id="modal-payment-status" class="mt-1"></div>
                </div>
            </div>

            <!-- Itemized KOT Dish Table -->
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Itemized Dish Summary</span>
                <div id="modal-items-list" class="bg-slate-950 border border-slate-800 rounded-2xl overflow-hidden divide-y divide-slate-800">
                </div>
            </div>

            <!-- Special Cooking Notes -->
            <div id="modal-notes-box" class="hidden bg-amber-500/10 border border-amber-500/30 text-amber-200 text-xs font-bold p-3 rounded-2xl">
                <span class="block text-[10px] uppercase font-bold text-amber-400 mb-1">Special Cooking Instructions</span>
                <p id="modal-notes-text" class="text-slate-200 font-medium"></p>
            </div>

            <!-- Financial Breakdown -->
            <div class="bg-slate-800/40 p-4 rounded-2xl border border-slate-700/40 space-y-2 font-medium">
                <div class="flex justify-between text-slate-400">
                    <span>Items Subtotal</span>
                    <span id="modal-subtotal" class="font-mono font-bold text-slate-200">₹0.00</span>
                </div>
                <div class="flex justify-between text-slate-400">
                    <span>GST Tax</span>
                    <span id="modal-tax" class="font-mono font-bold text-slate-200">₹0.00</span>
                </div>
                <div class="flex justify-between text-base font-black text-white pt-2 border-t border-slate-700/60">
                    <span>Total Bill</span>
                    <span id="modal-total" class="font-mono text-emerald-400 text-lg">₹0.00</span>
                </div>
            </div>
        </div>

        <!-- Modal Footer Actions -->
        <div class="bg-slate-800/80 border-t border-slate-700/70 p-4 flex justify-between items-center gap-3">
            <button onclick="printKotSlip()" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition flex items-center gap-1.5 border border-slate-700">
                🖨️ Print KOT Ticket
            </button>
            <button onclick="closeOrderDetailsModal()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-xl shadow-md transition">
                Close Details
            </button>
        </div>
    </div>
</div>

</body>
</html>

