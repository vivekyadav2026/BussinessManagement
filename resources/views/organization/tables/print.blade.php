<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print QR Codes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .page-break { page-break-after: always; }
        }
    </style>
</head>
<body class="bg-gray-100 p-8">
    <div class="no-print mb-8 flex justify-between items-center max-w-5xl mx-auto bg-white p-4 shadow rounded">
        <h1 class="font-bold text-lg">Print QR Codes</h1>
        <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded font-bold hover:bg-blue-700">Print / Save PDF</button>
    </div>

    <div class="max-w-5xl mx-auto grid grid-cols-2 md:grid-cols-3 gap-8 print:grid-cols-3 print:gap-4 print:max-w-none">
        @foreach($tables as $table)
            <div class="bg-white border-2 border-gray-800 p-6 flex flex-col items-center justify-center text-center rounded-xl shadow-sm print:shadow-none print:border-gray-400">
                <div class="font-bold text-2xl mb-4 text-gray-900">{{ $table->name }}</div>
                
                <div class="mb-4">
                    {!! QrCode::size(160)->generate(route('public.menu.table', $table->public_token)) !!}
                </div>
                
                <div class="text-xs text-gray-500 mt-2">Scan to order</div>
                <div class="font-bold text-sm mt-1">{{ $table->organization->name }} - {{ $table->location->name }}</div>
            </div>
        @endforeach
    </div>
</body>
</html>
