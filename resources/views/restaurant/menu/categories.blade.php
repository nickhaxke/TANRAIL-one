@extends('layouts.restaurant')

@section('content')
<div class="max-w-7xl mx-auto w-full">

    <!-- Header & Action Row -->
    <div class="mb-8 border-b-2 border-gray-900 pb-4 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold tracking-widest text-gray-500 uppercase">Menu Organization</span>
                <span class="text-xs font-bold text-gray-400">|</span>
                <span class="text-xs font-bold text-gray-900 uppercase">{{ $businessUnit->name ?? 'Business Unit' }}</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tight">Menu Categories</h1>
        </div>
        
        <button onclick="document.getElementById('createCategoryModal').classList.remove('hidden')" class="px-5 py-2 border-2 border-gray-900 bg-gray-900 text-white font-bold text-sm uppercase tracking-wider hover:bg-gray-800 transition-colors flex items-center gap-2">
            Add Category &rarr;
        </button>
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

    <!-- Categories Table -->
    <div class="bg-white border-2 border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b-2 border-gray-200">
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Category Name</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Description</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-center">Items Count</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-center">Status</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-4 font-black text-gray-900">
                                {{ $category->name }}
                            </td>
                            <td class="py-3 px-4 text-xs font-bold text-gray-500 uppercase truncate max-w-xs">
                                {{ $category->description ?: '-' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 border border-gray-300 text-[10px] font-bold uppercase tracking-wider text-gray-700 bg-gray-100">
                                    {{ $category->items_count }} Items
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($category->status)
                                    <span class="px-2 py-0.5 bg-green-50 text-green-800 border border-green-200 font-bold uppercase tracking-wider text-[10px]">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 border border-gray-300 font-bold uppercase tracking-wider text-[10px]">
                                        Hidden
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <button onclick="editCategory({{ $category->toJson() }})" class="text-xs font-bold text-blue-600 hover:underline uppercase">
                                        Edit
                                    </button>
                                    
                                    <form action="{{ route('restaurant.menu.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-bold text-red-600 hover:underline uppercase">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500 font-medium">
                                No categories found. Create one to organize your POS menu.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Category Modal -->
<div id="createCategoryModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white border-2 border-gray-900 max-w-md w-full p-6 shadow-2xl">
        <div class="flex items-center justify-between border-b-2 border-gray-200 pb-4 mb-4">
            <div>
                <h3 class="text-xl font-black text-gray-900 uppercase tracking-widest">Add Category</h3>
            </div>
            <button onclick="document.getElementById('createCategoryModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-900 text-2xl font-bold transition-colors">&times;</button>
        </div>
        
        <form action="{{ route('restaurant.menu.categories.store') }}" method="POST" id="createForm" class="space-y-4">
            @csrf
            <input type="hidden" name="business_unit_id" value="{{ $businessUnit->id }}">
            
            <div>
                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Category Name *</label>
                <input type="text" name="name" required class="w-full bg-white border-2 border-gray-300 rounded-none px-3 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900" placeholder="e.g. Hot Beverages">
            </div>
            
            <div>
                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full bg-white border-2 border-gray-300 rounded-none px-3 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900" placeholder="e.g. Coffees, teas, and hot chocolates"></textarea>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t-2 border-gray-200 mt-4">
                <button type="button" onclick="document.getElementById('createCategoryModal').classList.add('hidden')" class="px-4 py-2 border-2 border-gray-300 hover:bg-gray-50 text-xs font-bold uppercase text-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 border-2 border-gray-900 bg-gray-900 hover:bg-gray-800 text-white font-bold text-xs uppercase tracking-wider transition-colors">
                    Save Category
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="editCategoryModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white border-2 border-gray-900 max-w-md w-full p-6 shadow-2xl">
        <div class="flex items-center justify-between border-b-2 border-gray-200 pb-4 mb-4">
            <div>
                <h3 class="text-xl font-black text-gray-900 uppercase tracking-widest">Edit Category</h3>
            </div>
            <button onclick="document.getElementById('editCategoryModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-900 text-2xl font-bold transition-colors">&times;</button>
        </div>
        
        <form action="" method="POST" id="editForm" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Category Name *</label>
                <input type="text" name="name" id="edit_name" required class="w-full bg-white border-2 border-gray-300 rounded-none px-3 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900">
            </div>
            
            <div>
                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Description</label>
                <textarea name="description" id="edit_description" rows="3" class="w-full bg-white border-2 border-gray-300 rounded-none px-3 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900"></textarea>
            </div>
            
            <div class="flex items-center gap-3 pt-2">
                <input type="checkbox" name="status" id="edit_status" value="1" class="w-4 h-4 border-2 border-gray-300 text-gray-900 focus:ring-gray-900">
                <label for="edit_status" class="text-xs font-bold text-gray-700 uppercase tracking-wider">Active (Visible in POS)</label>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t-2 border-gray-200 mt-4">
                <button type="button" onclick="document.getElementById('editCategoryModal').classList.add('hidden')" class="px-4 py-2 border-2 border-gray-300 hover:bg-gray-50 text-xs font-bold uppercase text-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 border-2 border-gray-900 bg-gray-900 hover:bg-gray-800 text-white font-bold text-xs uppercase tracking-wider transition-colors">
                    Update Category
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function editCategory(category) {
        document.getElementById('edit_name').value = category.name;
        document.getElementById('edit_description').value = category.description || '';
        document.getElementById('edit_status').checked = category.status == 1;
        
        let url = '{{ route("restaurant.menu.categories.update", ":id") }}'.replace(':id', category.id);
        document.getElementById('editForm').action = url;
        
        document.getElementById('editCategoryModal').classList.remove('hidden');
    }
</script>
@endsection