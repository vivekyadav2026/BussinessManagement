@extends('layouts.sme')

@push('styles')
<style>
    @media print {
        body * { visibility: hidden; }
        #printArea, #printArea * { visibility: visible; }
        #printArea { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
    }
    .sticker-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 15px;
    }
    .sticker-card {
        border: 1px dashed #cbd5e1;
        padding: 10px;
        background: #ffffff;
        border-radius: 8px;
        text-align: center;
        display: flex;
        flex-col;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
@endpush

@section('content')
<div class="p-6 max-w-5xl mx-auto space-y-6">
    <!-- Non-Print Controls Card -->
    <div class="no-print bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-100 pb-4">
            <div>
                <a href="{{ route('organization.products.index') }}" class="text-xs text-gray-500 hover:text-indigo-600 mb-1 inline-block">&larr; Back to Products Catalog</a>
                <h1 class="text-xl font-bold text-gray-900">Print Product Barcode & QR Code Labels</h1>
                <p class="text-xs text-gray-500 mt-0.5">Generate printable sticker labels for <b>{{ $product->name }}</b>.</p>
            </div>
            
            <button onclick="window.print()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H7a2 2 0 00-2 2v4h10z"/></svg>
                <span>🖨️ Print Label Sheet</span>
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <label class="block font-bold text-gray-700 mb-1">Code Format</label>
                <select id="codeFormat" onchange="renderLabels()" class="w-full border-gray-300 rounded-lg py-1.5 text-xs font-semibold">
                    <option value="barcode">Standard Barcode (CODE128)</option>
                    <option value="qrcode">2D QR Code</option>
                </select>
            </div>
            
            <div>
                <label class="block font-bold text-gray-700 mb-1">Number of Stickers</label>
                <input type="number" id="labelCount" value="12" min="1" max="100" onchange="renderLabels()" class="w-full border-gray-300 rounded-lg py-1.5 text-xs font-bold text-center">
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">Show Price</label>
                <select id="showPrice" onchange="renderLabels()" class="w-full border-gray-300 rounded-lg py-1.5 text-xs font-semibold">
                    <option value="yes">Yes (Show ₹{{ number_format($product->selling_price, 2) }})</option>
                    <option value="no">No (Hide Price)</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">Show SKU</label>
                <select id="showSku" onchange="renderLabels()" class="w-full border-gray-300 rounded-lg py-1.5 text-xs font-semibold">
                    <option value="yes">Yes (SKU: {{ $product->sku }})</option>
                    <option value="no">No (Hide SKU)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Printable Area Sheet -->
    <div id="printArea" class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div id="stickerContainer" class="sticker-grid"></div>
    </div>
</div>

<script>
const barcodeVal = "{{ $product->barcode ?: $product->sku }}";
const productName = "{{ addslashes($product->name) }}";
const productPrice = "₹{{ number_format($product->selling_price, 2) }}";
const productSku = "{{ $product->sku }}";

function renderLabels() {
    const container = document.getElementById('stickerContainer');
    container.innerHTML = '';

    const format = document.getElementById('codeFormat').value;
    const count = parseInt(document.getElementById('labelCount').value) || 1;
    const incPrice = document.getElementById('showPrice').value === 'yes';
    const incSku = document.getElementById('showSku').value === 'yes';

    for (let i = 0; i < count; i++) {
        let card = document.createElement('div');
        card.className = 'sticker-card flex flex-col items-center justify-center p-3 border border-dashed border-gray-300 rounded-lg bg-white';
        
        let html = `<div class="text-[11px] font-bold text-gray-900 truncate max-w-full mb-1">${productName}</div>`;
        
        if (format === 'barcode') {
            html += `<svg id="barcode-${i}" class="max-w-full h-12"></svg>`;
        } else {
            html += `<div id="qrcode-${i}" class="my-1 flex justify-center"></div>`;
        }

        let meta = [];
        if (incSku) meta.push(`SKU: ${productSku}`);
        if (incPrice) meta.push(`<b>${productPrice}</b>`);
        
        if (meta.length > 0) {
            html += `<div class="text-[10px] text-gray-700 mt-1">${meta.join(' | ')}</div>`;
        }

        card.innerHTML = html;
        container.appendChild(card);

        // Render vector Graphics
        if (format === 'barcode') {
            JsBarcode(`#barcode-${i}`, barcodeVal, {
                format: "CODE128",
                width: 1.5,
                height: 40,
                displayValue: true,
                fontSize: 10,
                margin: 2
            });
        } else {
            const qrDeepLink = "{{ route('organization.inventory.scanner') }}?code=" + encodeURIComponent(barcodeVal);
            new QRCode(document.getElementById(`qrcode-${i}`), {
                text: qrDeepLink,
                width: 65,
                height: 65,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        }

    }
}

document.addEventListener('DOMContentLoaded', renderLabels);
</script>
@endsection
