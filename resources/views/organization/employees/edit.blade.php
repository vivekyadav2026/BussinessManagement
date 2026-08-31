@extends('layouts.sme')

@push('styles')
<style>
    .switch-toggle {
        position: relative;
        display: inline-flex;
        align-items: center;
        cursor: pointer;
    }
    .switch-toggle input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    .switch-slider {
        width: 36px;
        height: 20px;
        background-color: #cbd5e1;
        border-radius: 20px;
        position: relative;
        transition: background-color 0.2s;
    }
    .switch-slider::before {
        content: "";
        position: absolute;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: white;
        top: 2px;
        left: 2px;
        transition: transform 0.2s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.15);
    }
    .switch-toggle input:checked + .switch-slider {
        background-color: var(--theme-active, #D99A2B);
    }
    .switch-toggle input:checked + .switch-slider::before {
        transform: translateX(16px);
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto px-4 py-2">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-3 mb-4 border-b border-gray-100">
        <a href="{{ route('organization.employees.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 hover:text-gray-900 hover:bg-gray-50 shadow-sm transition">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900 tracking-tight">Edit Employee: {{ $employee->full_name }}</h1>
            <p class="text-xs text-gray-500">Modify personal, professional, or system access parameters.</p>
        </div>
    </div>

    <!-- Form Container -->
    <form action="{{ route('organization.employees.update', $employee) }}" method="POST" class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        @csrf
        @method('PUT')

        @if ($errors->any())
        <div class="m-5 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex gap-2">
                <svg class="w-4 h-4 text-red-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <h3 class="text-xs font-bold text-red-800">Please correct the following errors:</h3>
                    <ul class="list-disc pl-4 mt-1.5 space-y-1 text-xs text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Section 1: Personal Details -->
        <div class="p-5 space-y-4">
            <div class="flex items-center gap-2 pb-1.5 border-b border-gray-50">
                <span class="w-1 h-3.5 bg-[var(--theme-active)] rounded-full"></span>
                <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Personal Details</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('first_name') border-red-300 @enderror" placeholder="e.g. John">
                    @error('first_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('last_name') border-red-300 @enderror" placeholder="e.g. Doe">
                    @error('last_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Employee Code</label>
                    <input type="text" name="employee_code" value="{{ old('employee_code', $employee->employee_code) }}" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('employee_code') border-red-300 @enderror" placeholder="e.g. EMP-101">
                    @error('employee_code') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Designation / Title</label>
                    <input type="text" name="designation" value="{{ old('designation', $employee->designation) }}" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('designation') border-red-300 @enderror" placeholder="e.g. Manager">
                    @error('designation') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('phone') border-red-300 @enderror" placeholder="e.g. +91 98765 43210">
                    @error('phone') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Joining Date</label>
                    <input type="date" name="joining_date" value="{{ old('joining_date', $employee->joining_date) }}" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('joining_date') border-red-300 @enderror">
                    @error('joining_date') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Address</label>
                    <textarea name="address" rows="2" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('address') border-red-300 @enderror" placeholder="e.g. Flat 101, Main Road, New Delhi">{{ old('address', $employee->address) }}</textarea>
                    @error('address') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Contact Email</label>
                    <input type="email" name="email" value="{{ old('email', $employee->email) }}" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('email') border-red-300 @enderror" placeholder="e.g. employee@company.com">
                    @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Assign Locations (Branches) <span class="text-red-500">*</span></label>
                    <div class="bg-white border border-gray-300 rounded-lg p-3 max-h-36 overflow-y-auto divide-y divide-gray-100">
                        @foreach($locations as $loc)
                        <label class="flex items-center gap-3 py-2 first:pt-0 last:pb-0 cursor-pointer">
                            <input type="checkbox" name="locations[]" value="{{ $loc->id }}" class="rounded text-[var(--theme-active)] focus:ring-[var(--theme-active)] border-gray-300"
                                {{ (is_array(old('locations')) && in_array($loc->id, old('locations'))) || (!old('locations') && in_array($loc->id, $userLocations)) ? 'checked' : '' }}>
                            <span class="text-xs text-gray-700 font-medium">{{ $loc->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('locations') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        
        <!-- Section 2: Account Access (if account exists) -->
        @if($employee->user)
        <div class="border-t border-gray-200 p-5 space-y-4 bg-gray-50/50">
            <div class="flex items-center justify-between pb-1.5 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="w-1 h-3.5 bg-[var(--theme-active)] rounded-full"></span>
                    <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider">System Login Access</h2>
                </div>
            </div>
            
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Assigned Role *</label>
                    <select name="role" required class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('role') border-red-300 @enderror">
                        @php $currentRole = $employee->user->roles->first()?->name; @endphp
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ (old('role') ?? $currentRole) == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Password Reset Section -->
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex items-center justify-between pb-1.5">
                    <span class="text-xs font-semibold text-gray-700">Change Account Password</span>
                    <!-- Reset password switch toggle -->
                    <label class="switch-toggle">
                        <input type="checkbox" name="reset_password" id="resetPassToggle" value="1" {{ old('reset_password') ? 'checked' : '' }}>
                        <div class="switch-slider"></div>
                        <span class="ml-2.5 text-xs font-semibold text-gray-750">Reset Password</span>
                    </label>
                </div>
                
                <div id="resetFields" class="{{ old('reset_password') ? '' : 'hidden' }} mt-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">New Password *</label>
                            <input type="password" name="password" id="newPassword" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('password') border-red-300 @enderror" placeholder="••••••••">
                            @error('password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Account Access (Create Login Account for Existing Employee) -->
        <div class="border-t border-gray-200 p-5 space-y-4 bg-gray-50/50">
            <div class="flex items-center justify-between pb-1.5 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="w-1 h-3.5 bg-[var(--theme-active)] rounded-full"></span>
                    <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider">System Login Access</h2>
                </div>
                
                <!-- Toggle Switch styling -->
                <label class="switch-toggle">
                    <input type="checkbox" name="create_account" id="createAccountToggle" value="1" {{ old('create_account') ? 'checked' : '' }}>
                    <div class="switch-slider"></div>
                    <span class="ml-2.5 text-xs font-semibold text-gray-750">Create Login Credentials</span>
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
            </div>

            <!-- No Access Notice if toggle is off -->
            <div id="noAccessNotice" class="{{ old('create_account') ? 'hidden' : '' }} bg-white border border-gray-200 rounded-lg p-4 flex items-center justify-between text-xs font-medium text-gray-500 shadow-sm">
                <span>This employee does not have a system login account.</span>
                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-md font-semibold tracking-wider text-[10px] uppercase border border-gray-200">No Access</span>
            </div>
        </div>
        @endif
        
        <!-- Footer Actions -->
        <div class="bg-gray-50 border-t border-gray-200 px-5 py-3.5 flex justify-end gap-2.5">
            <a href="{{ route('organization.employees.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 bg-white rounded-lg font-semibold text-xs hover:bg-gray-50 shadow-sm transition">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-[var(--theme-active)] text-[var(--theme-active-text)] rounded-lg font-semibold text-xs hover:opacity-90 shadow-sm transition">Update Employee</button>
        </div>
    </form>
</div>

<script>
    const resetToggle = document.getElementById('resetPassToggle');
    if (resetToggle) {
        resetToggle.addEventListener('change', function() {
            const fields = document.getElementById('resetFields');
            const pass = document.getElementById('newPassword');
            
            if (this.checked) {
                fields.classList.remove('hidden');
                pass.setAttribute('required', 'required');
            } else {
                fields.classList.add('hidden');
                pass.removeAttribute('required');
            }
        });
    }

    const createToggle = document.getElementById('createAccountToggle');
    if (createToggle) {
        createToggle.addEventListener('change', function() {
            const fields = document.getElementById('accountFields');
            const notice = document.getElementById('noAccessNotice');
            const pass = document.getElementById('accPassword');
            const role = document.getElementById('accRole');
            
            if (this.checked) {
                fields.classList.remove('hidden');
                notice.classList.add('hidden');
                pass.setAttribute('required', 'required');
                role.setAttribute('required', 'required');
            } else {
                fields.classList.add('hidden');
                notice.classList.remove('hidden');
                pass.removeAttribute('required');
                role.removeAttribute('required');
            }
        });
    }
</script>
@endsection
