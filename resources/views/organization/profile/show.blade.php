@extends('layouts.sme')

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="dash-head">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Organization Profile</h1>
        <p class="text-sm text-gray-500 mt-0.5">Manage your tenant business information, contact details, and platform settings.</p>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
        {{ session('success') }}
    </div>
    @endif

    <div class="panel shadow-sm overflow-hidden">
        <form action="{{ route('organization.profile.update') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Grid Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Org Name -->
                <div class="col-span-full">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Business Name</label>
                    <input type="text" name="name" value="{{ old('name', $organization->name) }}" required class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('name') border-red-300 @enderror">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Email Address -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Business Email</label>
                    <input type="email" name="email" value="{{ old('email', $organization->email) }}" class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('email') border-red-300 @enderror">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Business Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $organization->phone) }}" class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('phone') border-red-300 @enderror">
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- GSTIN -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">GSTIN Number</label>
                    <input type="text" name="gst_number" value="{{ old('gst_number', $organization->gst_number) }}" placeholder="e.g., 22AAAAA0000A1Z5" class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition uppercase @error('gst_number') border-red-300 @enderror">
                    @error('gst_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Logo Uploader -->
                <div x-data="{ imgPreview: '{{ $organization->logo ? asset('storage/' . $organization->logo) : '' }}' }">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Logo</label>
                    <div class="flex items-center gap-4">
                        <template x-if="imgPreview">
                            <img :src="imgPreview" alt="Logo Preview" class="org-preview-logo w-16 h-16 rounded-xl object-cover border border-gray-200">
                        </template>

                        <template x-if="!imgPreview">
                            <div class="w-16 h-16 rounded-xl border border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-xs font-semibold bg-gray-50">No Logo</div>
                        </template>
                        <input type="file" name="logo" @change="
                            const file = $event.target.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = (e) => { imgPreview = e.target.result; };
                                reader.readAsDataURL(file);
                            }
                        " class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                    </div>
                    @error('logo') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                </div>

                <!-- Address -->
                <div class="col-span-full">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Address</label>
                    <textarea name="address" rows="3" class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('address') border-red-300 @enderror">{{ old('address', $organization->address) }}</textarea>
                    @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Working Hours Settings -->
                <div class="col-span-full pt-4 border-t border-gray-100">
                    <h3 class="font-bold text-slate-900 text-sm mb-3">Attendance Working Hours Configuration</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Standard Check-In Time</label>
                            <input type="time" name="default_check_in" value="{{ old('default_check_in', $organization->default_check_in ?? '09:00') }}" class="w-full border border-gray-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Standard Check-Out Time</label>
                            <input type="time" name="default_check_out" value="{{ old('default_check_out', $organization->default_check_out ?? '18:00') }}" class="w-full border border-gray-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800">
                        </div>
                    </div>
                </div>
            </div>


            <!-- Submit buttons -->
            <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100">
                <button type="submit" class="btn btn-gold py-2.5 px-6 font-semibold text-sm shadow-sm transition">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
