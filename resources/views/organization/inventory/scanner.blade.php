@extends('layouts.sme')

@section('content')
<div class="p-6 max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('organization.inventory.index') }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-white border border-gray-200 text-gray-500 hover:text-gray-900 hover:bg-gray-50 shadow-sm transition">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Barcode Scanner Mode</h1>
                <p class="text-sm text-gray-500 mt-0.5">Use your USB/Bluetooth hardware scanner or enter SKU codes manually.</p>
            </div>
        </div>
        <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-semibold rounded-full border border-emerald-100 animate-pulse">
            <span class="w-2 h-2 rounded-full bg-emerald-505 inline-block"></span>
            Hardware Scanner Ready
        </div>
    </div>

    <!-- Scanner Interface Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 flex flex-col items-center justify-center text-center space-y-6">
        <div class="w-20 h-20 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-650 shadow-inner">
            <!-- Nice SVG representation of barcode/scan focus -->
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 8V4h4m8 0h4v4M4 16v4h4m8 0h4v-4"></path></svg>
        </div>
        
        <div>
            <h2 class="text-lg font-bold text-gray-800">Scan Product Barcode</h2>
            <p class="text-sm text-gray-400 max-w-sm mt-1">Place your cursor inside the entry box below, scan your product's barcode label, and the system will auto-populate the details.</p>
        </div>
        
        <form id="scannerForm" onsubmit="processScan(event)" class="w-full max-w-md">
            <input type="text" id="barcodeInput" placeholder="Awaiting scan entry..." class="w-full text-center text-2xl tracking-widest font-mono border-2 border-indigo-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 rounded-2xl py-4 outline-none transition shadow-sm" autofocus autocomplete="off">
        </form>
    </div>

    <!-- Scanned Product Result Panel (Hidden by default) -->
    <div id="scanResultPanel" class="hidden bg-white rounded-2xl shadow-md border-t-4 border-indigo-550 border-gray-100 overflow-hidden transition-all duration-300">
        <div class="p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900" id="resName">Product Name</h3>
                <div class="text-xs text-gray-500 mt-1 font-medium">
                    SKU: <span id="resSku" class="font-mono text-gray-700"></span> | 
                    Barcode: <span id="resBarcode" class="font-mono text-gray-700"></span>
                </div>
            </div>
            <div class="text-left sm:text-right bg-indigo-50/50 px-4 py-2.5 rounded-xl border border-indigo-100/50">
                <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Active Branch Stock</div>
                <div class="text-3xl font-black text-indigo-600 mt-0.5" id="resStock">0</div>
            </div>
        </div>

        <!-- Adjustment form inside result container -->
        <form action="{{ route('organization.inventory.adjust') }}" method="POST" class="border-t border-gray-100 bg-gray-50/50 p-6 space-y-4">
            @csrf
            <input type="hidden" name="product_id" id="resProductId">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Quick Adjustment Transaction</h4>
            
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="sm:w-1/3">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Movement Type</label>
                    <select name="type" class="w-full border border-gray-200 focus:border-[var(--theme-active)] rounded-xl px-4 py-2.5 text-sm outline-none bg-white transition" id="adjType">
                        <option value="in">Stock IN (Receive)</option>
                        <option value="out">Stock OUT (Deduct)</option>
                    </select>
                </div>
                <div class="sm:w-1/3">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Quantity</label>
                    <input type="number" name="quantity" min="1" value="1" required class="w-full border border-gray-200 focus:border-[var(--theme-active)] rounded-xl px-4 py-2 text-lg font-bold text-center outline-none transition" id="adjQuantity">
                </div>
                <div class="sm:w-1/3 flex items-end">
                    <button type="submit" class="w-full px-5 py-2.5 bg-[var(--theme-active)] text-[var(--theme-active-text)] rounded-xl font-bold text-sm hover:opacity-95 shadow-sm transition h-11">Process Transaction</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Always keep focus on the scanner input unless user is typing in the adjustment form
document.addEventListener('click', function(e) {
    if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'SELECT' && e.target.tagName !== 'BUTTON') {
        const input = document.getElementById('barcodeInput');
        if (input) input.focus();
    }
});

function processScan(e) {
    e.preventDefault();
    const input = document.getElementById('barcodeInput');
    const barcode = input.value.trim();
    if (!barcode) return;
    
    input.disabled = true;
    
    fetch('{{ route("organization.inventory.scanner.process") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ barcode: barcode })
    })
    .then(response => response.json())
    .then(data => {
        input.disabled = false;
        input.value = '';
        input.focus();
        
        if (data.success) {
            document.getElementById('scanResultPanel').classList.remove('hidden');
            document.getElementById('resName').textContent = data.product.name;
            document.getElementById('resSku').textContent = data.product.sku;
            document.getElementById('resBarcode').textContent = data.product.barcode || 'N/A';
            document.getElementById('resStock').textContent = data.product.current_stock;
            document.getElementById('resProductId').value = data.product.id;
            
            // Auto-focus the quantity field for super fast entry
            setTimeout(() => {
                const qtyInput = document.getElementById('adjQuantity');
                qtyInput.focus();
                qtyInput.select();
            }, 100);
        } else {
            alert('Product not found.');
            document.getElementById('scanResultPanel').classList.add('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        input.disabled = false;
        input.value = '';
        input.focus();
        alert('An error occurred while scanning.');
    });
}
</script>
@endsection
