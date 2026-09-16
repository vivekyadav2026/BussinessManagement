<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kitchen Display System (KDS) - Commercial Screen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #060913; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .kds-card { 
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.4); 
            backdrop-filter: blur(12px);
        }
        .blink { animation: blinker 1.2s linear infinite; }
        @keyframes blinker { 50% { opacity: 0.25; } }
        .urgent-pulse { animation: urgentGlow 1.5s infinite; }
        @keyframes urgentGlow {
            0%, 100% { border-color: rgba(244, 63, 94, 0.8); box-shadow: 0 0 15px rgba(244, 63, 94, 0.4); }
            50% { border-color: rgba(244, 63, 94, 0.3); box-shadow: 0 0 5px rgba(244, 63, 94, 0.1); }
        }
        /* Custom Scrollbars */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0b0f19; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="h-screen flex flex-col overflow-hidden text-slate-100 antialiased selection:bg-amber-500 selection:text-slate-950">

    <!-- 1. Top Cockpit Header Bar -->
    <header class="bg-slate-950/95 border-b border-slate-800/90 px-4 py-2.5 flex flex-wrap justify-between items-center shrink-0 gap-3 backdrop-blur-md z-30">
        <!-- Brand & Location & Status -->
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center font-black text-slate-950 text-lg shadow-md shadow-amber-500/20">
                🍳
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-sm font-black tracking-wider text-white uppercase flex items-center gap-1.5">
                        <span>Kitchen KDS</span>
                        <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-md bg-amber-500/20 text-amber-400 border border-amber-500/30 tracking-widest">LIVE</span>
                    </h1>
                    <span class="bg-slate-900 text-slate-300 border border-slate-800 text-[11px] font-bold px-2 py-0.5 rounded-lg flex items-center gap-1">
                        📍 {{ session('active_location_id') ? (\App\Models\Location::find(session('active_location_id'))->name ?? 'Main Kitchen') : 'Main Kitchen' }}
                    </span>
                </div>
                <div class="flex items-center gap-2 mt-0.5">
                    <div id="connection-status" class="flex items-center text-[10px] font-bold text-emerald-400 gap-1.5">
                        <div class="w-2 h-2 rounded-full bg-emerald-400 blink"></div> Live Connected (5s sync)
                    </div>
                </div>
            </div>
        </div>

        <!-- Center Chef Clock & Shift Summary Trigger -->
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 bg-slate-900/90 border border-slate-800 px-3 py-1 rounded-xl shadow-xs">
                <span class="text-amber-400 text-xs">🕒</span>
                <span id="clock" class="font-black text-slate-100 text-sm font-mono tracking-tight">--:--:--</span>
                <span id="current-date" class="text-[10px] font-bold text-slate-400 border-l border-slate-700 pl-2">--</span>
            </div>

            <button onclick="openTodaySummaryModal()" class="flex items-center gap-1.5 bg-slate-900 hover:bg-slate-800 text-slate-200 hover:text-white border border-slate-700/80 hover:border-slate-600 px-3 py-1.5 rounded-xl text-xs font-bold transition shadow-xs">
                <span>📊</span>
                <span>Shift Report</span>
            </button>
        </div>

        <!-- Right Sound & Navigation Actions -->
        <div class="flex items-center gap-2 text-xs font-medium">
            <!-- Active Ringing Status & Silence Button -->
            <button id="silence-ring-btn" onclick="silenceRingingLoop()" class="hidden bg-rose-600 hover:bg-rose-700 text-white px-3 py-1.5 rounded-xl font-black transition flex items-center gap-1.5 shadow-lg border border-rose-500 blink">
                ⏹️ Silence Alert
            </button>

            <!-- Sound Hub Modal Button -->
            <button onclick="openAudioSettingsModal()" class="bg-slate-900 hover:bg-slate-800 text-slate-200 border border-slate-800 px-2.5 py-1.5 rounded-xl transition flex items-center gap-1.5 font-bold shadow-xs">
                <span>🔊</span>
                <span>Audio Settings</span>
            </button>

            <!-- Fast Mute / Unmute Button -->
            <button onclick="toggleAudio()" id="audio-toggle" class="bg-slate-900 hover:bg-slate-800 px-2.5 py-1.5 rounded-xl border border-slate-800 transition flex items-center gap-1.5 shadow-xs">
                <span id="audio-icon">🔔</span>
                <span id="audio-status" class="text-emerald-400 font-extrabold text-[11px]">ON</span>
            </button>

            <!-- Fullscreen Button -->
            <button onclick="toggleFullscreen()" title="Toggle Fullscreen" class="p-1.5 bg-slate-900 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-800 rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0 0l-5-5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
            </button>

            <!-- POS Quick Links -->
            <div class="h-4 w-px bg-slate-800 mx-1"></div>

            <a href="{{ route('organization.menu.counter.index') }}" class="hidden sm:flex items-center gap-1 text-slate-400 hover:text-amber-400 px-2 py-1 transition font-bold text-xs">
                ⚡ Counter POS
            </a>
            <a href="{{ route('organization.menu.pos.index') }}" class="hidden sm:flex items-center gap-1 text-slate-400 hover:text-indigo-400 px-2 py-1 transition font-bold text-xs">
                🪑 Waiter POS
            </a>
            <a href="{{ route('organization.dashboard') }}" class="bg-slate-900 hover:bg-rose-950/60 hover:text-rose-300 text-slate-400 border border-slate-800 hover:border-rose-700/50 px-2.5 py-1.5 rounded-xl transition font-bold text-xs flex items-center gap-1">
                ✕ Exit
            </a>
        </div>
    </header>

    <!-- 2. Interactive Filter & Triage Strip -->
    <div class="bg-slate-950/80 border-b border-slate-800/80 px-4 py-2 flex flex-wrap items-center justify-between gap-3 text-xs shrink-0 z-20">
        <!-- Status Counters & Rush Alert -->
        <div class="flex items-center gap-3 flex-wrap">
            <!-- Received Pill -->
            <div class="flex items-center gap-2 bg-amber-500/10 border border-amber-500/30 px-2.5 py-1 rounded-lg">
                <span class="w-2 h-2 rounded-full bg-amber-400 blink"></span>
                <span class="text-amber-200 text-[11px] font-bold uppercase tracking-wider">Pending:</span>
                <span id="stat-received" class="font-mono font-black text-amber-300 text-xs">0</span>
            </div>

            <!-- Preparing Pill -->
            <div class="flex items-center gap-2 bg-indigo-500/10 border border-indigo-500/30 px-2.5 py-1 rounded-lg">
                <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                <span class="text-indigo-200 text-[11px] font-bold uppercase tracking-wider">Cooking:</span>
                <span id="stat-preparing" class="font-mono font-black text-indigo-300 text-xs">0</span>
            </div>

            <!-- Ready Pill -->
            <div class="flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/30 px-2.5 py-1 rounded-lg">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <span class="text-emerald-200 text-[11px] font-bold uppercase tracking-wider">Ready:</span>
                <span id="stat-ready" class="font-mono font-black text-emerald-300 text-xs">0</span>
            </div>

            <!-- Overdue Rush Alert Badge -->
            <div id="stat-overdue-container" class="hidden items-center gap-1.5 bg-rose-500/20 border border-rose-500/40 text-rose-300 px-2.5 py-1 rounded-lg font-black text-xs blink">
                <span>🔥</span>
                <span id="stat-overdue-count">0</span>
                <span>OVERDUE (>15m)</span>
            </div>
        </div>

        <!-- Filter Segmented Tabs & Search -->
        <div class="flex items-center gap-2 flex-wrap">
            <!-- Search Filter -->
            <div class="relative">
                <input type="text" id="kds-search-input" oninput="applyFilters()" placeholder="Search Token / Table / Dish..." class="bg-slate-900 border border-slate-800 rounded-lg pl-8 pr-3 py-1 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 w-44 sm:w-56 transition font-medium">
                <span class="absolute left-2.5 top-1.5 text-slate-500 text-xs">🔍</span>
                <button onclick="clearSearch()" id="search-clear-btn" class="hidden absolute right-2 top-1 text-slate-400 hover:text-white text-xs">&times;</button>
            </div>

            <!-- Dining Type Filter Buttons -->
            <div class="flex items-center bg-slate-900 border border-slate-800 p-0.5 rounded-lg">
                <button onclick="setFilterType('all')" id="filter-btn-all" class="px-2.5 py-1 rounded-md text-xs font-bold bg-amber-500 text-slate-950 transition">All</button>
                <button onclick="setFilterType('Dine-in')" id="filter-btn-dine" class="px-2.5 py-1 rounded-md text-xs font-bold text-slate-400 hover:text-white transition">🍽️ Dine-in</button>
                <button onclick="setFilterType('Takeaway')" id="filter-btn-takeaway" class="px-2.5 py-1 rounded-md text-xs font-bold text-slate-400 hover:text-white transition">🛍️ Takeaway</button>
                <button onclick="setFilterType('Delivery')" id="filter-btn-delivery" class="px-2.5 py-1 rounded-md text-xs font-bold text-slate-400 hover:text-white transition">🛵 Delivery</button>
            </div>
        </div>
    </div>

    <!-- 3. Kanban Columns Board -->
    <div class="flex-grow flex gap-4 p-4 overflow-x-auto overflow-y-hidden bg-[#060913]">
        
        <!-- COLUMN 1: RECEIVED / PENDING -->
        <div class="flex-1 min-w-[340px] max-w-[420px] bg-slate-950/80 rounded-xl flex flex-col border border-slate-800/90 overflow-hidden shadow-2xl">
            <!-- Column Header -->
            <div class="bg-gradient-to-r from-amber-950/90 via-amber-900/40 to-slate-950 border-b border-amber-500/40 px-4 py-3 flex justify-between items-center shrink-0">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-amber-400 blink"></span>
                    <h2 class="font-black text-sm text-amber-100 uppercase tracking-wider flex items-center gap-1.5">
                        <span>1. PENDING ORDER</span>
                    </h2>
                </div>
                <div class="flex items-center gap-2">
                    <span id="count-received" class="bg-amber-500 text-slate-950 text-xs font-black px-2.5 py-0.5 rounded-full font-mono shadow-xs">0</span>
                </div>
            </div>
            <!-- Cards Container -->
            <div id="col-received" class="flex-1 p-3 overflow-y-auto space-y-3">
                <!-- Dynamically injected -->
            </div>
        </div>

        <!-- COLUMN 2: PREPARING / COOKING -->
        <div class="flex-1 min-w-[340px] max-w-[420px] bg-slate-950/80 rounded-xl flex flex-col border border-slate-800/90 overflow-hidden shadow-2xl">
            <!-- Column Header -->
            <div class="bg-gradient-to-r from-indigo-950/90 via-indigo-900/40 to-slate-950 border-b border-indigo-500/40 px-4 py-3 flex justify-between items-center shrink-0">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-indigo-400"></span>
                    <h2 class="font-black text-sm text-indigo-100 uppercase tracking-wider flex items-center gap-1.5">
                        <span>2. COOKING IN PROGRESS</span>
                    </h2>
                </div>
                <div class="flex items-center gap-2">
                    <span id="count-preparing" class="bg-indigo-500 text-white text-xs font-black px-2.5 py-0.5 rounded-full font-mono shadow-xs">0</span>
                </div>
            </div>
            <!-- Cards Container -->
            <div id="col-preparing" class="flex-1 p-3 overflow-y-auto space-y-3">
                <!-- Dynamically injected -->
            </div>
        </div>

        <!-- COLUMN 3: READY FOR PICKUP -->
        <div class="flex-1 min-w-[340px] max-w-[420px] bg-slate-950/80 rounded-xl flex flex-col border border-slate-800/90 overflow-hidden shadow-2xl">
            <!-- Column Header -->
            <div class="bg-gradient-to-r from-emerald-950/90 via-emerald-900/40 to-slate-950 border-b border-emerald-500/40 px-4 py-3 flex justify-between items-center shrink-0">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                    <h2 class="font-black text-sm text-emerald-100 uppercase tracking-wider flex items-center gap-1.5">
                        <span>3. READY FOR PICKUP</span>
                    </h2>
                </div>
                <div class="flex items-center gap-2">
                    <span id="count-ready" class="bg-emerald-500 text-slate-950 text-xs font-black px-2.5 py-0.5 rounded-full font-mono shadow-xs">0</span>
                </div>
            </div>
            <!-- Cards Container -->
            <div id="col-ready" class="flex-1 p-3 overflow-y-auto space-y-3">
                <!-- Dynamically injected -->
            </div>
        </div>
    </div>

    <!-- 4. Audio Alert & Sound Hub Settings Modal -->
    <div id="audio-settings-modal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md hidden items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-700/80 rounded-xl max-w-md w-full shadow-2xl overflow-hidden flex flex-col animate-in fade-in zoom-in duration-150">
            <div class="bg-slate-800/90 border-b border-slate-700 px-5 py-3.5 flex justify-between items-center">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 border border-amber-500/30 flex items-center justify-center text-base font-bold">
                        🔊
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-white uppercase tracking-wider">Audio & Alert Hub</h3>
                        <p class="text-[11px] text-slate-400">Manage kitchen ringtones, voice notifications, and loop alerts</p>
                    </div>
                </div>
                <button onclick="closeAudioSettingsModal()" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center font-bold text-lg">&times;</button>
            </div>

            <div class="p-5 space-y-4 text-xs">
                <!-- Ringtone Selection -->
                <div>
                    <label class="font-extrabold text-slate-300 block mb-1.5 uppercase text-[10px] tracking-wider">Select Kitchen Ringtone</label>
                    <select id="modal-sound-mode-select" onchange="setSoundMode(this.value)" class="w-full bg-slate-950 text-white font-bold text-xs rounded-lg px-3 py-2 border border-slate-700 focus:outline-none focus:border-amber-500 cursor-pointer">
                        <option value="zomato">🛵 Zomato / Swiggy Partner App Signature Tune</option>
                        <option value="iphone">📱 iPhone Radar / Apex High Alert</option>
                        <option value="fanfare">🎺 Upbeat Victory Food Fanfare</option>
                        <option value="hotel_bell">🛎️ Metallic Hotel Reception Desk Bell</option>
                        <option value="voice">🗣️ AI Voice Speech (Hindi / English)</option>
                        <option value="siren">🚨 Emergency Kitchen Rush Siren</option>
                        <option value="bell">🔔 Crisp Service Ding-Dong Bell</option>
                        <option value="ping">🎵 Soft Digital Ping</option>
                    </select>
                </div>

                <!-- Custom Voice AI Template -->
                <div id="modal-speech-container" class="space-y-1">
                    <label class="font-extrabold text-slate-300 block uppercase text-[10px] tracking-wider">Voice Speech Template</label>
                    <input type="text" id="modal-speech-template-input" onchange="saveSpeechTemplate(this.value)" class="w-full bg-slate-950 text-white font-medium text-xs rounded-lg px-3 py-2 border border-slate-700 focus:outline-none focus:border-amber-500" placeholder="Naya Order Aaya Hai! {table_name}, Order {order_number}">
                    <p class="text-[10px] text-slate-400">Available variables: <span class="text-amber-400">{table_name}</span>, <span class="text-amber-400">{order_number}</span>, <span class="text-amber-400">{order_type}</span></p>
                </div>

                <!-- Repeat Ringing Toggle -->
                <label class="flex items-center justify-between p-3 rounded-lg bg-slate-950 border border-slate-800 cursor-pointer hover:bg-slate-850 transition">
                    <div>
                        <span class="text-slate-200 font-bold block">Repeat Alert Until Acknowledged</span>
                        <span class="text-slate-400 text-[11px]">Keeps ringing every 5s while new orders are waiting in Pending</span>
                    </div>
                    <input type="checkbox" id="modal-repeat-ring-checkbox" onchange="toggleRepeatRinging(this.checked)" class="rounded text-amber-500 focus:ring-amber-400 w-4 h-4 bg-slate-900 border-slate-700">
                </label>

                <!-- Test Alert Button -->
                <button onclick="testSoundAlert()" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold rounded-lg shadow-md transition flex items-center justify-center gap-2">
                    <span>🔔</span>
                    <span>Test Sound Ringtone Now</span>
                </button>
            </div>

            <div class="bg-slate-850 border-t border-slate-800 p-3 flex justify-end">
                <button onclick="closeAudioSettingsModal()" class="px-4 py-1.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-lg transition text-xs">
                    Done
                </button>
            </div>
        </div>
    </div>

    <!-- 5. Today's Shift Summary & Kitchen Report Modal -->
    <div id="today-summary-modal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md hidden items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-700/80 rounded-xl max-w-2xl w-full shadow-2xl overflow-hidden flex flex-col max-h-[85vh] animate-in fade-in zoom-in duration-150">
            <!-- Modal Header -->
            <div class="bg-slate-800/90 border-b border-slate-700 px-5 py-3.5 flex justify-between items-center shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center text-base font-bold">
                        📊
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-white uppercase tracking-wider">Today's Kitchen Shift Summary</h3>
                        <p id="shift-summary-date" class="text-[11px] text-slate-400 font-mono"></p>
                    </div>
                </div>
                <button onclick="closeTodaySummaryModal()" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center font-bold text-lg">&times;</button>
            </div>

            <!-- Modal Content -->
            <div class="p-5 overflow-y-auto space-y-4 text-xs">
                <!-- 3 KPI Metric Cards -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-slate-950 border border-slate-800 p-3.5 rounded-xl">
                        <span class="text-[10px] font-bold uppercase text-slate-400 block mb-1">Total Orders Cooked</span>
                        <div id="summary-total-orders" class="text-xl font-black text-amber-400 font-mono">0</div>
                    </div>
                    <div class="bg-slate-950 border border-slate-800 p-3.5 rounded-xl">
                        <span class="text-[10px] font-bold uppercase text-slate-400 block mb-1">Total Dish Portions</span>
                        <div id="summary-total-items" class="text-xl font-black text-indigo-400 font-mono">0</div>
                    </div>
                    <div class="bg-slate-950 border border-slate-800 p-3.5 rounded-xl">
                        <span class="text-[10px] font-bold uppercase text-slate-400 block mb-1">Kitchen Sales Value</span>
                        <div id="summary-total-revenue" class="text-xl font-black text-emerald-400 font-mono">₹0.00</div>
                    </div>
                </div>

                <!-- Item-wise Sales / Kitchen Cooking Breakdown -->
                <div>
                    <h4 class="text-xs font-black text-slate-200 uppercase tracking-wider mb-2 flex items-center justify-between">
                        <span>Dish Preparation Breakdown</span>
                        <span class="text-[10px] font-bold text-slate-400">Ranked by volume</span>
                    </h4>
                    <div class="bg-slate-950 border border-slate-800 rounded-xl overflow-hidden divide-y divide-slate-800">
                        <div class="grid grid-cols-12 px-3 py-2 bg-slate-900 text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">
                            <div class="col-span-7">Dish Name</div>
                            <div class="col-span-2 text-center">Portions</div>
                            <div class="col-span-3 text-right">Total Amount</div>
                        </div>
                        <div id="summary-items-list" class="divide-y divide-slate-800/60 max-h-60 overflow-y-auto">
                            <!-- Injected -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-slate-850 border-t border-slate-800 px-5 py-3 flex justify-between items-center shrink-0">
                <button onclick="fetchTodaySummary()" class="text-xs font-bold text-slate-400 hover:text-white flex items-center gap-1">
                    <span>↻</span> Refresh Stats
                </button>
                <button onclick="closeTodaySummaryModal()" class="px-4 py-1.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-lg text-xs transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- 6. Order Details Interactive Modal -->
    <div id="order-details-modal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md hidden items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-700/80 rounded-xl max-w-lg w-full shadow-2xl overflow-hidden flex flex-col max-h-[90vh] animate-in fade-in zoom-in duration-150">
            <!-- Modal Header -->
            <div class="bg-slate-800/90 border-b border-slate-700 p-4 flex justify-between items-center shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-indigo-600/30 border border-indigo-500/40 flex items-center justify-center text-indigo-400 font-black text-lg">
                        📋
                    </div>
                    <div>
                        <h3 id="modal-order-number" class="text-base font-black text-white tracking-tight">Order Details</h3>
                        <p id="modal-order-date" class="text-[11px] text-slate-400 font-mono"></p>
                    </div>
                </div>
                <button onclick="closeOrderDetailsModal()" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center font-bold text-lg transition">&times;</button>
            </div>

            <!-- Modal Content Body -->
            <div class="p-5 overflow-y-auto space-y-3.5 text-xs">
                <!-- Customer Info & Dining Type -->
                <div class="grid grid-cols-2 gap-3 bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] block mb-0.5">Customer</span>
                        <div id="modal-customer-name" class="font-extrabold text-white text-xs">Walk-in Guest</div>
                        <div id="modal-customer-phone" class="text-slate-400 font-mono text-[11px] mt-0.5">-</div>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] block mb-0.5">Dining Type & Table</span>
                        <div id="modal-table-type" class="font-extrabold text-indigo-300 text-xs">Dine-in</div>
                        <div id="modal-payment-status" class="mt-1"></div>
                    </div>
                </div>

                <!-- Itemized KOT Dish Table -->
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">Dish Breakdown</span>
                    <div id="modal-items-list" class="bg-slate-950 border border-slate-800 rounded-xl overflow-hidden divide-y divide-slate-800">
                    </div>
                </div>

                <!-- Special Cooking Notes -->
                <div id="modal-notes-box" class="hidden bg-amber-500/10 border border-amber-500/30 text-amber-200 text-xs font-bold p-3 rounded-xl">
                    <span class="block text-[10px] uppercase font-bold text-amber-400 mb-0.5">Special Cooking Instructions:</span>
                    <p id="modal-notes-text" class="text-slate-200 font-medium"></p>
                </div>

                <!-- Financial Breakdown -->
                <div class="bg-slate-950 p-3 rounded-xl border border-slate-800 space-y-1.5 font-medium">
                    <div class="flex justify-between text-slate-400">
                        <span>Items Subtotal</span>
                        <span id="modal-subtotal" class="font-mono font-bold text-slate-200">₹0.00</span>
                    </div>
                    <div class="flex justify-between text-slate-400">
                        <span>GST Tax</span>
                        <span id="modal-tax" class="font-mono font-bold text-slate-200">₹0.00</span>
                    </div>
                    <div class="flex justify-between text-sm font-black text-white pt-1.5 border-t border-slate-800">
                        <span>Total Bill</span>
                        <span id="modal-total" class="font-mono text-emerald-400 text-base font-black">₹0.00</span>
                    </div>
                </div>
            </div>

            <!-- Modal Footer Actions -->
            <div class="bg-slate-850 border-t border-slate-800 p-3.5 flex justify-between items-center gap-3 shrink-0">
                <button onclick="printKotSlip()" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-lg transition flex items-center gap-1.5 border border-slate-700">
                    <span>🖨️</span> Print KOT Slip
                </button>
                <button onclick="closeOrderDetailsModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-lg shadow-sm transition">
                    Close
                </button>
            </div>
        </div>
    </div>

<script>
    // State Variables
    let allOrders = [];
    let knownOrderIds = new Set();
    let isInitialLoad = true;
    let audioEnabled = true;
    let activeFilterType = 'all';
    let searchQuery = '';
    let struckItemsMap = new Set(JSON.parse(localStorage.getItem('kds_struck_items') || '[]'));

    // Ringing Loop State
    let repeatRingingEnabled = localStorage.getItem('kds_repeat_ring') !== 'false';
    let ringingLoopInterval = null;
    let currentlyRingingOrder = null;

    // Load preferences
    let soundMode = localStorage.getItem('kds_sound_mode') || 'zomato';
    let customSpeechTemplate = localStorage.getItem('kds_speech_template') || 'Naya Order Aaya Hai! {table_name}, Order {order_number}';

    document.addEventListener('DOMContentLoaded', () => {
        // Init clock
        updateClock();
        setInterval(updateClock, 1000);

        // Init sound config inputs
        const modeSelect = document.getElementById('modal-sound-mode-select');
        if (modeSelect) modeSelect.value = soundMode;
        
        const speechInput = document.getElementById('modal-speech-template-input');
        if (speechInput) speechInput.value = customSpeechTemplate;

        const repeatCheck = document.getElementById('modal-repeat-ring-checkbox');
        if (repeatCheck) repeatCheck.checked = repeatRingingEnabled;

        const speechContainer = document.getElementById('modal-speech-container');
        if (speechContainer) speechContainer.style.display = soundMode === 'voice' ? 'block' : 'none';

        // Initial fetch
        fetchOrders();
        setInterval(fetchOrders, 5000);
    });

    function updateClock() {
        const now = new Date();
        const clockEl = document.getElementById('clock');
        const dateEl = document.getElementById('current-date');
        if (clockEl) {
            clockEl.innerText = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', second:'2-digit'});
        }
        if (dateEl) {
            dateEl.innerText = now.toLocaleDateString([], {weekday: 'short', month: 'short', day: 'numeric'});
        }
    }

    // Toggle Fullscreen
    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => {});
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    }

    // Sound Hub Controls
    function openAudioSettingsModal() {
        document.getElementById('audio-settings-modal').classList.remove('hidden');
        document.getElementById('audio-settings-modal').classList.add('flex');
    }

    function closeAudioSettingsModal() {
        document.getElementById('audio-settings-modal').classList.add('hidden');
        document.getElementById('audio-settings-modal').classList.remove('flex');
    }

    function setSoundMode(mode) {
        soundMode = mode;
        localStorage.setItem('kds_sound_mode', mode);
        const speechContainer = document.getElementById('modal-speech-container');
        if (speechContainer) speechContainer.style.display = mode === 'voice' ? 'block' : 'none';
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
        const statusEl = document.getElementById('audio-status');
        const iconEl = document.getElementById('audio-icon');
        if (statusEl) {
            statusEl.innerText = audioEnabled ? 'ON' : 'OFF';
            statusEl.className = audioEnabled ? 'text-emerald-400 font-extrabold text-[11px]' : 'text-rose-400 font-extrabold text-[11px]';
        }
        if (iconEl) {
            iconEl.innerText = audioEnabled ? '🔔' : '🔕';
        }
        if (!audioEnabled) {
            stopRingingLoop();
        }
    }

    // Filters
    function setFilterType(type) {
        activeFilterType = type;
        const types = ['all', 'Dine-in', 'Takeaway', 'Delivery'];
        types.forEach(t => {
            const btnId = t === 'all' ? 'filter-btn-all' : (t === 'Dine-in' ? 'filter-btn-dine' : (t === 'Takeaway' ? 'filter-btn-takeaway' : 'filter-btn-delivery'));
            const btn = document.getElementById(btnId);
            if (btn) {
                if (t === type) {
                    btn.className = "px-2.5 py-1 rounded-md text-xs font-bold bg-amber-500 text-slate-950 transition shadow-xs";
                } else {
                    btn.className = "px-2.5 py-1 rounded-md text-xs font-bold text-slate-400 hover:text-white transition";
                }
            }
        });
        renderFilteredOrders();
    }

    function applyFilters() {
        searchQuery = (document.getElementById('kds-search-input').value || '').trim().toLowerCase();
        const clearBtn = document.getElementById('search-clear-btn');
        if (clearBtn) {
            clearBtn.classList.toggle('hidden', searchQuery.length === 0);
        }
        renderFilteredOrders();
    }

    function clearSearch() {
        document.getElementById('kds-search-input').value = '';
        applyFilters();
    }

    // Toggle individual dish item strike-through for kitchen plating
    function toggleItemStrike(event, itemKey) {
        event.stopPropagation();
        if (struckItemsMap.has(itemKey)) {
            struckItemsMap.delete(itemKey);
        } else {
            struckItemsMap.add(itemKey);
        }
        localStorage.setItem('kds_struck_items', JSON.stringify(Array.from(struckItemsMap)));
        
        const el = document.getElementById('item-row-' + itemKey);
        if (el) {
            const isStruck = struckItemsMap.has(itemKey);
            el.classList.toggle('opacity-40', isStruck);
            el.classList.toggle('line-through', isStruck);
            const checkEl = el.querySelector('.item-checkbox');
            if (checkEl) {
                checkEl.innerText = isStruck ? '✓' : '';
                checkEl.classList.toggle('bg-emerald-500', isStruck);
                checkEl.classList.toggle('border-emerald-400', isStruck);
            }
        }
    }

    // Fetch Orders from Backend
    async function fetchOrders() {
        try {
            const response = await fetch('{{ route('organization.menu.kitchen.orders.fetch') }}');
            const orders = await response.json();
            allOrders = orders;
            processAndRender(orders);
        } catch (error) {
            console.error('KDS Fetch Error', error);
            const statusEl = document.getElementById('connection-status');
            if (statusEl) {
                statusEl.innerHTML = '<div class="w-2 h-2 rounded-full bg-rose-500 blink"></div> Reconnecting...';
                statusEl.className = "flex items-center text-[10px] font-bold text-rose-400 gap-1.5";
            }
        }
    }

    function processAndRender(orders) {
        cacheOrdersForModal(orders);

        const statusEl = document.getElementById('connection-status');
        if (statusEl) {
            statusEl.innerHTML = '<div class="w-2 h-2 rounded-full bg-emerald-400 blink"></div> Live Connected (5s sync)';
            statusEl.className = "flex items-center text-[10px] font-bold text-emerald-400 gap-1.5";
        }

        let newOrdersDetected = false;
        let latestNewOrder = null;

        orders.forEach(order => {
            if (!knownOrderIds.has(order.id)) {
                if (!isInitialLoad) {
                    newOrdersDetected = true;
                    latestNewOrder = order;
                }
                knownOrderIds.add(order.id);
            }
        });

        // If newly detected order
        if (newOrdersDetected && latestNewOrder) {
            playOrderAlert(latestNewOrder);
        }

        // Check if unhandled orders in Received
        const receivedOrders = orders.filter(o => o.status === 'Received');
        if (receivedOrders.length > 0) {
            let topUnreceivedOrder = receivedOrders[0];
            if (!ringingLoopInterval && repeatRingingEnabled) {
                startRingingLoop(topUnreceivedOrder);
            }
        } else {
            stopRingingLoop();
        }

        isInitialLoad = false;
        renderFilteredOrders();
    }

    function renderFilteredOrders() {
        let filtered = allOrders.filter(order => {
            // Type filter
            if (activeFilterType !== 'all' && order.order_type !== activeFilterType) {
                return false;
            }
            // Search query
            if (searchQuery.length > 0) {
                const orderNum = (order.order_number || '').toLowerCase();
                const tableName = order.table ? (order.table.name || '').toLowerCase() : '';
                const customer = (order.customer_name || '').toLowerCase();
                const hasItem = (order.items || []).some(item => (item.name_snapshot || '').toLowerCase().includes(searchQuery));
                if (!orderNum.includes(searchQuery) && !tableName.includes(searchQuery) && !customer.includes(searchQuery) && !hasItem) {
                    return false;
                }
            }
            return true;
        });

        let cols = {
            'Received': [],
            'Preparing': [],
            'Ready': []
        };

        let overdueCount = 0;
        const nowMs = new Date().getTime();

        allOrders.forEach(order => {
            if (cols[order.status]) {
                // Count overdue (> 15m)
                const createdMs = new Date(order.created_at).getTime();
                const diffMins = Math.floor((nowMs - createdMs) / 60000);
                if (diffMins >= 15 && order.status !== 'Ready') {
                    overdueCount++;
                }
            }
        });

        filtered.forEach(order => {
            if (cols[order.status]) {
                cols[order.status].push(order);
            }
        });

        // Update counts in header strip
        document.getElementById('stat-received').innerText = allOrders.filter(o => o.status === 'Received').length;
        document.getElementById('stat-preparing').innerText = allOrders.filter(o => o.status === 'Preparing').length;
        document.getElementById('stat-ready').innerText = allOrders.filter(o => o.status === 'Ready').length;

        const overdueContainer = document.getElementById('stat-overdue-container');
        const overdueCountEl = document.getElementById('stat-overdue-count');
        if (overdueCount > 0) {
            overdueContainer.classList.remove('hidden');
            overdueContainer.classList.add('flex');
            overdueCountEl.innerText = overdueCount;
        } else {
            overdueContainer.classList.add('hidden');
            overdueContainer.classList.remove('flex');
        }

        renderCol('col-received', 'count-received', cols['Received'], 'Received');
        renderCol('col-preparing', 'count-preparing', cols['Preparing'], 'Preparing');
        renderCol('col-ready', 'count-ready', cols['Ready'], 'Ready');
    }

    function renderCol(colId, countId, orders, type) {
        const col = document.getElementById(colId);
        document.getElementById(countId).innerText = orders.length;
        col.innerHTML = '';

        if (orders.length === 0) {
            col.innerHTML = `<div class="h-44 flex flex-col items-center justify-center text-slate-500 text-xs border border-dashed border-slate-800/80 rounded-xl p-4 text-center">
                <span class="text-2xl mb-1">${type === 'Received' ? '✨' : (type === 'Preparing' ? '🍳' : '🛎️')}</span>
                <span class="font-bold text-slate-400">No tickets in ${type}</span>
                <span class="text-[11px] text-slate-600 mt-0.5">Kitchen station is clear</span>
            </div>`;
            return;
        }

        orders.forEach(order => {
            col.appendChild(createOrderCard(order, type));
        });
    }

    function getElapsedInfo(dateStr) {
        const orderTime = new Date(dateStr);
        const diffMs = new Date() - orderTime;
        const diffMins = Math.floor(diffMs / 60000);
        
        if (diffMins < 1) {
            return { text: 'Just now', isOverdue: false, isWarning: false };
        } else if (diffMins < 10) {
            return { text: `${diffMins}m ago`, isOverdue: false, isWarning: false };
        } else if (diffMins < 18) {
            return { text: `${diffMins}m ago`, isOverdue: false, isWarning: true };
        } else {
            return { text: `🔥 ${diffMins}m OVERDUE`, isOverdue: true, isWarning: true };
        }
    }

    function createOrderCard(order, type) {
        const div = document.createElement('div');
        const elapsed = getElapsedInfo(order.created_at);

        // Dynamic Border Glow based on column status & delay
        let borderClasses = "border-slate-800 hover:border-slate-700";
        if (elapsed.isOverdue && type !== 'Ready') {
            borderClasses = "urgent-pulse border-rose-500 bg-rose-950/20";
        } else if (type === 'Received') {
            borderClasses = "border-amber-500/40 hover:border-amber-400";
        } else if (type === 'Preparing') {
            borderClasses = "border-indigo-500/40 hover:border-indigo-400";
        } else if (type === 'Ready') {
            borderClasses = "border-emerald-500/40 hover:border-emerald-400";
        }

        div.className = `bg-slate-900/95 border ${borderClasses} rounded-xl p-3.5 text-white kds-card flex flex-col transition duration-150`;

        // Card Header: Order #, Table Badge, Elapsed Timer
        let tableNameStr = order.table ? (order.table.name.toLowerCase().startsWith('table') ? order.table.name : `Table ${order.table.name}`) : 'Walk-in';

        let diningBadgeClass = order.order_type === 'Dine-in' 
            ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' 
            : (order.order_type === 'Takeaway' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30');

        let timerClass = elapsed.isOverdue 
            ? 'bg-rose-500 text-white font-black animate-pulse px-2 py-0.5 rounded-md text-[10px]' 
            : (elapsed.isWarning ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30 px-2 py-0.5 rounded-md text-[10px]' : 'text-slate-400 text-[11px]');

        let header = `
            <div class="flex justify-between items-start mb-2.5 border-b border-slate-800 pb-2.5">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-black text-base text-white tracking-tight cursor-pointer hover:text-amber-400 transition" onclick="openOrderDetailsModal(${order.id})">
                            ${order.order_number}
                        </span>
                        <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-md ${diningBadgeClass}">
                            ${order.order_type}
                        </span>
                    </div>
                    <div class="flex items-center gap-1.5 mt-1 font-mono">
                        <span class="${timerClass}">⏱️ ${elapsed.text}</span>
                        <span class="text-[10px] text-slate-500">• ${new Date(order.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                    </div>
                </div>

                <div class="text-right">
                    <div class="bg-indigo-600/90 text-white font-black text-xs px-2.5 py-1 rounded-lg shadow-xs uppercase tracking-wider">
                        ${tableNameStr}
                    </div>
                    ${order.customer_name && order.customer_name !== 'Guest' ? `<div class="text-[10px] font-bold text-slate-400 mt-1 max-w-[120px] truncate">${order.customer_name}</div>` : ''}
                </div>
            </div>
        `;

        // Card Items Body with Veg / Non-Veg Indicator & Line-Item Strike
        let body = `<div class="flex-grow space-y-1.5 mb-3">`;
        (order.items || []).forEach(item => {
            const itemKey = `${order.id}_${item.id}`;
            const isStruck = struckItemsMap.has(itemKey);
            
            // Veg / Non-Veg Badge
            let isVeg = true;
            if (item.menu_item && typeof item.menu_item.is_veg !== 'undefined') {
                isVeg = item.menu_item.is_veg;
            }
            let vegBadge = isVeg 
                ? `<span title="Vegetarian" class="w-3.5 h-3.5 rounded border border-emerald-500 flex items-center justify-center p-0.5 shrink-0"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span></span>`
                : `<span title="Non-Vegetarian" class="w-3.5 h-3.5 rounded border border-rose-500 flex items-center justify-center p-0.5 shrink-0"><span class="w-1.5 h-1.5 bg-rose-500 rotate-45"></span></span>`;

            body += `
                <div id="item-row-${itemKey}" onclick="toggleItemStrike(event, '${itemKey}')" class="flex items-center justify-between text-xs font-semibold p-1.5 rounded-lg hover:bg-slate-800/80 cursor-pointer select-none transition border border-transparent hover:border-slate-700/60 ${isStruck ? 'opacity-40 line-through' : ''}">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="item-checkbox w-3.5 h-3.5 rounded border border-slate-600 flex items-center justify-center text-[9px] font-bold text-slate-950 ${isStruck ? 'bg-emerald-500 border-emerald-400' : ''}">
                            ${isStruck ? '✓' : ''}
                        </div>
                        ${vegBadge}
                        <span class="font-black text-amber-400 bg-amber-950/80 border border-amber-500/30 px-1.5 py-0.5 rounded text-[11px] font-mono shrink-0">${item.quantity}x</span>
                        <span class="text-slate-100 font-bold truncate">${item.name_snapshot}</span>
                    </div>
                </div>
            `;
        });
        body += `</div>`;

        // Special Cooking Notes Banner
        if (order.special_notes) {
            body += `
                <div class="bg-amber-500/10 border border-amber-500/30 text-amber-200 text-xs font-bold p-2 mb-2.5 rounded-lg flex items-start gap-1.5">
                    <span class="text-sm shrink-0">⚠️</span>
                    <span class="leading-tight"><strong class="uppercase text-[10px] text-amber-400 block">Note:</strong>${order.special_notes}</span>
                </div>
            `;
        }

        // Action Buttons
        let actions = `<div class="mt-auto border-t border-slate-800 pt-2.5 flex items-center gap-2">`;
        
        // Print KOT quick button
        actions += `
            <button onclick="quickPrintKot(${order.id})" title="Print KOT Slip" class="p-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg border border-slate-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            </button>
        `;

        if (type === 'Received') {
            actions += `
                <button onclick="updateStatus(${order.id}, 'Preparing')" class="flex-1 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-black py-2 rounded-lg shadow-sm transition flex items-center justify-center gap-1.5 text-xs uppercase tracking-wider">
                    <span>🔥 Start Cooking</span>
                </button>
            `;
        } else if (type === 'Preparing') {
            // Recall button to Received
            actions += `
                <button onclick="updateStatus(${order.id}, 'Received')" title="Move back to Pending" class="px-2.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white rounded-lg border border-slate-700 transition text-xs font-bold">
                    ↩
                </button>
                <button onclick="updateStatus(${order.id}, 'Ready')" class="flex-1 bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-slate-950 font-black py-2 rounded-lg shadow-sm transition flex items-center justify-center gap-1.5 text-xs uppercase tracking-wider">
                    <span>✅ Mark Ready</span>
                </button>
            `;
        } else if (type === 'Ready') {
            // Recall button to Preparing
            actions += `
                <button onclick="updateStatus(${order.id}, 'Preparing')" title="Move back to Cooking" class="px-2.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white rounded-lg border border-slate-700 transition text-xs font-bold">
                    ↩
                </button>
                <button onclick="updateStatus(${order.id}, 'Served')" class="flex-1 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-black py-2 rounded-lg shadow-sm transition flex items-center justify-center gap-1.5 text-xs uppercase tracking-wider">
                    <span>🛎️ Served & Clear</span>
                </button>
            `;
        }

        actions += `</div>`;

        div.innerHTML = header + body + actions;
        return div;
    }

    // Status Updater
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
            alert('Failed to update kitchen status. Please check your network connection.');
        }
    }

    function quickPrintKot(orderId) {
        window.open('/organization/menu/pos/orders/' + orderId + '/print-kot', '_blank');
    }

    // Modal Cache & View
    let currentOrdersMap = new Map();
    function cacheOrdersForModal(orders) {
        currentOrdersMap.clear();
        orders.forEach(o => currentOrdersMap.set(o.id, o));
    }

    let activeModalOrderId = null;
    function openOrderDetailsModal(orderId) {
        const order = currentOrdersMap.get(orderId);
        if (!order) return;

        document.getElementById('modal-order-number').innerText = `Order ${order.order_number}`;
        document.getElementById('modal-order-date').innerText = `${new Date(order.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})} • ${new Date(order.created_at).toLocaleDateString()}`;
        
        document.getElementById('modal-customer-name').innerText = order.customer_name || 'Walk-in Guest';
        document.getElementById('modal-customer-phone').innerText = order.customer_phone ? `📞 ${order.customer_phone}` : '-';

        let tName = order.table ? (order.table.name.toLowerCase().startsWith('table') ? order.table.name : `Table ${order.table.name}`) : 'Walk-in / Takeaway';
        document.getElementById('modal-table-type').innerText = `${order.order_type} (${tName})`;

        let payBadge = order.payment_status === 'Paid' 
            ? `<span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Payment: PAID</span>`
            : `<span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">Payment: PENDING</span>`;
        document.getElementById('modal-payment-status').innerHTML = payBadge;

        // Render Items List
        let itemsHtml = '';
        (order.items || []).forEach(item => {
            let unitPrice = item.price_snapshot || (item.total / (item.quantity || 1));
            let isVeg = true;
            if (item.menu_item && typeof item.menu_item.is_veg !== 'undefined') {
                isVeg = item.menu_item.is_veg;
            }
            let vegBadge = isVeg 
                ? `<span class="w-3.5 h-3.5 rounded border border-emerald-500 flex items-center justify-center p-0.5 shrink-0"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span></span>`
                : `<span class="w-3.5 h-3.5 rounded border border-rose-500 flex items-center justify-center p-0.5 shrink-0"><span class="w-1.5 h-1.5 bg-rose-500 rotate-45"></span></span>`;

            itemsHtml += `
                <div class="p-2.5 flex justify-between items-center text-xs font-semibold">
                    <div class="flex items-center gap-2">
                        ${vegBadge}
                        <span class="font-black text-amber-400 bg-amber-950 px-1.5 py-0.5 rounded border border-amber-500/30 font-mono">${item.quantity}x</span>
                        <span class="text-white font-bold">${item.name_snapshot}</span>
                    </div>
                    <div class="text-right font-mono">
                        <div class="text-slate-200 font-bold">₹${parseFloat(item.total).toFixed(2)}</div>
                        <div class="text-[10px] text-slate-500">₹${parseFloat(unitPrice).toFixed(2)} each</div>
                    </div>
                </div>
            `;
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

    function printKotSlip() {
        if (activeModalOrderId) {
            quickPrintKot(activeModalOrderId);
        }
    }

    function closeOrderDetailsModal() {
        document.getElementById('order-details-modal').classList.add('hidden');
        document.getElementById('order-details-modal').classList.remove('flex');
    }

    // Today's Shift Summary Modal
    async function openTodaySummaryModal() {
        document.getElementById('today-summary-modal').classList.remove('hidden');
        document.getElementById('today-summary-modal').classList.add('flex');
        document.getElementById('shift-summary-date').innerText = new Date().toLocaleDateString([], {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'});
        await fetchTodaySummary();
    }

    function closeTodaySummaryModal() {
        document.getElementById('today-summary-modal').classList.add('hidden');
        document.getElementById('today-summary-modal').classList.remove('flex');
    }

    async function fetchTodaySummary() {
        try {
            const res = await fetch('{{ route('organization.menu.kitchen.today-summary') }}');
            const data = await res.json();

            document.getElementById('summary-total-orders').innerText = data.total_orders || 0;
            document.getElementById('summary-total-items').innerText = data.total_items_sold || 0;
            document.getElementById('summary-total-revenue').innerText = '₹' + parseFloat(data.total_revenue || 0).toFixed(2);

            let html = '';
            if (data.items && data.items.length > 0) {
                data.items.forEach(it => {
                    html += `
                        <div class="grid grid-cols-12 px-3 py-2 items-center text-xs font-semibold hover:bg-slate-900/60">
                            <div class="col-span-7 font-bold text-white">${it.name_snapshot}</div>
                            <div class="col-span-2 text-center font-mono font-black text-amber-400">${it.total_quantity}</div>
                            <div class="col-span-3 text-right font-mono text-emerald-400 font-bold">₹${parseFloat(it.total_amount).toFixed(2)}</div>
                        </div>
                    `;
                });
            } else {
                html = `<div class="p-4 text-center text-slate-500 font-medium">No dishes cooked yet today</div>`;
            }
            document.getElementById('summary-items-list').innerHTML = html;
        } catch (e) {
            console.error('Summary Error', e);
        }
    }

    // Sound alert engines
    function playOrderAlert(latestOrder = null) {
        if (!audioEnabled) return;

        if (soundMode === 'zomato') playZomatoStyleChime();
        else if (soundMode === 'iphone') playIphoneRadarTune();
        else if (soundMode === 'fanfare') playFanfareJingle();
        else if (soundMode === 'hotel_bell') playHotelServiceBell();
        else if (soundMode === 'voice') playVoiceAlert(latestOrder);
        else if (soundMode === 'bell') playBellChime();
        else if (soundMode === 'siren') playEmergencySiren();
        else if (soundMode === 'ping') playDigitalPing();
    }

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

    // Continuous Ringing Loop
    function startRingingLoop(unreceivedOrder) {
        if (!repeatRingingEnabled || !audioEnabled) return;
        currentlyRingingOrder = unreceivedOrder;
        const silenceBtn = document.getElementById('silence-ring-btn');
        if (silenceBtn) silenceBtn.classList.remove('hidden');

        playOrderAlert(currentlyRingingOrder);

        if (!ringingLoopInterval) {
            ringingLoopInterval = setInterval(() => {
                if (currentlyRingingOrder && repeatRingingEnabled && audioEnabled) {
                    playOrderAlert(currentlyRingingOrder);
                } else {
                    stopRingingLoop();
                }
            }, 5000);
        }
    }

    function stopRingingLoop() {
        if (ringingLoopInterval) {
            clearInterval(ringingLoopInterval);
            ringingLoopInterval = null;
        }
        currentlyRingingOrder = null;
        if (window.speechSynthesis) window.speechSynthesis.cancel();
        const silenceBtn = document.getElementById('silence-ring-btn');
        if (silenceBtn) silenceBtn.classList.add('hidden');
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
</script>

</body>
</html>
