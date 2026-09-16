@extends('layouts.sme')

@section('title', 'Barcode Scanner Mode')

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
@endpush

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumb & Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <a href="{{ route('organization.inventory.index') }}" class="hover:text-slate-900 transition-colors">Inventory</a>
                <span class="text-slate-400 font-bold">/</span>
                <span class="hover:text-slate-900 transition-colors">Quick Operations</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Barcode Scanner</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 text-xl font-black shadow-sm shrink-0">
                    📷
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Barcode Scanner Mode</h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-ping"></span>
                            <span>Hardware Gun Ready</span>
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">
                        Scan items with a USB/Bluetooth hardware barcode gun, or toggle the live camera viewfinder.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Header Actions -->
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap lg:justify-end">
            <button type="button" onclick="toggleCameraScanner()" id="cameraToggleBtn" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-950 hover:bg-slate-800 text-white font-extrabold text-xs rounded-lg shadow-xs transition">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Camera Scanner</span>
            </button>

            <a href="{{ route('organization.inventory.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-800 font-bold text-xs rounded-lg shadow-2xs transition">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Stock Levels</span>
            </a>
        </div>
    </div>

    <!-- AJAX Success / Alert Message Banner -->
    <div id="txAlertBanner" class="hidden bg-emerald-50 border border-emerald-300 text-emerald-950 px-4 py-3.5 rounded-xl text-xs sm:text-sm font-semibold shadow-2xs flex items-center justify-between transition-all">
        <div class="flex items-center gap-2">
            <span class="font-extrabold text-emerald-700 text-base">✓</span>
            <span id="txAlertMsg">Stock transaction completed successfully!</span>
        </div>
        <button type="button" onclick="document.getElementById('txAlertBanner').classList.add('hidden')" class="text-emerald-700 hover:text-emerald-900 font-extrabold text-base">&times;</button>
    </div>

    <!-- Live Mobile Camera Reader (Hidden by default) -->
    <div id="cameraScannerCard" class="hidden bg-slate-950 rounded-2xl shadow-xl border border-slate-800 p-5 space-y-4 transition-all">
        <div class="flex justify-between items-center pb-3 border-b border-slate-800 text-white">
            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-300 flex items-center gap-2">
                <span class="w-2 h-2 bg-rose-500 rounded-full animate-pulse"></span>
                Live Camera Barcode Viewfinder
            </span>
            <button type="button" onclick="toggleCameraScanner()" class="px-3 py-1 bg-slate-800 hover:bg-slate-700 rounded-lg text-xs font-bold text-slate-300 transition">&times; Close Camera</button>
        </div>
        <div id="interactiveReader" class="w-full max-w-md mx-auto rounded-xl overflow-hidden bg-black min-h-[240px]"></div>
    </div>

    <!-- Scanner Interface Hero Card -->
    <div class="bg-white rounded-2xl shadow-2xs border border-slate-200/90 p-8 flex flex-col items-center justify-center text-center space-y-6">
        <!-- Target Scanner Graphic -->
        <div class="relative w-20 h-20 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 shadow-inner group">
            <svg class="w-10 h-10 transition transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 8V4h4m8 0h4v4M4 16v4h4m8 0h4v-4"></path>
            </svg>
            <span class="absolute -top-1 -right-1 flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
            </span>
        </div>
        
        <div class="space-y-1">
            <h2 class="text-xl font-extrabold text-slate-950 tracking-tight">Scan Product Barcode / SKU</h2>
            <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed">
                Aim your hardware scanner gun at the product label, or type a code below and press Enter.
            </p>
        </div>
        
        <form id="scannerForm" onsubmit="processScan(event)" class="w-full max-w-md">
            <div class="relative">
                <input type="text" id="barcodeInput" placeholder="Awaiting barcode scan..." class="w-full text-center text-2xl tracking-widest font-mono border-2 border-slate-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-100 rounded-xl py-4 px-6 outline-none transition shadow-2xs bg-slate-50 focus:bg-white text-slate-950 font-black placeholder:text-slate-400 placeholder:font-medium placeholder:tracking-normal placeholder:text-sm" autofocus autocomplete="off">
            </div>
        </form>
    </div>

    <!-- Scanned Product Result Panel (Hidden by default) -->
    <div id="scanResultPanel" class="hidden bg-white rounded-2xl shadow-md border border-slate-200/90 overflow-hidden transition-all duration-300">
        <!-- Card Header -->
        <div class="p-6 bg-slate-950 text-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-500 text-slate-950 uppercase tracking-wider mb-2">Scanned Item Identified</span>
                <h3 class="text-xl font-black text-white" id="resName">Product Name</h3>
                <div class="flex items-center gap-2 mt-1.5 text-xs text-slate-300 font-medium">
                    <span class="px-2 py-0.5 bg-slate-800 rounded border border-slate-700 font-mono text-slate-200">SKU: <b id="resSku"></b></span>
                    <span class="px-2 py-0.5 bg-slate-800 rounded border border-slate-700 font-mono text-slate-200">Barcode: <b id="resBarcode"></b></span>
                </div>
            </div>
            
            <div class="text-left sm:text-right bg-slate-900 p-4 rounded-xl border border-slate-800 shadow-inner min-w-[140px]">
                <div class="text-[10px] text-slate-400 uppercase tracking-wider font-extrabold">Active Stock</div>
                <div class="text-3xl font-black text-amber-400 font-mono mt-0.5" id="resStock">0</div>
            </div>
        </div>

        <!-- Adjustment Form -->
        <form id="adjustmentForm" onsubmit="processTransaction(event)" class="p-6 space-y-5 bg-white">
            @csrf
            <input type="hidden" name="product_id" id="resProductId">
            <h4 class="text-xs font-extrabold text-slate-600 uppercase tracking-wider border-b border-slate-200 pb-2">Quick Movement Controller</h4>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 mb-1.5 uppercase tracking-wider">Movement Action</label>
                    <select name="type" id="adjType" class="w-full border border-slate-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 rounded-lg px-3 py-2.5 text-xs font-bold bg-white outline-none transition">
                        <option value="in">➕ Stock In (Add Stock)</option>
                        <option value="out">➖ Stock Out (Deduct Stock)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 mb-1.5 uppercase tracking-wider">Quantity Adjustment</label>
                    <div class="flex items-center border border-slate-300 rounded-lg overflow-hidden bg-white">
                        <button type="button" onclick="adjustQtyVal(-1)" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-black border-r border-slate-200 transition">-</button>
                        <input type="number" name="quantity" min="1" value="1" required class="w-full text-center text-sm font-black font-mono outline-none py-2 border-0 text-slate-950" id="adjQuantity">
                        <button type="button" onclick="adjustQtyVal(1)" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-black border-l border-slate-200 transition">+</button>
                    </div>
                </div>

                <div>
                    <button type="submit" id="btnProcessTx" class="w-full px-5 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 rounded-lg font-extrabold text-xs shadow-xs transition h-[42px] uppercase tracking-wider">
                        Process Movement
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

