@extends('layouts.sme')

@section('content')
<div class="dash-head mb-6">
  <a href="{{ route('organization.employees.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 mb-2 inline-block">&larr; Back to Employees</a>
  <h1 class="text-2xl font-bold text-gray-900">Add New Employee</h1>
</div>

<form action="{{ route('organization.employees.store') }}" method="POST" class="max-w-4xl">
    @csrf
    
    <div class="panel mb-6">
        <h3 class="text-lg font-bold mb-4 pb-2 border-b">Personal Details</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                @error('first_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                <input type="text" name="last_name" value="{{ old('last_name') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                @error('last_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Employee Code</label>
                <input type="text" name="employee_code" value="{{ old('employee_code') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                @error('employee_code') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Designation / Title</label>
                <input type="text" name="designation" value="{{ old('designation') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                @error('designation') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                @error('phone') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Joining Date</label>
                <input type="date" name="joining_date" value="{{ old('joining_date') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                @error('joining_date') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea name="address" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">{{ old('address') }}</textarea>
                @error('address') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <div class="panel mb-6">
        <div class="flex items-center justify-between mb-4 pb-2 border-b">
            <h3 class="text-lg font-bold">System Login Access</h3>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="create_account" id="createAccountToggle" value="1" {{ old('create_account') ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500 border-gray-300">
                <span class="text-sm font-medium text-gray-700">Create Login Account</span>
            </label>
        </div>
        
        <div id="accountFields" class="{{ old('create_account') ? '' : 'hidden' }}">
            <div class="bg-indigo-50 border border-indigo-100 p-4 rounded-lg mb-4 text-sm text-indigo-800">
                Enabling this will create a user account for the employee. The Contact Email above will be used as their login ID.
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Initial Password *</label>
                    <input type="password" name="password" id="accPassword" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                    @error('password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assign Role *</label>
                    <select name="role" id="accRole" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="">Select a role...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Assign Locations (Branches) *</label>
                <div class="bg-white border border-gray-300 rounded-lg p-4 max-h-48 overflow-y-auto">
                    @foreach($locations as $loc)
                    <label class="flex items-center gap-3 py-1 cursor-pointer">
                        <input type="checkbox" name="locations[]" value="{{ $loc->id }}" class="rounded text-indigo-600 focus:ring-indigo-500"
                            {{ is_array(old('locations')) && in_array($loc->id, old('locations')) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">{{ $loc->name }}</span>
                    </label>
                    @endforeach
                </div>
                @error('locations') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
    
    <div class="flex justify-end gap-3 mb-10">
        <a href="{{ route('organization.employees.index') }}" class="btn bg-white border border-gray-300 text-gray-700">Cancel</a>
        <button type="submit" class="btn btn-gold">Save Employee</button>
    </div>
</form>

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
