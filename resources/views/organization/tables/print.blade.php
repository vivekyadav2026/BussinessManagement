<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Table QR Standees - {{ auth()->user()->organization->name ?? 'Restaurant' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f8fafc;
            color: #020617;
        }
        @media print {
            .no-print { display: none !important; }
            body { 
                background: #ffffff !important; 
                padding: 0 !important;
                margin: 0 !important;
            }
            .print-container {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
                gap: 16px !important;
            }
            .standee-card {
                page-break-inside: avoid;
                break-inside: avoid;
                box-shadow: none !important;
                border: 2px dashed #64748b !important;
            }
            @page {
                size: auto;
                margin: 10mm;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-8">

    <!-- Top Non-Print Screen Control Bar -->
    <header class="no-print max-w-6xl mx-auto mb-8 bg-white border border-slate-200/90 rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('organization.menu.tables.index') }}" 
               class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition flex items-center gap-1.5">
                <span>&larr;</span>
                <span>Back to Tables</span>
            </a>
            <div>
                <h1 class="text-base font-black text-slate-950">Table QR Standees & Tent Cards</h1>
                <p class="text-xs text-slate-500 font-medium">Ready for print on standard A4 or card stock paper ({{ $tables->count() }} {{ $tables->count() === 1 ? 'card' : 'cards' }})</p>
            </div>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
            <span class="text-xs text-slate-500 font-medium hidden md:inline">💡 Print in color or B&W</span>
            <button onclick="window.print()" 
                    class="px-5 py-2.5 bg-amber-500 hover:bg-amber-400 active:bg-amber-600 text-slate-950 font-black rounded-xl text-xs uppercase tracking-wider shadow-md transition flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print Standees / Save PDF</span>
            </button>
        </div>
    </header>

    <!-- Standee Cards Grid -->
    <div class="print-container max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 print:grid-cols-2 print:gap-6">
        @foreach($tables as $table)
            @php
                $tablePublicUrl = route('public.menu.table', $table->public_token);
            @endphp
            <div class="standee-card bg-white border-2 border-slate-900 rounded-3xl p-6 flex flex-col items-center justify-between text-center relative shadow-md transition">
                
                <!-- Card Header with Restaurant Branding -->
                <div class="w-full pb-4 border-b border-slate-100 flex flex-col items-center space-y-1.5">
                    @if($table->organization && $table->organization->logo)
                        <img src="{{ asset('storage/' . $table->organization->logo) }}" alt="{{ $table->organization->name }}" class="h-10 w-10 object-cover rounded-xl border border-slate-200 shadow-2xs mb-1">
                    @else
                        <div class="w-10 h-10 rounded-xl bg-slate-950 text-amber-400 flex items-center justify-center font-black text-base shadow-xs mb-1">
                            🍽️
                        </div>
                    @endif
                    <h2 class="text-sm font-black text-slate-950 uppercase tracking-tight leading-tight">
                        {{ $table->organization->name ?? 'RESTAURANT DINE-IN' }}
                    </h2>
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-md">
                        <span>📍</span> {{ $table->location->name ?? 'Main Outlet' }}
                    </span>
                </div>

                <!-- Table Identifier Badge -->
                <div class="my-4">
                    <span class="inline-block px-5 py-2 rounded-2xl bg-slate-950 text-amber-400 font-mono font-black text-xl tracking-wide uppercase shadow-sm border border-slate-800">
                        {{ $table->name }}
                    </span>
                </div>

                <!-- High-Resolution QR Code Frame -->
                <div class="p-4 bg-slate-50 border-2 border-slate-200 rounded-2xl shadow-inner flex flex-col items-center justify-center my-2 w-full max-w-[240px]">
                    <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-xs">
                        {!! QrCode::size(175)->margin(1)->generate($tablePublicUrl) !!}
                    </div>
                    <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest mt-2.5 font-mono">
                        SCAN TO ORDER
                    </span>
                </div>

                <!-- Guest Instructions -->
                <div class="w-full pt-3 mt-2 border-t border-slate-100 space-y-1">
                    <div class="flex items-center justify-center gap-3 text-[10px] font-black uppercase text-slate-700 tracking-wider">
                        <span>1. Open Camera</span>
                        <span>&bull;</span>
                        <span>2. Scan Code</span>
                        <span>&bull;</span>
                        <span>3. Order Food</span>
                    </div>
                    <p class="text-[10px] text-slate-400 font-medium">
                        Contactless Digital Menu &bull; Table Token: <span class="font-mono font-bold">{{ substr($table->public_token, 0, 8) }}</span>
                    </p>
                </div>

                <!-- Cut line indicator on print -->
                <span class="no-print absolute top-2 right-2 text-[9px] text-slate-300 font-mono">✂ Standee Cutout</span>
            </div>
        @endforeach
    </div>

</body>
</html>
