@extends('layouts.sme')

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
@endpush

@section('content')
<div class="p-6 max-w-4xl mx-auto space-y-6">
    <!-- Header Navigation & Title -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-xs">
        <div class="flex items-center gap-3">
            <a href="{{ route('organization.inventory.index') }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 border border-gray-200 text-gray-600 hover:text-gray-900 hover:bg-gray-100 shadow-xs transition">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900 tracking-tight flex items-center gap-2">
                    Barcode Scanner Mode
                </h1>
                <p class="text-xs text-gray-500 mt-0.5">Hardware USB/Bluetooth scanner gun or mobile phone camera scanner.</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" onclick="toggleCameraScanner()" id="cameraToggleBtn" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Camera Scanner</span>
            </button>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-xl border border-emerald-100">
                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-ping"></span>
                Hardware Gun Ready
            </div>
        </div>
    </div>

    <!-- AJAX Success / Alert Message Banner -->
    <div id="txAlertBanner" class="hidden bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-semibold shadow-xs flex items-center justify-between transition-all">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span id="txAlertMsg">Stock transaction completed successfully!</span>
        </div>
        <button type="button" onclick="document.getElementById('txAlertBanner').classList.add('hidden')" class="text-emerald-500 hover:text-emerald-700 font-bold">&times;</button>
    </div>

    <!-- Live Mobile Camera Reader (Hidden by default) -->
    <div id="cameraScannerCard" class="hidden bg-slate-900 rounded-2xl shadow-xl border border-slate-800 p-5 space-y-4 transition-all">
        <div class="flex justify-between items-center pb-3 border-b border-slate-800 text-white">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2">
                <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                Live Camera Barcode Viewfinder
            </span>
            <button type="button" onclick="toggleCameraScanner()" class="px-3 py-1 bg-slate-800 hover:bg-slate-700 rounded-lg text-xs font-bold text-slate-300 transition">&times; Close Camera</button>
        </div>
        <div id="interactiveReader" class="w-full max-w-md mx-auto rounded-xl overflow-hidden bg-black min-h-[240px]"></div>
    </div>

    <!-- Scanner Interface Hero Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 flex flex-col items-center justify-center text-center space-y-6">
        <!-- Target Scanner Graphic -->
        <div class="relative w-20 h-20 rounded-2xl bg-indigo-50/80 border border-indigo-100 flex items-center justify-center text-indigo-600 shadow-inner group">
            <svg class="w-10 h-10 transition transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 8V4h4m8 0h4v4M4 16v4h4m8 0h4v-4"></path>
            </svg>
            <span class="absolute -top-1 -right-1 flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
            </span>
        </div>
        
        <div class="space-y-1">
            <h2 class="text-xl font-bold text-gray-900 tracking-tight">Scan Product Barcode</h2>
            <p class="text-xs text-gray-400 max-w-md mx-auto leading-relaxed">
                Aim your hardware scanner gun at the product label, or click <b>Camera Scanner</b> to scan using mobile camera.
            </p>
        </div>
        
        <form id="scannerForm" onsubmit="processScan(event)" class="w-full max-w-md">
            <div class="relative">
                <input type="text" id="barcodeInput" placeholder="Awaiting scan entry..." class="w-full text-center text-2xl tracking-widest font-mono border-2 border-indigo-100 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 rounded-2xl py-4 px-6 outline-none transition shadow-xs bg-gray-50/50 focus:bg-white text-gray-900 font-bold placeholder:text-gray-300 placeholder:font-normal placeholder:tracking-normal placeholder:text-sm" autofocus autocomplete="off">
            </div>
        </form>
    </div>

    <!-- Scanned Product Result Panel (Hidden by default) -->
    <div id="scanResultPanel" class="hidden bg-white rounded-2xl shadow-md border border-indigo-100 overflow-hidden transition-all duration-300">
        <!-- Card Header -->
        <div class="p-6 bg-gradient-to-r from-gray-50 to-indigo-50/30 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-100">
            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700 uppercase tracking-wider mb-2">Scanned Item Result</span>
                <h3 class="text-xl font-bold text-gray-900" id="resName">Product Name</h3>
                <div class="flex items-center gap-2 mt-1.5 text-xs text-gray-500 font-medium">
                    <span class="px-2 py-0.5 bg-white rounded border border-gray-200 font-mono text-gray-700">SKU: <b id="resSku"></b></span>
                    <span class="px-2 py-0.5 bg-white rounded border border-gray-200 font-mono text-gray-700">Barcode: <b id="resBarcode"></b></span>
                </div>
            </div>
            
            <div class="text-left sm:text-right bg-white p-4 rounded-xl border border-indigo-100 shadow-xs min-w-[140px]">
                <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Active Branch Stock</div>
                <div class="text-3xl font-black text-indigo-600 mt-0.5" id="resStock">0</div>
            </div>
        </div>

        <!-- Adjustment Form -->
        <form id="adjustmentForm" onsubmit="processTransaction(event)" class="p-6 space-y-5 bg-white">
            @csrf
            <input type="hidden" name="product_id" id="resProductId">
            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100 pb-2">Stock Transaction Controller</h4>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 items-end">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-2">Movement Action</label>
                    <select name="type" id="adjType" class="w-full border border-gray-300 focus:border-[var(--theme-active)] rounded-xl px-4 py-2.5 text-xs font-bold bg-white outline-none transition">
                        <option value="in">✓ Stock IN (Add Stock)</option>
                        <option value="out">✗ Stock OUT (Deduct Stock)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-2">Quantity Adjustment</label>
                    <div class="flex items-center border border-gray-300 rounded-xl overflow-hidden bg-white">
                        <button type="button" onclick="adjustQtyVal(-1)" class="px-3 py-2 bg-gray-50 hover:bg-gray-100 text-gray-600 font-bold border-r border-gray-200 transition">-</button>
                        <input type="number" name="quantity" min="1" value="1" required class="w-full text-center text-base font-bold outline-none py-2 border-0" id="adjQuantity">
                        <button type="button" onclick="adjustQtyVal(1)" class="px-3 py-2 bg-gray-50 hover:bg-gray-100 text-gray-600 font-bold border-l border-gray-200 transition">+</button>
                    </div>
                </div>

                <div>
                    <button type="submit" id="btnProcessTx" class="w-full px-5 py-2.5 bg-[var(--theme-active)] text-[var(--theme-active-text)] rounded-xl font-bold text-xs hover:opacity-90 shadow-sm transition h-[42px] uppercase tracking-wider">
                        Process Transaction
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let html5QrcodeScanner = null;
let isCameraActive = false;
let isScanLocked = false;

function adjustQtyVal(delta) {
    const qtyInput = document.getElementById('adjQuantity');
    let val = parseInt(qtyInput.value) || 1;
    val = Math.max(1, val + delta);
    qtyInput.value = val;
}

function toggleCameraScanner() {
    const card = document.getElementById('cameraScannerCard');
    const btn = document.getElementById('cameraToggleBtn');
    
    if (isCameraActive) {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear();
        }
        card.classList.add('hidden');
        btn.querySelector('span').textContent = 'Camera Scanner';
        isCameraActive = false;
    } else {
        card.classList.remove('hidden');
        btn.querySelector('span').textContent = 'Close Camera';
        isCameraActive = true;
        
        html5QrcodeScanner = new Html5QrcodeScanner(
            "interactiveReader", 
            { fps: 10, qrbox: { width: 250, height: 250 } },
            false
        );
        
        html5QrcodeScanner.render((decodedText, decodedResult) => {
            if (isScanLocked) return;
            isScanLocked = true;

            if (navigator.vibrate) {
                navigator.vibrate(100);
            }

            document.getElementById('barcodeInput').value = decodedText;
            processScanData(decodedText);

            setTimeout(() => { isScanLocked = false; }, 2500);
        }, (errorMessage) => {
            // Scanning in progress...
        });
    }
}


document.addEventListener('click', function(e) {
    if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'SELECT' && e.target.tagName !== 'BUTTON') {
        const input = document.getElementById('barcodeInput');
        if (input && !isCameraActive) input.focus();
    }
});

function processScan(e) {
    e.preventDefault();
    const input = document.getElementById('barcodeInput');
    const barcode = input.value.trim();
    if (!barcode) return;
    processScanData(barcode);
}

function processScanData(barcode) {
    const input = document.getElementById('barcodeInput');
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
        if(!isCameraActive) input.focus();
        
        if (data.success) {
            document.getElementById('scanResultPanel').classList.remove('hidden');
            document.getElementById('resName').textContent = data.product.name;
            document.getElementById('resSku').textContent = data.product.sku;
            document.getElementById('resBarcode').textContent = data.product.barcode || 'N/A';
            document.getElementById('resStock').textContent = data.product.current_stock;
            document.getElementById('resProductId').value = data.product.id;
            
            setTimeout(() => {
                const qtyInput = document.getElementById('adjQuantity');
                qtyInput.focus();
                qtyInput.select();
            }, 100);
        } else {
            alert('Product not found for barcode: ' + barcode);
            document.getElementById('scanResultPanel').classList.add('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        input.disabled = false;
        input.value = '';
        if(!isCameraActive) input.focus();
        alert('An error occurred while processing barcode scan.');
    });
}

function processTransaction(e) {
    e.preventDefault();
    const btn = document.getElementById('btnProcessTx');
    btn.disabled = true;
    btn.textContent = 'Processing...';

    const productId = document.getElementById('resProductId').value;
    const type = document.getElementById('adjType').value;
    const quantity = document.getElementById('adjQuantity').value;

    fetch('{{ route("organization.inventory.adjust") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            type: type,
            quantity: quantity,
            notes: 'Processed via Scanner Mode'
        })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.textContent = 'Process Transaction';

        if (data.success) {
            // Update stock count live on screen with a fast animation
            const stockEl = document.getElementById('resStock');
            stockEl.textContent = data.new_stock;
            stockEl.classList.add('text-emerald-600', 'scale-110');
            setTimeout(() => stockEl.classList.remove('scale-110'), 300);

            // Show success banner
            const alertBanner = document.getElementById('txAlertBanner');
            const alertMsg = document.getElementById('txAlertMsg');
            alertMsg.textContent = `✓ Stock ${type === 'in' ? 'Added (+)' : 'Deducted (-)'} ${quantity} units. New Stock: ${data.new_stock} units.`;
            alertBanner.classList.remove('hidden');

            // Refocus barcode input for continuous super-fast scanning
            const input = document.getElementById('barcodeInput');
            if (input) {
                input.focus();
                input.select();
            }
        } else {
            alert("Error: " + (data.message || "Failed to process stock transaction."));
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.textContent = 'Process Transaction';
        console.error(err);
        alert("A server error occurred while processing transaction.");
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const code = urlParams.get('code') || urlParams.get('barcode');
    if (code) {
        document.getElementById('barcodeInput').value = code;
        processScanData(code);
    }
});
</script>
@endsection

