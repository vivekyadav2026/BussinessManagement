@extends('layouts.sme')

@section('content')
<div class="p-6 max-w-4xl space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('organization.employees.index') }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-white border border-gray-200 text-gray-500 hover:text-gray-900 hover:bg-gray-50 shadow-sm transition">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Employee: {{ $employee->full_name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">Modify personal, professional, or system access parameters.</p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('organization.employees.update', $employee) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Personal Details -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-50 pb-3">Personal Details</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('first_name') border-red-300 @enderror">
                    @error('first_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}" class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('last_name') border-red-300 @enderror">
                    @error('last_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Employee Code</label>
                    <input type="text" name="employee_code" value="{{ old('employee_code', $employee->employee_code) }}" class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('employee_code') border-red-300 @enderror">
                    @error('employee_code') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Designation / Title</label>
                    <input type="text" name="designation" value="{{ old('designation', $employee->designation) }}" class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('designation') border-red-300 @enderror">
                    @error('designation') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('phone') border-red-300 @enderror">
                    @error('phone') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Joining Date</label>
                    <input type="date" name="joining_date" value="{{ old('joining_date', $employee->joining_date) }}" class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('joining_date') border-red-300 @enderror">
                    @error('joining_date') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Address</label>
                    <textarea name="address" rows="3" class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('address') border-red-300 @enderror">{{ old('address', $employee->address) }}</textarea>
                    @error('address') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Contact Email</label>
                    <input type="email" name="email" value="{{ old('email', $employee->email) }}" class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('email') border-red-300 @enderror">
                    @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        
        <!-- System Access (if account exists) -->
        @if($employee->user)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-50 pb-3">System Login Access</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Assigned Role *</label>
                    <select name="role" required class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('role') border-red-300 @enderror">
                        @php $currentRole = $employee->user->roles->first()?->name; @endphp
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ (old('role') ?? $currentRole) == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Assign Locations (Branches) *</label>
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 max-h-48 overflow-y-auto divide-y divide-gray-100">
                        @foreach($locations as $loc)
                        <label class="flex items-center gap-3 py-2.5 first:pt-0 last:pb-0 cursor-pointer">
                            <input type="checkbox" name="locations[]" value="{{ $loc->id }}" class="rounded text-[var(--theme-active)] focus:ring-[var(--theme-active)] border-gray-300"
                                {{ (is_array(old('locations')) && in_array($loc->id, old('locations'))) || (!old('locations') && in_array($loc->id, $userLocations)) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700 font-medium">{{ $loc->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('locations') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Password Reset -->
            <div class="mt-6 border-t border-gray-50 pt-6">
                <label class="flex items-center gap-2 cursor-pointer mb-4">
                    <input type="checkbox" name="reset_password" id="resetPassToggle" value="1" {{ old('reset_password') ? 'checked' : '' }} class="rounded text-[var(--theme-active)] focus:ring-[var(--theme-active)] border-gray-300">
                    <span class="text-sm font-semibold text-gray-750">Reset Login Password</span>
                </label>
                
                <div id="resetFields" class="{{ old('reset_password') ? '' : 'hidden' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">New Password *</label>
                            <input type="password" name="password" id="newPassword" class="w-full border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('password') border-red-300 @enderror">
                            @error('password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="bg-gray-50 border border-gray-100 rounded-2xl p-6 flex items-center justify-between">
            <div class="text-sm text-gray-500 font-medium">This employee does not have a system login account.</div>
            <div class="text-xs font-semibold px-2.5 py-1 bg-gray-200 text-gray-600 rounded-full">No Access</div>
        </div>
        @endif
        
        <!-- Submit Buttons -->
        <div class="flex justify-end gap-3 pt-4 pb-10">
            <a href="{{ route('organization.employees.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="px-5 py-2.5 bg-[var(--theme-active)] text-[var(--theme-active-text)] rounded-xl font-semibold text-sm hover:opacity-95 shadow-sm transition">Update Employee</button>
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
</script>
@endsection
