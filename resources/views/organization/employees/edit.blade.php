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
                <span class="text-slate-950 font-extrabold">Edit {{ $employee->full_name }}</span>
            </nav>
            <div class="flex items-center gap-3">
                <a href="{{ route('organization.employees.index') }}" class="w-8 h-8 rounded-lg bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 flex items-center justify-center transition shadow-2xs" title="Back to Directory">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Edit Employee: {{ $employee->full_name }}</h1>
                    <p class="text-xs sm:text-sm text-slate-700 font-medium mt-0.5">Modify personal, branch assignment, or system security credentials.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('organization.employees.show', $employee) }}" class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-xs font-bold text-slate-800 transition shadow-2xs">
                View Full Profile &rarr;
            </a>
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

    <form action="{{ route('organization.employees.update', $employee) }}" method="POST" id="editEmployeeForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- LEFT / MAIN FORM COLUMN (8 cols) -->
            <div class="lg:col-span-8 bg-white rounded-xl border border-slate-200/90 shadow-2xs divide-y divide-slate-100 overflow-hidden">

                <!-- Section 1: Personal & Employment Details -->
                <div class="p-5 sm:p-6 space-y-5">
                    <div>
                        <h3 class="text-base font-bold text-slate-950">Personal &amp; Professional Details</h3>
                        <p class="text-xs text-slate-600 font-medium mt-0.5">Employee profile attributes and operational designations.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- First Name -->
                        <div>
                            <label class="block text-xs font-bold text-slate-900 mb-1.5">
                                First Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="first_name" 
                                   value="{{ old('first_name', $employee->first_name) }}" 
                                   required 
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
                                   value="{{ old('last_name', $employee->last_name) }}" 
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
                                   value="{{ old('employee_code', $employee->employee_code) }}" 
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
                                   value="{{ old('designation', $employee->designation) }}" 
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
                                   value="{{ old('email', $employee->email) }}" 
                                   class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-950 outline-none transition @error('email') border-rose-300 @enderror">
                            @error('email') <p class="text-rose-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-xs font-bold text-slate-900 mb-1.5">
                                Phone Number
                            </label>
                            <input type="text" 
                                   name="phone" 
                                   value="{{ old('phone', $employee->phone) }}" 
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
                                   value="{{ old('joining_date', $employee->joining_date) }}" 
                                   class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-950 outline-none transition @error('joining_date') border-rose-300 @enderror">
                            @error('joining_date') <p class="text-rose-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Branch Location Assignments -->
                        <div class="col-span-full">
                            <label class="block text-xs font-bold text-slate-900 mb-1.5">
                                Assigned Branch Locations <span class="text-rose-500">*</span>
                            </label>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 max-h-48 overflow-y-auto space-y-1">
                                @forelse($locations as $loc)
                                <label class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-white cursor-pointer transition-colors border border-transparent hover:border-slate-200">
                                    <input type="checkbox" 
                                           name="locations[]" 
                                           value="{{ $loc->id }}" 
                                           class="rounded text-amber-600 focus:ring-amber-500 border-slate-300 w-4 h-4"
                                           {{ in_array($loc->id, old('locations', $userLocations)) ? 'checked' : '' }}>
                                    <span class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                        <span>📍</span> {{ $loc->name }}
                                    </span>
                                </label>
                                @empty
                                <div class="text-xs text-slate-500 p-2 italic">No branches found.</div>
                                @endforelse
                            </div>
                            @error('locations') <p class="text-rose-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Residential Address -->
                        <div class="col-span-full">
                            <label class="block text-xs font-bold text-slate-900 mb-1.5">
                                Residential / Mailing Address
                            </label>
                            <textarea name="address" 
                                      rows="2" 
                                      class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg p-3 text-xs font-medium text-slate-950 outline-none transition @error('address') border-rose-300 @enderror">{{ old('address', $employee->address) }}</textarea>
                            @error('address') <p class="text-rose-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: System Login & Platform Access -->
                <div class="p-5 sm:p-6 space-y-5">
                    @if($employee->user)
                        <!-- Already has user account -->
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div>
                                <h3 class="text-base font-bold text-slate-950">Portal Login Access</h3>
                                <p class="text-xs text-slate-600 font-medium mt-0.5">This staff member holds an active login account.</p>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-950 border border-emerald-300">
                                <span>✓</span> User Active
                            </span>
                        </div>

                        <div class="space-y-4">
                            <!-- Role Selection -->
                            <div>
                                <label class="block text-xs font-bold text-slate-900 mb-1.5">
                                    Assigned System Role <span class="text-rose-500">*</span>
                                </label>
                                <select name="role" class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3 py-2.5 text-xs sm:text-sm font-bold text-slate-900 outline-none transition">
                                    @php
                                        $currentRole = $employee->user->roles->first()?->name;
                                    @endphp
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ old('role', $currentRole) === $role->name ? 'selected' : '' }}>
                                            🛡️ {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Password Reset Toggle -->
                            <div class="pt-3 border-t border-slate-100">
                                <label class="flex items-center gap-2 cursor-pointer select-none">
                                    <input type="checkbox" name="reset_password" id="resetPasswordCheckbox" value="1" class="rounded text-amber-600 focus:ring-amber-500 border-slate-300 w-4 h-4">
                                    <span class="text-xs font-bold text-slate-900">Reset Employee Login Password</span>
                                </label>

                                <div id="resetPasswordField" class="hidden mt-3 max-w-md">
                                    <label class="block text-xs font-bold text-slate-900 mb-1.5">New Password</label>
                                    <input type="password" 
                                           name="password" 
                                           id="newPasswordInput" 
                                           placeholder="Enter new password"
                                           class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3.5 py-2 text-xs sm:text-sm font-semibold text-slate-950 outline-none transition">
                                </div>
                            </div>
                        </div>

                    @else
                        <!-- Does NOT have user account yet -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <h3 class="text-base font-bold text-slate-950">System Login &amp; Portal Access</h3>
                                <p class="text-xs text-slate-600 font-medium mt-0.5">Enable login credentials for this staff member.</p>
                            </div>

                            <label class="switch-toggle shrink-0 select-none">
                                <input type="checkbox" name="create_account" id="createAccountToggle" value="1" {{ old('create_account') ? 'checked' : '' }}>
                                <div class="switch-slider"></div>
                                <span class="ml-2.5 text-xs font-bold text-slate-900">Enable Login</span>
                            </label>
                        </div>

                        <div id="accountFields" class="{{ old('create_account') ? '' : 'hidden' }} space-y-4 pt-4 border-t border-slate-100">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-900 mb-1.5">Initial Password *</label>
                                    <input type="password" 
                                           name="password" 
                                           id="accPassword" 
                                           placeholder="••••••••"
                                           class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-950 outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-900 mb-1.5">Assign Role *</label>
                                    <select name="role" id="accRole" class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3 py-2.5 text-xs sm:text-sm font-bold text-slate-900 outline-none transition">
                                        <option value="">Select a role...</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                                                🛡️ {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

            </div>

            <!-- RIGHT / SIDEBAR DETAILS (4 cols) -->
            <div class="lg:col-span-4 space-y-5">
                <!-- Summary Card -->
                <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 space-y-3">
                    <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Profile Overview</h3>
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <div class="w-11 h-11 rounded-full bg-slate-900 text-amber-400 font-extrabold text-sm flex items-center justify-center shrink-0 shadow-2xs">
                            {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name ?? '', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-slate-950 text-sm truncate">{{ $employee->full_name }}</div>
                            <div class="text-xs text-slate-600 font-medium">{{ $employee->designation ?? 'Staff Member' }}</div>
                        </div>
                    </div>

                    <dl class="divide-y divide-slate-100 text-xs">
                        <div class="py-2 flex justify-between">
                            <dt class="text-slate-500">Status</dt>
                            <dd class="font-bold text-slate-900">{{ ucfirst($employee->status) }}</dd>
                        </div>
                        <div class="py-2 flex justify-between">
                            <dt class="text-slate-500">Employee Code</dt>
                            <dd class="font-mono font-bold text-slate-900">{{ $employee->employee_code ?? 'N/A' }}</dd>
                        </div>
                        <div class="py-2 flex justify-between">
                            <dt class="text-slate-500">Created At</dt>
                            <dd class="font-medium text-slate-800">{{ $employee->created_at->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Danger Zone (Delete / Deactivate) -->
                <div class="bg-rose-50/60 rounded-xl border border-rose-200 p-5 space-y-3">
                    <h3 class="text-xs font-bold text-rose-900 uppercase tracking-wider">Account Actions</h3>
                    <p class="text-[11px] text-rose-700 leading-relaxed">
                        Deactivating removes POS access while maintaining historical sales records and shift logs.
                    </p>
                    <div class="flex items-center gap-2 pt-1">
                        <button type="button" 
                                onclick="document.getElementById('toggleStatusForm').submit()" 
                                class="w-full text-center px-3 py-2 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-xs font-bold text-slate-900 transition shadow-2xs">
                            {{ $employee->status === 'active' ? 'Deactivate Employee' : 'Activate Employee' }}
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <!-- Sticky Bottom Action Bar (~64px) -->
        <div class="sticky bottom-4 z-20 mt-8 bg-white/95 backdrop-blur-md border border-slate-300 rounded-xl shadow-lg px-5 py-3 flex items-center justify-between gap-4">
            <div class="text-xs font-medium text-slate-700">
                <span>Saving will update this employee across all assigned terminals.</span>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('organization.employees.index') }}" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-950 transition">
                    Cancel
                </a>
                <button type="submit" id="updateEmployeeBtn" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Update Employee</span>
                </button>
            </div>
        </div>

    </form>

    <form id="toggleStatusForm" action="{{ route('organization.employees.toggle-status', $employee) }}" method="POST" class="hidden">
        @csrf
        @method('PATCH')
    </form>

</div>

<script>
    const resetCheckbox = document.getElementById('resetPasswordCheckbox');
    if (resetCheckbox) {
        resetCheckbox.addEventListener('change', function() {
            const field = document.getElementById('resetPasswordField');
            const input = document.getElementById('newPasswordInput');
            if (this.checked) {
                field.classList.remove('hidden');
                input.setAttribute('required', 'required');
            } else {
                field.classList.add('hidden');
                input.removeAttribute('required');
            }
        });
    }

    const createToggle = document.getElementById('createAccountToggle');
    if (createToggle) {
        createToggle.addEventListener('change', function() {
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
    }

    document.getElementById('editEmployeeForm').addEventListener('submit', function() {
        const btn = document.getElementById('updateEmployeeBtn');
        btn.disabled = true;
        btn.innerHTML = `
            <svg class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 text-slate-950 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Updating...
        `;
    });
</script>
@endsection
