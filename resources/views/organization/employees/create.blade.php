@extends('layouts.sme')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-2">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-3 mb-4 border-b border-gray-100">
        <a href="{{ route('organization.employees.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 hover:text-gray-900 hover:bg-gray-50 shadow-sm transition">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900 tracking-tight">Add New Employee</h1>
            <p class="text-xs text-gray-500">Register a new staff member to the platform.</p>
        </div>
    </div>

    <!-- Form Container -->
    <form action="{{ route('organization.employees.store') }}" method="POST" class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        @csrf
        
        <!-- Section 1: Personal Details -->
        <div class="p-5 space-y-4">
            <div class="flex items-center gap-2 pb-1.5 border-b border-gray-50">
                <span class="w-1 h-3.5 bg-[var(--theme-active)] rounded-full"></span>
                <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Personal Details</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('first_name') border-red-300 @enderror" placeholder="e.g. John">
                    @error('first_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('last_name') border-red-300 @enderror" placeholder="e.g. Doe">
                    @error('last_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Employee Code</label>
                    <input type="text" name="employee_code" value="{{ old('employee_code') }}" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('employee_code') border-red-300 @enderror" placeholder="e.g. EMP-101">
                    @error('employee_code') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Designation / Title</label>
                    <input type="text" name="designation" value="{{ old('designation') }}" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('designation') border-red-300 @enderror" placeholder="e.g. Manager">
                    @error('designation') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('phone') border-red-300 @enderror" placeholder="e.g. +91 98765 43210">
                    @error('phone') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Joining Date</label>
                    <input type="date" name="joining_date" value="{{ old('joining_date') }}" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('joining_date') border-red-300 @enderror">
                    @error('joining_date') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Address</label>
                    <textarea name="address" rows="2" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('address') border-red-300 @enderror" placeholder="e.g. Flat 101, Main Road, New Delhi">{{ old('address') }}</textarea>
                    @error('address') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Contact Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('email') border-red-300 @enderror" placeholder="e.g. employee@company.com">
                    @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        
        <!-- Section 2: Account Access -->
        <div class="border-t border-gray-200 p-5 space-y-4 bg-gray-50/50">
            <div class="flex items-center justify-between pb-1.5 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="w-1 h-3.5 bg-[var(--theme-active)] rounded-full"></span>
                    <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider">System Login Access</h2>
                </div>
                
                <!-- Toggle Switch styling -->
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="create_account" id="createAccountToggle" value="1" {{ old('create_account') ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[var(--theme-active)]"></div>
                    <span class="ml-2.5 text-xs font-semibold text-gray-700">Create Login Credentials</span>
                </label>
            </div>
            
            <div id="accountFields" class="{{ old('create_account') ? '' : 'hidden' }} space-y-4 mt-2">
                <div class="bg-blue-50 border border-blue-100 p-3 rounded-lg text-xs text-blue-800 font-medium">
                    Enabling this will create a user account for the employee. The Contact Email entered above will be used as their login username.
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Initial Password *</label>
                        <input type="password" name="password" id="accPassword" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('password') border-red-300 @enderror" placeholder="••••••••">
                        @error('password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Assign Role *</label>
                        <select name="role" id="accRole" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('role') border-red-300 @enderror">
                            <option value="">Select a role...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-gray-600">Assign Locations (Branches) *</label>
                    <div class="bg-white border border-gray-300 rounded-lg p-3 max-h-36 overflow-y-auto divide-y divide-gray-100">
                        @foreach($locations as $loc)
                        <label class="flex items-center gap-3 py-2 first:pt-0 last:pb-0 cursor-pointer">
                            <input type="checkbox" name="locations[]" value="{{ $loc->id }}" class="rounded text-[var(--theme-active)] focus:ring-[var(--theme-active)] border-gray-300"
                                {{ is_array(old('locations')) && in_array($loc->id, old('locations')) ? 'checked' : '' }}>
                            <span class="text-xs text-gray-700 font-medium">{{ $loc->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('locations') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-gray-50 border-t border-gray-200 px-5 py-3.5 flex justify-end gap-2.5">
            <a href="{{ route('organization.employees.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 bg-white rounded-lg font-semibold text-xs hover:bg-gray-50 shadow-sm transition">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-[var(--theme-active)] text-[var(--theme-active-text)] rounded-lg font-semibold text-xs hover:opacity-90 shadow-sm transition">Save Employee</button>
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
