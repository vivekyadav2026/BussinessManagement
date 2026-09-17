<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Submitted - Support</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-slate-950 rounded-2xl border border-slate-800 shadow-2xl p-8 text-center space-y-5">
        <div class="w-16 h-16 bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 rounded-full flex items-center justify-center text-3xl mx-auto">
            ✓
        </div>

        <div>
            <span class="text-[10px] font-black uppercase tracking-widest text-amber-400 font-mono">SUPPORT TICKET #CMP-{{ str_pad($complaint->id, 5, '0', STR_PAD_LEFT) }}</span>
            <h1 class="text-xl font-black text-white mt-1">Complaint Received!</h1>
            <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                Thank you, <strong class="text-slate-200">{{ $complaint->client?->name ?? 'Customer' }}</strong>. Your ticket has been logged into the support queue for <strong class="text-amber-400">{{ $complaint->organization?->name }}</strong>.
            </p>
        </div>

        <div class="bg-slate-900 p-4 rounded-xl border border-slate-800 text-left text-xs space-y-2">
            <div class="flex justify-between">
                <span class="text-slate-500">Ticket ID:</span>
                <span class="font-mono font-bold text-white">#CMP-{{ str_pad($complaint->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Subject:</span>
                <span class="font-bold text-slate-200 truncate max-w-[200px]">{{ $complaint->subject }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Priority:</span>
                <span class="font-extrabold text-amber-400 uppercase text-[10px] bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/30">{{ $complaint->priority }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Status:</span>
                <span class="font-extrabold text-emerald-400 uppercase text-[10px] bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/30">{{ $complaint->status }}</span>
            </div>
        </div>

        <p class="text-xs text-slate-400 leading-relaxed">
            Our support team will inspect your issue and contact you via email at <strong class="text-slate-200 font-mono">{{ $complaint->client?->email }}</strong>.
        </p>

        <div class="pt-2">
            <a href="{{ route('public.complaint.create', ['org_id' => $complaint->organization_id]) }}" class="inline-block py-2.5 px-6 bg-slate-900 hover:bg-slate-800 text-slate-300 font-bold text-xs rounded-xl border border-slate-700 transition">
                Submit Another Ticket
            </a>
        </div>
    </div>

</body>
</html>
