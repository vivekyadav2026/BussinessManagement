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
        width: 38px;
        height: 22px;
        background-color: #cbd5e1;
        border-radius: 20px;
        position: relative;
        transition: background-color 0.2s;
    }
    .switch-slider::before {
        content: "";
        position: absolute;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background-color: white;
        top: 2px;
        left: 2px;
        transition: transform 0.2s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .switch-toggle input:checked + .switch-slider {
        background-color: #D99A2B;
    }
    .switch-toggle input:checked + .switch-slider::before {
        transform: translateX(16px);
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-24">

    <!-- 1. Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <a href="{{ route('organization.employees.index') }}" class="hover:text-slate-900 transition-colors">Employees</a>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Add New Employee</span>
            </nav>
            <div class="flex items-center gap-3">
                <a href="{{ route('organization.employees.index') }}" class="w-8 h-8 rounded-lg bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 flex items-center justify-center transition shadow-2xs" title="Back to Directory">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Add New Employee</h1>
                    <p class="text-xs sm:text-sm text-slate-700 font-medium mt-0.5">Register a new team member and configure their workspace permissions.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback Errors -->
    @if (isset($errors) && $errors->any())
    <div class="bg-rose-50 border border-rose-300 text-rose-950 px-4 py-3.5 rounded-xl text-xs shadow-2xs">
        <div class="font-extrabold mb-1 text-rose-900">Please correct the following errors:</div>
        <ul class="list-disc list-inside space-y-0.5 text-rose-800 font-medium">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('organization.employees.store') }}" method="POST" id="employeeForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- LEFT / MAIN FORM COLUMN (8 cols) -->
            <div class="lg:col-span-8 bg-white rounded-xl border border-slate-200/90 shadow-2xs divide-y divide-slate-100 overflow-hidden">

                <!-- Section 1: Personal Details -->
                <div class="p-5 sm:p-6 space-y-5">
                    <div>
                        <h3 class="text-base font-bold text-slate-950">Personal &amp; Professional Details</h3>
                        <p class="text-xs text-slate-600 font-medium mt-0.5">Basic identity coordinates and employment record.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- First Name -->
                        <div>
                            <label class="block text-xs font-bold text-slate-900 mb-1.5">
                                First Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="first_name" 
                                   value="{{ old('first_name') }}" 
                                   required 
                                   placeholder="e.g. Rahul"
                                   class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-950 outline-none transition @error('first_name') border-rose-300 @enderror">
                            @error('first_name') <p class="text-rose-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label class="block text-xs font-bold text-slate-900 mb-1.5">
                                Last Name
                            </label>
                            <input type="text" 
                                   name="last_name" 
                                   value="{{ old('last_name') }}" 
                                   placeholder="e.g. Sharma"
                                   class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-950 outline-none transition @error('last_name') border-rose-300 @enderror">
                            @error('last_name') <p class="text-rose-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Employee Code -->
                        <div>
                            <label class="block text-xs font-bold text-slate-900 mb-1.5">
                                Employee Code
                            </label>
                            <input type="text" 
                                   name="employee_code" 
                                   value="{{ old('employee_code') }}" 
                                   placeholder="e.g. EMP-101"
                                   class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3.5 py-2.5 text-xs sm:text-sm font-mono font-bold text-slate-950 outline-none transition @error('employee_code') border-rose-300 @enderror">
                            @error('employee_code') <p class="text-rose-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Designation -->
                        <div>
                            <label class="block text-xs font-bold text-slate-900 mb-1.5">
                                Designation / Title
                            </label>
                            <input type="text" 
                                   name="designation" 
                                   value="{{ old('designation') }}" 
                                   placeholder="e.g. Store Manager, Cashier, Head Chef"
                                   class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-950 outline-none transition @error('designation') border-rose-300 @enderror">
                            @error('designation') <p class="text-rose-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Contact Email -->
                        <div>
                            <label class="block text-xs font-bold text-slate-900 mb-1.5">
                                Contact Email
                            </label>
                            <input type="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="employee@company.com"
                                   class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-950 outline-none transition @error('email') border-rose-300 @enderror">
                            <p class="text-[11px] text-slate-500 mt-1">Used as username if portal login is enabled below.</p>
                            @error('email') <p class="text-rose-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label class="block text-xs font-bold text-slate-900 mb-1.5">
                                Phone Number
                            </label>
                            <input type="text" 
                                   name="phone" 
                                   value="{{ old('phone') }}" 
                                   placeholder="+91 98765 43210"
                                   class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-950 outline-none transition @error('phone') border-rose-300 @enderror">
                            @error('phone') <p class="text-rose-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Joining Date -->
                        <div class="col-span-full">
                            <label class="block text-xs font-bold text-slate-900 mb-1.5">
                                Joining Date
                            </label>
                            <input type="date" 
                                   name="joining_date" 
                                   value="{{ old('joining_date', date('Y-m-d')) }}" 
                                   class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-950 outline-none transition @error('joining_date') border-rose-300 @enderror">
                            @error('joining_date') <p class="text-rose-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Branch Location Assignments -->
                        <div class="col-span-full">
                            <label class="block text-xs font-bold text-slate-900 mb-1.5">
                                Assign Branch Locations <span class="text-rose-500">*</span>
                            </label>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 max-h-48 overflow-y-auto space-y-1">
                                @forelse($locations as $loc)
                                <label class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-white cursor-pointer transition-colors border border-transparent hover:border-slate-200">
                                    <input type="checkbox" 
                                           name="locations[]" 
                                           value="{{ $loc->id }}" 
                                           class="rounded text-amber-600 focus:ring-amber-500 border-slate-300 w-4 h-4"
                                           {{ is_array(old('locations')) && in_array($loc->id, old('locations')) ? 'checked' : (count($locations) === 1 ? 'checked' : '') }}>
                                    <span class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                        <span>📍</span> {{ $loc->name }}
                                    </span>
                                </label>
                                @empty
                                <div class="text-xs text-slate-500 p-2 italic">No branches registered. All staff default to Main Organization.</div>
                                @endforelse
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1">Staff will only be allowed to punch attendance and view invoices for checked branches.</p>
                            @error('locations') <p class="text-rose-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Physical Address -->
                        <div class="col-span-full">
                            <label class="block text-xs font-bold text-slate-900 mb-1.5">
                                Residential / Mailing Address
                            </label>
                            <textarea name="address" 
                                      rows="2" 
                                      placeholder="Street address, city, state, postal code"
                                      class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg p-3 text-xs font-medium text-slate-950 outline-none transition @error('address') border-rose-300 @enderror">{{ old('address') }}</textarea>
                            @error('address') <p class="text-rose-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: System Login & Platform Access -->
                <div class="p-5 sm:p-6 space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h3 class="text-base font-bold text-slate-950">System Login &amp; Portal Access</h3>
                            <p class="text-xs text-slate-600 font-medium mt-0.5">Permit this employee to log in to the POS or backoffice dashboard.</p>
                        </div>

                        <!-- Toggle Switch -->
                        <label class="switch-toggle shrink-0 select-none">
                            <input type="checkbox" name="create_account" id="createAccountToggle" value="1" {{ old('create_account') ? 'checked' : '' }}>
                            <div class="switch-slider"></div>
                            <span class="ml-2.5 text-xs font-bold text-slate-900">Enable Login</span>
                        </label>
                    </div>

                    <!-- Conditional Credentials Box -->
                    <div id="accountFields" class="{{ old('create_account') ? '' : 'hidden' }} space-y-4 pt-4 border-t border-slate-100">
                        <div class="p-3 rounded-lg bg-indigo-50/70 border border-indigo-200 text-xs text-indigo-950 flex items-start gap-2.5">
                            <span class="text-indigo-600 font-bold text-sm">ℹ️</span>
                            <span class="font-medium leading-relaxed">
                                The <strong>Contact Email</strong> provided above will serve as the unique login username for this staff member.
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Initial Password -->
                            <div>
                                <label class="block text-xs font-bold text-slate-900 mb-1.5">
                                    Initial Password <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" 
                                       name="password" 
                                       id="accPassword" 
                                       placeholder="••••••••"
                                       class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-950 outline-none transition @error('password') border-rose-300 @enderror">
                                <p class="text-[11px] text-slate-500 mt-1">Minimum 8 characters recommended.</p>
                                @error('password') <p class="text-rose-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Role Assignment -->
                            <div>
                                <label class="block text-xs font-bold text-slate-900 mb-1.5">
                                    Assign Role / Permission Tier <span class="text-rose-500">*</span>
                                </label>
                                <select name="role" 
                                        id="accRole" 
                                        class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3 py-2.5 text-xs sm:text-sm font-bold text-slate-900 outline-none transition @error('role') border-rose-300 @enderror">
                                    <option value="">Select an operational role...</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                                            🛡️ {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-slate-500 mt-1">Controls which POS &amp; backoffice menus they can access.</p>
                                @error('role') <p class="text-rose-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT / SIDEBAR GUIDES (4 cols) -->
            <div class="lg:col-span-4 space-y-5">
                <!-- Guide Card 1 -->
                <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 space-y-3">
                    <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Onboarding Checklist</h3>
                    <ul class="space-y-2.5 text-xs text-slate-700">
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><strong>Personal ID:</strong> Accurate name and phone for shift logs.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><strong>Branch Access:</strong> Select all locations where this employee works.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><strong>Login Access:</strong> Only enable for managers, cashiers, or captains needing POS screens.</span>
                        </li>
                    </ul>
                </div>

                <!-- Guide Card 2 -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 text-xs text-slate-700 space-y-2">
                    <div class="font-bold text-slate-950 flex items-center gap-1.5">
                        <span>🛡️</span> Role-Based Security
                    </div>
                    <p class="leading-relaxed text-[11px]">
                        Staff members will only see modules authorized by their role. Organization Admin permissions remain exclusive to primary tenant owners.
                    </p>
                </div>
            </div>

        </div>

        <!-- 3. Compact Sticky Bottom Action Bar (~64px) -->
        <div class="sticky bottom-4 z-20 mt-8 bg-white/95 backdrop-blur-md border border-slate-300 rounded-xl shadow-lg px-5 py-3 flex items-center justify-between gap-4">
            <div class="text-xs font-medium text-slate-700">
                <span>Fill all required fields (<span class="text-rose-500 font-bold">*</span>) before saving.</span>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('organization.employees.index') }}" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-950 transition">
                    Cancel
                </a>
                <button type="submit" id="saveEmployeeBtn" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Save Employee</span>
                </button>
            </div>
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

    document.getElementById('employeeForm').addEventListener('submit', function() {
        const btn = document.getElementById('saveEmployeeBtn');
        btn.disabled = true;
        btn.innerHTML = `
            <svg class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 text-slate-950 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Saving Employee...
        `;
    });
</script>
@endsection
