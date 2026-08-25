@extends('layouts.sme')

@section('content')
<div class="dash-head mb-6">
  <a href="{{ route('organization.employees.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 mb-2 inline-block">&larr; Back to Employees</a>
  <h1 class="text-2xl font-bold text-gray-900">Edit Employee: {{ $employee->full_name }}</h1>
</div>

<form action="{{ route('organization.employees.update', $employee) }}" method="POST" class="max-w-4xl">
    @csrf
    @method('PUT')
    
    <div class="panel mb-6">
        <h3 class="text-lg font-bold mb-4 pb-2 border-b">Personal Details</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                @error('first_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                @error('last_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Employee Code</label>
                <input type="text" name="employee_code" value="{{ old('employee_code', $employee->employee_code) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                @error('employee_code') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Designation / Title</label>
                <input type="text" name="designation" value="{{ old('designation', $employee->designation) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                @error('designation') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                @error('phone') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Joining Date</label>
                <input type="date" name="joining_date" value="{{ old('joining_date', $employee->joining_date) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                @error('joining_date') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea name="address" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">{{ old('address', $employee->address) }}</textarea>
                @error('address') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                <input type="email" name="email" value="{{ old('email', $employee->email) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    @if($employee->user)
    <div class="panel mb-6">
        <h3 class="text-lg font-bold mb-4 pb-2 border-b">System Login Access</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Assigned Role *</label>
                <select name="role" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                    @php $currentRole = $employee->user->roles->first()?->name; @endphp
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ (old('role') ?? $currentRole) == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Assign Locations (Branches) *</label>
                <div class="bg-white border border-gray-300 rounded-lg p-4 max-h-48 overflow-y-auto">
                    @foreach($locations as $loc)
                    <label class="flex items-center gap-3 py-1 cursor-pointer">
                        <input type="checkbox" name="locations[]" value="{{ $loc->id }}" class="rounded text-indigo-600 focus:ring-indigo-500"
                            {{ (is_array(old('locations')) && in_array($loc->id, old('locations'))) || (!old('locations') && in_array($loc->id, $userLocations)) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">{{ $loc->name }}</span>
                    </label>
                    @endforeach
                </div>
                @error('locations') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mt-6 border-t pt-4">
            <label class="flex items-center gap-2 cursor-pointer mb-4">
                <input type="checkbox" name="reset_password" id="resetPassToggle" value="1" {{ old('reset_password') ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500 border-gray-300">
                <span class="text-sm font-medium text-gray-700">Reset Password</span>
            </label>
            
            <div id="resetFields" class="{{ old('reset_password') ? '' : 'hidden' }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password *</label>
                        <input type="password" name="password" id="newPassword" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        @error('password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="panel mb-6 bg-gray-50 flex items-center justify-between">
        <div class="text-sm text-gray-600">This employee does not have a system login account.</div>
        <div class="text-xs bg-gray-200 text-gray-700 px-3 py-1 rounded">No Access</div>
    </div>
    @endif
    
    <div class="flex justify-end gap-3 mb-10">
        <a href="{{ route('organization.employees.index') }}" class="btn bg-white border border-gray-300 text-gray-700">Cancel</a>
        <button type="submit" class="btn btn-gold">Update Employee</button>
    </div>
</form>

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
