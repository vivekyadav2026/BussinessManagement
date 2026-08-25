@extends('layouts.sme')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="dash-head mb-6 flex justify-between items-end">
        <div>
            <a href="{{ route('organization.inventory.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 mb-2 inline-block">&larr; Back to Inventory</a>
            <h1 class="text-2xl font-bold text-gray-900">Barcode Scanner Mode</h1>
        </div>
        <div class="text-sm bg-indigo-50 text-indigo-700 px-3 py-1 rounded font-medium border border-indigo-100">
            Hardware Scanner Ready
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 border border-green-200 text-sm font-medium">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg mb-6 border border-red-200 text-sm font-medium">
        {{ session('error') }}
    </div>
    @endif

    <div class="panel text-center py-10 mb-6">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 8V4h4m8 0h4v4M4 16v4h4m8 0h4v-4"></path></svg>
        <h2 class="text-xl font-bold text-gray-800 mb-2">Scan Product Barcode</h2>
        <p class="text-gray-500 text-sm mb-6">Use your USB/Bluetooth scanner or enter the SKU/Barcode manually.</p>
        
        <form id="scannerForm" onsubmit="processScan(event)" class="max-w-md mx-auto">
            <input type="text" id="barcodeInput" placeholder="Awaiting scan..." class="w-full text-center text-2xl tracking-widest font-mono border-2 border-indigo-300 rounded-lg py-4 focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition-all shadow-sm" autofocus autocomplete="off">
        </form>
    </div>

    <!-- Scanned Product Result Panel (Hidden by default) -->
    <div id="scanResultPanel" class="hidden panel border-t-4 border-indigo-500">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="text-xl font-bold text-gray-900" id="resName">Product Name</h3>
                <div class="text-sm text-gray-500 mt-1">
                    SKU: <span id="resSku" class="font-mono"></span> | 
                    Barcode: <span id="resBarcode" class="font-mono"></span>
                </div>
            </div>
            <div class="text-right">
                <div class="text-xs text-gray-500 uppercase tracking-wide font-bold">Current Stock</div>
                <div class="text-3xl font-black text-indigo-600" id="resStock">0</div>
            </div>
        </div>

        <form action="{{ route('organization.inventory.adjust') }}" method="POST" class="mt-8 border-t pt-6 bg-gray-50 -mx-6 -mb-6 p-6 rounded-b-lg">
            @csrf
            <input type="hidden" name="product_id" id="resProductId">
            <h4 class="font-bold text-gray-800 mb-4">Quick Adjustment</h4>
            <div class="flex gap-4">
                <div class="w-1/3">
                    <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Type</label>
                    <select name="type" class="w-full border-gray-300 rounded-lg" id="adjType">
                        <option value="in">Stock IN (Receive)</option>
                        <option value="out">Stock OUT (Deduct)</option>
                    </select>
                </div>
                <div class="w-1/3">
                    <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Quantity</label>
                    <input type="number" name="quantity" min="1" value="1" required class="w-full border-gray-300 rounded-lg font-bold text-lg" id="adjQuantity">
                </div>
                <div class="w-1/3 flex items-end">
                    <button type="submit" class="btn btn-gold w-full h-11 text-lg shadow-sm">Process</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Always keep focus on the scanner input unless user is typing in the adjustment form
document.addEventListener('click', function(e) {
    if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'SELECT' && e.target.tagName !== 'BUTTON') {
        document.getElementById('barcodeInput').focus();
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
