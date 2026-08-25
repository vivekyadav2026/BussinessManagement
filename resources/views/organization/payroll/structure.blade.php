@extends('layouts.sme')

@section('content')
<div class="p-4 max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Salary Structure</h1>
        <a href="{{ route('organization.payroll.index') }}" class="text-blue-600 hover:underline">&larr; Back to Payroll</a>
    </div>

    <div class="bg-white p-6 rounded shadow border">
        <div class="mb-6 pb-4 border-b">
            <h2 class="text-xl font-bold">{{ $employee->first_name }} {{ $employee->last_name }}</h2>
            <p class="text-gray-500">Employee ID: #{{ $employee->id }}</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif
        
        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('organization.employees.salary-structure.store', $employee) }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <label class="block font-bold text-gray-700 mb-2">Basic Monthly Salary</label>
                <input type="number" step="0.01" name="basic_salary" value="{{ old('basic_salary', $structure->basic_salary ?? 0) }}" class="w-full border rounded px-3 py-2 text-xl" required>
            </div>

            <div class="grid grid-cols-2 gap-8 mb-6">
                <!-- Allowances -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block font-bold text-gray-700">Allowances</label>
                        <button type="button" onclick="addRow('allowances-container', 'allowances')" class="text-blue-600 text-sm font-medium">+ Add</button>
                    </div>
                    <div id="allowances-container" class="space-y-2">
                        @php $allowances = old('allowances', $structure->allowances ?? []); @endphp
                        @foreach($allowances as $index => $allowance)
                        <div class="flex gap-2 items-center row-item">
                            <input type="text" name="allowances[{{ $index }}][name]" value="{{ $allowance['name'] }}" placeholder="Name (e.g. HRA)" class="w-1/2 border rounded px-2 py-1">
                            <input type="number" step="0.01" name="allowances[{{ $index }}][amount]" value="{{ $allowance['amount'] }}" placeholder="Amount" class="w-1/2 border rounded px-2 py-1">
                            <button type="button" onclick="this.closest('.row-item').remove()" class="text-red-500">&times;</button>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Deductions -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block font-bold text-gray-700">Deductions</label>
                        <button type="button" onclick="addRow('deductions-container', 'deductions')" class="text-blue-600 text-sm font-medium">+ Add</button>
                    </div>
                    <div id="deductions-container" class="space-y-2">
                        @php $deductions = old('deductions', $structure->deductions ?? []); @endphp
                        @foreach($deductions as $index => $deduction)
                        <div class="flex gap-2 items-center row-item">
                            <input type="text" name="deductions[{{ $index }}][name]" value="{{ $deduction['name'] }}" placeholder="Name (e.g. PF)" class="w-1/2 border rounded px-2 py-1">
                            <input type="number" step="0.01" name="deductions[{{ $index }}][amount]" value="{{ $deduction['amount'] }}" placeholder="Amount" class="w-1/2 border rounded px-2 py-1">
                            <button type="button" onclick="this.closest('.row-item').remove()" class="text-red-500">&times;</button>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded shadow hover:bg-blue-700 w-full text-lg">Save Structure</button>
        </form>
    </div>
</div>

<script>
let counter = 1000;
function addRow(containerId, fieldName) {
    const container = document.getElementById(containerId);
    counter++;
    const html = `
        <div class="flex gap-2 items-center row-item">
            <input type="text" name="${fieldName}[${counter}][name]" placeholder="Name" class="w-1/2 border rounded px-2 py-1">
            <input type="number" step="0.01" name="${fieldName}[${counter}][amount]" placeholder="Amount" class="w-1/2 border rounded px-2 py-1">
            <button type="button" onclick="this.closest('.row-item').remove()" class="text-red-500">&times;</button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}
</script>
@endsection
