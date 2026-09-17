<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Support Ticket &amp; Complaint</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-xl w-full bg-slate-950 rounded-2xl border border-slate-800 shadow-2xl overflow-hidden my-8">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-amber-500 to-amber-600 p-6 text-slate-950">
            <div class="flex justify-between items-center">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest bg-slate-950 text-amber-400 px-2.5 py-1 rounded-full font-mono">Customer Self-Service</span>
                    <h1 class="text-xl font-black mt-2">Lodge Support Ticket &amp; Complaint</h1>
                </div>
                <div class="text-3xl">📝</div>
            </div>
            <p class="text-xs font-semibold text-slate-900 mt-1 opacity-90">Have an issue with an invoice, order, or service? Submit your request below.</p>
        </div>

        <form action="{{ route('public.complaint.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            @if(session('error'))
                <div class="p-3 bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs rounded-xl font-medium">
                    {{ session('error') }}
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="p-3 bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs rounded-xl font-medium space-y-1">
                    @foreach($errors->all() as $err)
                        <div>• {{ $err }}</div>
                    @endforeach
                </div>
            @endif

            <!-- Organization Selection -->
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1.5">Select Business / Organization <span class="text-amber-400">*</span></label>
                @if($selectedOrg)
                    <input type="hidden" name="organization_id" value="{{ $selectedOrg->id }}">
                    <div class="w-full bg-slate-900 border border-slate-800 px-3.5 py-2.5 rounded-xl text-xs font-extrabold text-amber-400">
                        🏢 {{ $selectedOrg->name }}
                    </div>
                @else
                    <select name="organization_id" required class="w-full bg-slate-900 border border-slate-800 text-slate-200 text-xs rounded-xl p-3 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 font-medium">
                        <option value="">-- Choose Business / Vendor --</option>
                        @foreach($organizations as $org)
                            <option value="{{ $org->id }}" {{ old('organization_id') == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>

            @if($invoice)
                <div class="bg-slate-900/80 border border-amber-500/30 p-3 rounded-xl flex justify-between items-center text-xs">
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Related Invoice:</span>
                        <span class="font-extrabold text-white font-mono">#{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="text-right font-mono font-bold text-amber-400">
                        ₹{{ number_format($invoice->grand_total, 2) }}
                    </div>
                </div>
            @endif

            <!-- Customer Details -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Your Full Name <span class="text-amber-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $invoice?->client?->name) }}" required placeholder="John Doe" class="w-full bg-slate-900 border border-slate-800 text-slate-200 text-xs rounded-xl p-3 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Email Address <span class="text-amber-400">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $invoice?->client?->email) }}" required placeholder="you@example.com" class="w-full bg-slate-900 border border-slate-800 text-slate-200 text-xs rounded-xl p-3 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">Phone Number (Optional)</label>
                <input type="text" name="phone" value="{{ old('phone', $invoice?->client?->phone) }}" placeholder="10-digit mobile number" class="w-full bg-slate-900 border border-slate-800 text-slate-200 text-xs rounded-xl p-3 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 font-mono">
            </div>

            <!-- Complaint Details -->
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">Complaint Subject <span class="text-amber-400">*</span></label>
                <input type="text" name="subject" value="{{ old('subject', $invoice ? 'Issue regarding Invoice #' . $invoice->invoice_number : '') }}" required placeholder="e.g., Payment deducted but status shown unpaid" class="w-full bg-slate-900 border border-slate-800 text-slate-200 text-xs rounded-xl p-3 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">Urgency / Priority</label>
                <select name="priority" class="w-full bg-slate-900 border border-slate-800 text-slate-200 text-xs rounded-xl p-3 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    <option value="Low">Low - General query</option>
                    <option value="Medium" selected>Medium - Normal issue</option>
                    <option value="High">High - Urgent attention needed</option>
                    <option value="Urgent">Urgent - Critical billing / order error</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">Detailed Explanation <span class="text-amber-400">*</span></label>
                <textarea name="description" rows="4" required placeholder="Please describe what happened, transaction reference, or any relevant details..." class="w-full bg-slate-900 border border-slate-800 text-slate-200 text-xs rounded-xl p-3 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 leading-relaxed">{{ old('description') }}</textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3.5 bg-amber-500 hover:bg-amber-400 active:bg-amber-600 text-slate-950 font-black text-xs uppercase tracking-wider rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                    <span>Submit Support Ticket</span>
                    <span>&rarr;</span>
                </button>
            </div>
            
            <p class="text-[11px] text-slate-500 text-center pt-2">
                🔒 Your ticket will be logged directly into the vendor support system.
            </p>
        </form>
    </div>

</body>
</html>
