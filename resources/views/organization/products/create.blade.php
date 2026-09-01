@extends('layouts.sme')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-2 space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-3 mb-4 border-b border-gray-100">
        <a href="{{ route('organization.products.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 hover:text-gray-900 hover:bg-gray-50 shadow-sm transition">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900 tracking-tight">Add New Product</h1>
            <p class="text-xs text-gray-500">Create a new item configuration inside your central product catalog.</p>
        </div>
    </div>

    <!-- Form Container -->
    <form action="{{ route('organization.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Columns: Product Configurations -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Panel 1: Basic Information -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 space-y-4">
                    <div class="flex items-center gap-2 pb-1.5 border-b border-gray-50">
                        <span class="w-1 h-3.5 bg-[var(--theme-active)] rounded-full"></span>
                        <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Basic Information</h2>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Product Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('name') border-red-300 @enderror" placeholder="e.g. Wireless Mouse, Red T-Shirt">
                            @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Category</label>
                                <select name="category_id" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">SKU (Auto-generated if empty)</label>
                                <input type="text" name="sku" value="{{ old('sku') }}" placeholder="e.g. PRD-1029" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('sku') border-red-300 @enderror">
                                @error('sku') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-1.5">
                                <label class="block text-xs font-semibold text-gray-600">Existing Factory Barcode (EAN / UPC / QR)</label>
                                <button type="button" onclick="openBoxCameraScanner()" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>📷 Scan Box Barcode</span>
                                </button>
                            </div>
                            <input type="text" id="barcodeField" name="barcode" value="{{ old('barcode') }}" placeholder="Scan product box barcode via gun or camera..." class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('barcode') border-red-300 @enderror">
                            <span class="text-[11px] text-gray-400 mt-1 block">Point USB/Bluetooth barcode gun or use mobile camera to capture the barcode printed on the product packaging.</span>
                            @error('barcode') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>


                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Description</label>
                            <textarea name="description" rows="3" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition" placeholder="Provide product technical details or notes...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Panel 2: Pricing & Inventory Config -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 space-y-4">
                    <div class="flex items-center gap-2 pb-1.5 border-b border-gray-50">
                        <span class="w-1 h-3.5 bg-[var(--theme-active)] rounded-full"></span>
                        <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Pricing & Inventory Config</h2>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Selling Price <span class="text-red-500">*</span></label>
                                <div class="flex rounded-xl shadow-xs overflow-hidden border border-gray-300 focus-within:border-[var(--theme-active)] focus-within:ring-1 focus-within:ring-[var(--theme-active)] transition @error('selling_price') border-red-300 @enderror">
                                    <span class="inline-flex items-center px-3.5 bg-gray-50 text-gray-500 font-bold text-xs border-r border-gray-200 select-none">₹</span>
                                    <input type="number" step="0.01" name="selling_price" value="{{ old('selling_price') }}" required class="w-full border-0 px-3 py-2 text-sm font-semibold text-gray-900 outline-none" placeholder="0.00">
                                </div>
                                @error('selling_price') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Purchase Price</label>
                                <div class="flex rounded-xl shadow-xs overflow-hidden border border-gray-300 focus-within:border-[var(--theme-active)] focus-within:ring-1 focus-within:ring-[var(--theme-active)] transition @error('purchase_price') border-red-300 @enderror">
                                    <span class="inline-flex items-center px-3.5 bg-gray-50 text-gray-500 font-bold text-xs border-r border-gray-200 select-none">₹</span>
                                    <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price') }}" class="w-full border-0 px-3 py-2 text-sm font-semibold text-gray-900 outline-none" placeholder="0.00">
                                </div>
                                @error('purchase_price') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tax Rate (%)</label>
                                <input type="number" step="0.01" name="tax_rate" value="{{ old('tax_rate', 0) }}" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('tax_rate') border-red-300 @enderror" placeholder="0">
                                @error('tax_rate') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Low Stock Alert Level</label>
                                <input type="number" name="min_stock_level" value="{{ old('min_stock_level', 5) }}" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('min_stock_level') border-red-300 @enderror" placeholder="e.g. 5">
                                @error('min_stock_level') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Media & Actions -->
            <div class="space-y-6">
                <!-- Product Media Panel -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 space-y-4">
                    <div class="flex items-center gap-2 pb-1.5 border-b border-gray-50">
                        <span class="w-1 h-3.5 bg-[var(--theme-active)] rounded-full"></span>
                        <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Product Photos & Gallery</h2>
                    </div>
                    
                    <!-- Primary Thumbnail -->
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-gray-600">Main Cover Image</label>
                        <div class="w-full h-36 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 flex flex-col items-center justify-center overflow-hidden relative" id="imagePreviewContainer">
                            <svg class="w-8 h-8 text-gray-350 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-[11px] text-gray-400 font-medium">Upload Main Cover</span>
                        </div>
                        <input type="file" name="image" id="imageInput" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border file:border-gray-300 file:text-xs file:font-semibold file:bg-white hover:file:bg-gray-50 file:cursor-pointer cursor-pointer">
                        @error('image') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Multi-Image Gallery with Individual Add & Remove -->
                    <div class="pt-3 border-t border-gray-100 space-y-3">
                        <div class="flex justify-between items-center">
                            <label class="block text-xs font-semibold text-gray-600">Additional Gallery Photos</label>
                            <button type="button" onclick="document.getElementById('pickerInput').click()" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1">
                                + Add Photos
                            </button>
                        </div>
                        
                        <!-- Hidden File Inputs -->
                        <input type="file" id="pickerInput" multiple accept="image/*" class="hidden" onchange="addSelectedFiles(this)">
                        <input type="file" name="images[]" id="actualGalleryInput" multiple accept="image/*" class="hidden">

                        <!-- Preview Grid -->
                        <div id="galleryPreviewContainer" class="grid grid-cols-3 gap-2 pt-1">
                            <div id="emptyGalleryNotice" class="col-span-3 py-6 border-2 border-dashed border-gray-200 rounded-lg text-center text-gray-400 text-xs">
                                No additional photos selected.<br>Click <b>+ Add Photos</b> to add images one by one or in batch.
                            </div>
                        </div>
                        @error('images.*') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <!-- Action Controls Panel -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 space-y-4">
                    <div class="flex items-center gap-2 pb-1.5 border-b border-gray-50">
                        <span class="w-1 h-3.5 bg-[var(--theme-active)] rounded-full"></span>
                        <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Publish Product</h2>
                    </div>
                    
                    <div class="flex gap-2.5 pt-1.5">
                        <a href="{{ route('organization.products.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 bg-white rounded-lg font-semibold text-xs hover:bg-gray-50 shadow-sm flex-1 text-center transition">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-[var(--theme-active)] text-[var(--theme-active-text)] rounded-lg font-semibold text-xs hover:opacity-90 shadow-sm flex-1 transition">Save Product</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Single Main Cover Image Preview
document.getElementById('imageInput').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        let reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('imagePreviewContainer').innerHTML = `<img src="${ev.target.result}" class="w-full h-full object-cover">`;
        }
        reader.readAsDataURL(e.target.files[0]);
    }
});

// Dynamic Multi-Image Gallery List Manager
let galleryFiles = new DataTransfer();

function addSelectedFiles(input) {
    if (input.files && input.files.length > 0) {
        Array.from(input.files).forEach(file => {
            galleryFiles.items.add(file);
        });
        input.value = ''; // Reset picker so user can pick again
        renderGalleryPreviews();
    }
}

function removeGalleryFile(index) {
    const dt = new DataTransfer();
    const files = galleryFiles.files;
    for (let i = 0; i < files.length; i++) {
        if (i !== index) {
            dt.items.add(files[i]);
        }
    }
    galleryFiles = dt;
    renderGalleryPreviews();
}

function renderGalleryPreviews() {
    const hiddenFileInput = document.getElementById('actualGalleryInput');
    hiddenFileInput.files = galleryFiles.files;

    const container = document.getElementById('galleryPreviewContainer');
    container.innerHTML = '';

    if (galleryFiles.files.length === 0) {
        container.innerHTML = `
            <div class="col-span-3 py-6 border-2 border-dashed border-gray-200 rounded-lg text-center text-gray-400 text-xs">
                No additional photos selected.<br>Click <b>+ Add Photos</b> to add images one by one or in batch.
            </div>
        `;
        return;
    }

    Array.from(galleryFiles.files).forEach((file, index) => {
        let reader = new FileReader();
        reader.onload = function(ev) {
            const wrapper = document.createElement('div');
            wrapper.className = 'relative group rounded-lg overflow-hidden border border-gray-200 shadow-xs h-20 bg-gray-50';
            wrapper.innerHTML = `
                <img src="${ev.target.result}" class="w-full h-full object-cover">
                <button type="button" onclick="removeGalleryFile(${index})" class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold shadow-md hover:bg-red-700 transition" title="Remove photo">&times;</button>
            `;
            container.appendChild(wrapper);
        };
        reader.readAsDataURL(file);
    });
}
</script>

<!-- Box Barcode Camera Modal -->
<div id="boxCameraModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeBoxCameraScanner()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-800 p-5 space-y-4">
            <div class="flex justify-between items-center pb-2 border-b border-slate-800 text-white">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    Scan Factory Barcode from Product Box
                </span>
                <button type="button" onclick="closeBoxCameraScanner()" class="text-slate-400 hover:text-white font-bold text-sm">&times;</button>
            </div>
            <div id="boxBarcodeReader" class="w-full rounded-xl overflow-hidden bg-black min-h-[220px]"></div>
            <p class="text-xs text-slate-400 text-center">Point mobile phone camera at barcode on the product packaging.</p>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
let boxScanner = null;

function openBoxCameraScanner() {
    document.getElementById('boxCameraModal').classList.remove('hidden');
    boxScanner = new Html5QrcodeScanner("boxBarcodeReader", { fps: 10, qrbox: { width: 250, height: 250 } }, false);
    boxScanner.render((decodedText) => {
        document.getElementById('barcodeField').value = decodedText;
        closeBoxCameraScanner();
    }, (err) => {});
}

function closeBoxCameraScanner() {
    if (boxScanner) {
        boxScanner.clear();
        boxScanner = null;
    }
    document.getElementById('boxCameraModal').classList.add('hidden');
}
</script>
@endsection
