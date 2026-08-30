@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
  <div>
      <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
      <p class="text-gray-500 mt-1">Manage product categories.</p>
  </div>
  <button class="btn btn-gold btn-sm" onclick="document.getElementById('createCategoryModal').classList.remove('hidden')">+ Add Category</button>
</div>

@if(session('success'))
<div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 border border-green-200 text-sm font-medium">
    {{ session('success') }}
</div>
@endif

<div class="panel">
  <table class="inv-table w-full">
    <thead>
      <tr>
        <th>Category Name</th>
        <th>Slug</th>
        <th>Products</th>
        <th class="text-right">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($categories as $category)
      <tr>
        <td class="font-bold">{{ $category->name }}</td>
        <td class="text-gray-500">{{ $category->slug }}</td>
        <td>{{ $category->products()->count() }}</td>
        <td class="text-right flex justify-end gap-3 items-center">
            <button onclick="editCategory({{ $category->id }}, '{{ $category->name }}')" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs">Edit</button>
            @if($category->products()->count() === 0)
                <form action="{{ route('organization.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');" style="display:inline-block; margin:0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-xs">Delete</button>
                </form>
            @endif
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="4" class="text-center py-6 text-gray-500">No categories added yet.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

<!-- Create Modal -->
<div id="createCategoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4">Add Category</h3>
        <form action="{{ route('organization.categories.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" class="btn border" onclick="document.getElementById('createCategoryModal').classList.add('hidden')">Cancel</button>
                <button type="submit" class="btn btn-gold">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editCategoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4">Edit Category</h3>
        <form id="editCategoryForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" id="editCategoryName" required class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" class="btn border" onclick="document.getElementById('editCategoryModal').classList.add('hidden')">Cancel</button>
                <button type="submit" class="btn btn-gold">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function editCategory(id, name) {
    document.getElementById('editCategoryName').value = name;
    document.getElementById('editCategoryForm').action = `/organization/categories/${id}`;
    document.getElementById('editCategoryModal').classList.remove('hidden');
}
</script>
@endsection
