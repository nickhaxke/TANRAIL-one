@extends('layouts.restaurant')

@section('content')
<div class="max-w-7xl mx-auto w-full">

    <!-- Header & Action Row -->
    <div class="mb-8 border-b-2 border-gray-900 pb-4 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold tracking-widest text-gray-500 uppercase">Inventory Locations</span>
                <span class="text-xs font-bold text-gray-400">|</span>
                <span class="text-xs font-bold text-gray-900 uppercase">{{ $branch->name }}</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tight">Store Locations</h1>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" onclick="document.getElementById('createLocationModal').style.display = 'flex'" class="px-4 py-2 border-2 border-gray-900 bg-gray-900 text-white font-bold text-xs uppercase tracking-wider hover:bg-gray-800 transition-colors">
                + New Location
            </button>
        </div>
    </div>

    <!-- Flash Alerts -->
    @if(session('success'))
        <div class="mb-6 p-4 border-2 border-green-600 bg-green-50 text-green-800 font-bold text-sm uppercase tracking-wider">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 border-2 border-red-600 bg-red-50 text-red-800 font-bold text-sm uppercase tracking-wider">
            {{ session('error') }}
        </div>
    @endif

    <!-- Locations List -->
    <div class="bg-white border-2 border-gray-200 mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b-2 border-gray-200">
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Location Name</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Code</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-center">Status</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-right">Total Stock</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($locations as $location)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-4">
                                <div class="font-black text-gray-900 text-sm">{{ $location->name }}
                                    @if($branch->default_sales_location_id === $location->id)
                                        <span class="ml-2 px-2 py-0.5 bg-blue-50 text-blue-800 border border-blue-200 font-bold uppercase tracking-wider text-[10px]">Default Sales</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4 font-mono text-gray-600">{{ $location->code }}</td>
                            <td class="py-3 px-4 text-center">
                                @if($location->status === 'active')
                                    <span class="px-2 py-0.5 bg-green-50 text-green-800 border border-green-200 font-bold uppercase tracking-wider text-[10px]">Active</span>
                                @else
                                    <span class="px-2 py-0.5 bg-gray-50 text-gray-600 border border-gray-200 font-bold uppercase tracking-wider text-[10px]">Inactive</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-black font-mono text-gray-900">
                                {{ number_format($location->total_stock, 1) }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <button type="button" onclick="openEditLocationModal({{ $location->id }}, '{{ addslashes($location->name) }}', '{{ addslashes($location->code) }}', '{{ $location->status }}')" class="text-xs font-bold text-blue-600 hover:underline uppercase">
                                        Edit
                                    </button>
                                    @if($location->status === 'active' && $branch->default_sales_location_id !== $location->id)
                                        <form action="{{ route('restaurant.locations.default', $location->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs font-bold text-gray-600 hover:text-gray-900 hover:underline uppercase" onclick="return confirm('Set this location as the default sales point?')">
                                                Set Default
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500 font-medium">
                                No inventory locations found for this branch.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Create Location Modal -->
<div id="createLocationModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white border-2 border-gray-900 shadow-2xl w-full max-w-md">
        <div class="px-6 py-4 border-b-2 border-gray-900 flex justify-between items-center bg-gray-50">
            <h2 class="text-lg font-black text-gray-900 uppercase tracking-wider">New Location</h2>
            <button onclick="document.getElementById('createLocationModal').style.display = 'none'" class="text-gray-500 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <form action="{{ route('restaurant.locations.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Location Name</label>
                    <input type="text" name="name" required class="w-full border-2 border-gray-300 p-2 text-sm focus:border-gray-900 focus:outline-none" placeholder="e.g. Main Kitchen">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Location Code</label>
                    <input type="text" name="code" required class="w-full border-2 border-gray-300 p-2 text-sm focus:border-gray-900 focus:outline-none" placeholder="e.g. MK-01">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Status</label>
                    <select name="status" class="w-full border-2 border-gray-300 p-2 text-sm focus:border-gray-900 focus:outline-none">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('createLocationModal').style.display = 'none'" class="px-4 py-2 border-2 border-gray-300 text-gray-600 font-bold text-sm uppercase tracking-wider hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-gray-900 border-2 border-gray-900 text-white font-bold text-sm uppercase tracking-wider hover:bg-gray-800">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Location Modal -->
<div id="editLocationModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white border-2 border-gray-900 shadow-2xl w-full max-w-md">
        <div class="px-6 py-4 border-b-2 border-gray-900 flex justify-between items-center bg-gray-50">
            <h2 class="text-lg font-black text-gray-900 uppercase tracking-wider">Edit Location</h2>
            <button onclick="document.getElementById('editLocationModal').style.display = 'none'" class="text-gray-500 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <form id="editLocationForm" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Location Name</label>
                    <input type="text" name="name" id="edit_name" required class="w-full border-2 border-gray-300 p-2 text-sm focus:border-gray-900 focus:outline-none">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Location Code</label>
                    <input type="text" name="code" id="edit_code" required class="w-full border-2 border-gray-300 p-2 text-sm focus:border-gray-900 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Status</label>
                    <select name="status" id="edit_status" class="w-full border-2 border-gray-300 p-2 text-sm focus:border-gray-900 focus:outline-none">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('editLocationModal').style.display = 'none'" class="px-4 py-2 border-2 border-gray-300 text-gray-600 font-bold text-sm uppercase tracking-wider hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-gray-900 border-2 border-gray-900 text-white font-bold text-sm uppercase tracking-wider hover:bg-gray-800">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditLocationModal(id, name, code, status) {
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_code').value = code;
    document.getElementById('edit_status').value = status;
    document.getElementById('editLocationForm').action = '/restaurant/locations/' + id;
    document.getElementById('editLocationModal').style.display = 'flex';
}
</script>
@endsection
