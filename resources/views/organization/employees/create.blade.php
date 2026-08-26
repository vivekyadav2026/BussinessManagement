@extends('layouts.sme')

@section('content')
<div class="p-6 max-w-4xl space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('organization.employees.index') }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-white border border-gray-200 text-gray-500 hover:text-gray-900 hover:bg-gray-50 shadow-sm transition">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Add New Employee</h1>
            <p class="text-sm text-gray-500 mt-0.5">Register a new staff member to the platform.</p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('organization.employees.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Personal Details -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-50 pb-3">Personal Details</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('first_name') border-red-300 @enderror">
                    @error('first_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('last_name') border-red-300 @enderror">
                    @error('last_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Employee Code</label>
                    <input type="text" name="employee_code" value="{{ old('employee_code') }}" class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('employee_code') border-red-300 @enderror">
                    @error('employee_code') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Designation / Title</label>
                    <input type="text" name="designation" value="{{ old('designation') }}" class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('designation') border-red-300 @enderror">
                    @error('designation') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('phone') border-red-300 @enderror">
                    @error('phone') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Joining Date</label>
                    <input type="date" name="joining_date" value="{{ old('joining_date') }}" class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('joining_date') border-red-300 @enderror">
                    @error('joining_date') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Address</label>
                    <textarea name="address" rows="3" class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('address') border-red-300 @enderror">{{ old('address') }}</textarea>
                    @error('address') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Contact Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('email') border-red-300 @enderror">
                    @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        
        <!-- Account Access -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            <div class="flex items-center justify-between border-b border-gray-50 pb-3">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">System Login Access</h3>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="create_account" id="createAccountToggle" value="1" {{ old('create_account') ? 'checked' : '' }} class="rounded text-[var(--theme-active)] focus:ring-[var(--theme-active)] border-gray-300">
                    <span class="text-sm font-medium text-gray-700">Create Login Account</span>
                </label>
            </div>
            
            <div id="accountFields" class="{{ old('create_account') ? '' : 'hidden' }} space-y-6">
                <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl text-xs text-blue-800 font-medium">
                    Enabling this will create a user account for the employee. The Contact Email above will be used as their login ID.
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Initial Password *</label>
                        <input type="password" name="password" id="accPassword" class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('password') border-red-300 @enderror">
                        @error('password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Assign Role *</label>
                        <select name="role" id="accRole" class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('role') border-red-300 @enderror">
                            <option value="">Select a role...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Assign Locations (Branches) *</label>
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 max-h-48 overflow-y-auto divide-y divide-gray-100">
                        @foreach($locations as $loc)
                        <label class="flex items-center gap-3 py-2.5 first:pt-0 last:pb-0 cursor-pointer">
                            <input type="checkbox" name="locations[]" value="{{ $loc->id }}" class="rounded text-[var(--theme-active)] focus:ring-[var(--theme-active)] border-gray-300"
                                {{ is_array(old('locations')) && in_array($loc->id, old('locations')) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700 font-medium">{{ $loc->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('locations') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        
        <!-- Submit Buttons -->
        <div class="flex justify-end gap-3 pt-4 pb-10">
            <a href="{{ route('organization.employees.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="px-5 py-2.5 bg-[var(--theme-active)] text-[var(--theme-active-text)] rounded-xl font-semibold text-sm hover:opacity-95 shadow-sm transition">Save Employee</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('createAccountToggle').addEventListener('change', function() {
        const fields = document.getElementById('accountFields');
        const pass = document.getElementById('accPassword');
        const role = document.getElementById('accRole');
        
        if (this.checked) {
            fields.classList.remove('hidden');
            pass.setAttribute('required', 'required');
            role.setAttribute('required', 'required');
        } else {
            fields.classList.add('hidden');
            pass.removeAttribute('required');
            role.removeAttribute('required');
        }
    });
</script>
@endsection
