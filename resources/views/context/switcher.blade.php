<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Business Context</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Select Business Unit</h1>
        
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($businessUnits->isEmpty())
            <p class="text-red-500 text-center">You are not assigned to any active Business Units.</p>
        @else
            <form action="{{ route('context.switch') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    @foreach($businessUnits as $bu)
                        <button type="submit" name="business_unit_id" value="{{ $bu->id }}" class="w-full text-left px-4 py-3 border border-gray-300 rounded-md hover:bg-indigo-50 hover:border-indigo-500 transition duration-150 ease-in-out">
                            <span class="block font-medium text-gray-900">{{ $bu->name }}</span>
                            <span class="block text-sm text-gray-500">Code: {{ $bu->code }}</span>
                        </button>
                    @endforeach
                </div>
            </form>
        @endif
    </div>
</body>
</html>
