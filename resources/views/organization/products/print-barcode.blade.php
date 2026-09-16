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

@section('title', 'Print Barcode Labels: ' . $product->name)

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumb & Page Header (Non-Print) -->
    <div class="no-print flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <a href="{{ route('organization.products.index') }}" class="hover:text-slate-900 transition-colors">Inventory</a>
                <span class="text-slate-400 font-bold">/</span>
                <a href="{{ route('organization.products.show', $product) }}" class="hover:text-slate-900 transition-colors">{{ $product->name }}</a>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Print Barcodes</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 text-xl font-black shadow-sm shrink-0">
                    🖨️
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Print Barcode &amp; QR Sticker Labels</h1>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">
                        Generate printable retail adhesive barcode stickers for <b>{{ $product->name }}</b> ({{ $product->barcode ?: $product->sku }}).
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Header Action Buttons -->
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap lg:justify-end">
            <a href="{{ route('organization.products.show', $product) }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs rounded-lg transition shadow-2xs">
                &larr; Product Specs
            </a>

            <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H7a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print Sticker Sheet</span>
            </button>
        </div>
    </div>

    <!-- 2. Non-Print Interactive Sticker Configuration Card -->
    <div class="no-print bg-white p-6 rounded-xl border border-slate-200/90 shadow-2xs space-y-4">
        <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-800">Label Sheet Configuration</span>
            <span class="text-xs text-slate-500 font-medium">Auto-renders below in real-time</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5 text-[11px]">Barcode Format</label>
                <select id="codeFormat" onchange="renderLabels()" class="w-full bg-slate-50 border border-slate-300 rounded-lg py-2 px-3 text-xs font-bold text-slate-950 focus:bg-white focus:ring-2 focus:ring-amber-500 shadow-2xs">
                    <option value="barcode">Standard Barcode (CODE128)</option>
                    <option value="qrcode">2D QR Code</option>
                </select>
            </div>
            
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5 text-[11px]">Quantity of Labels</label>
                <input type="number" id="labelCount" value="12" min="1" max="100" onchange="renderLabels()" class="w-full bg-slate-50 border border-slate-300 rounded-lg py-2 px-3 text-xs font-black text-slate-950 text-center focus:bg-white focus:ring-2 focus:ring-amber-500 shadow-2xs">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5 text-[11px]">Show Selling Price</label>
                <select id="showPrice" onchange="renderLabels()" class="w-full bg-slate-50 border border-slate-300 rounded-lg py-2 px-3 text-xs font-bold text-slate-950 focus:bg-white focus:ring-2 focus:ring-amber-500 shadow-2xs">
                    <option value="yes">Yes (₹{{ number_format($product->selling_price, 2) }})</option>
                    <option value="no">No (Hide Price)</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5 text-[11px]">Show SKU Code</label>
                <select id="showSku" onchange="renderLabels()" class="w-full bg-slate-50 border border-slate-300 rounded-lg py-2 px-3 text-xs font-bold text-slate-950 focus:bg-white focus:ring-2 focus:ring-amber-500 shadow-2xs">
                    <option value="yes">Yes (SKU: {{ $product->sku }})</option>
                    <option value="no">No (Hide SKU)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- 3. Printable Area Sheet -->
    <div id="printArea" class="bg-white p-6 sm:p-8 rounded-xl border border-slate-200/90 shadow-2xs">
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
