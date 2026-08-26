@extends('layouts.sme')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Salary Structure</h1>
        <a href="{{ route('organization.payroll.index') }}" class="text-indigo-600 hover:underline text-sm">&larr; Back to Payroll</a>
    </div>

    <div class="panel p-6 shadow-sm">
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</h2>
            <p class="text-xs text-gray-400 font-mono">Employee ID: #{{ $employee->id }}</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 border border-green-200 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif
        
        @if($errors->any())
            <div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg mb-6 border border-red-200 text-sm font-medium">
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
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Basic Monthly Salary (₹)</label>
                <input type="number" step="0.01" name="basic_salary" value="{{ old('basic_salary', $structure->basic_salary ?? 0) }}" class="w-full border rounded px-3 py-2 text-xl font-bold font-mono" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                <!-- Allowances -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Allowances</label>
                        <button type="button" onclick="addRow('allowances-container', 'allowances')" class="text-indigo-600 text-xs font-bold hover:underline">+ Add</button>
                    </div>
                    <div id="allowances-container" class="space-y-2">
                        @php $allowances = old('allowances', $structure->allowances ?? []); @endphp
                        @foreach($allowances as $index => $allowance)
                        <div class="flex gap-2 items-center row-item">
                            <input type="text" name="allowances[{{ $index }}][name]" value="{{ $allowance['name'] }}" placeholder="Name (e.g. HRA)" class="w-1/2 border rounded px-2 py-1 text-sm">
                            <input type="number" step="0.01" name="allowances[{{ $index }}][amount]" value="{{ $allowance['amount'] }}" placeholder="Amount" class="w-1/2 border rounded px-2 py-1 text-sm font-mono">
                            <button type="button" onclick="this.closest('.row-item').remove()" class="text-red-500 text-lg font-bold">&times;</button>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Deductions -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Deductions</label>
                        <button type="button" onclick="addRow('deductions-container', 'deductions')" class="text-indigo-600 text-xs font-bold hover:underline">+ Add</button>
                    </div>
                    <div id="deductions-container" class="space-y-2">
                        @php $deductions = old('deductions', $structure->deductions ?? []); @endphp
                        @foreach($deductions as $index => $deduction)
                        <div class="flex gap-2 items-center row-item">
                            <input type="text" name="deductions[{{ $index }}][name]" value="{{ $deduction['name'] }}" placeholder="Name (e.g. PF)" class="w-1/2 border rounded px-2 py-1 text-sm">
                            <input type="number" step="0.01" name="deductions[{{ $index }}][amount]" value="{{ $deduction['amount'] }}" placeholder="Amount" class="w-1/2 border rounded px-2 py-1 text-sm font-mono">
                            <button type="button" onclick="this.closest('.row-item').remove()" class="text-red-500 text-lg font-bold">&times;</button>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-gold py-2.5 px-6 w-full justify-center text-lg">Save Structure</button>
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
            <input type="text" name="${fieldName}[${counter}][name]" placeholder="Name" class="w-1/2 border rounded px-2 py-1 text-sm">
            <input type="number" step="0.01" name="${fieldName}[${counter}][amount]" placeholder="Amount" class="w-1/2 border rounded px-2 py-1 text-sm font-mono">
            <button type="button" onclick="this.closest('.row-item').remove()" class="text-red-500 text-lg font-bold">&times;</button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}
</script>
@endsection
