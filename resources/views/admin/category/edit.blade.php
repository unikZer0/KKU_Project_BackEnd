<x-admin-layout>
<div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Edit Category</h1>
    <form action="{{ route('admin.category.update', $category->id) }}" method="POST" class="space-y-4" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text" 
                   name="name" 
                   value="{{ old('name', $category->name) }}" 
                   class="w-full border rounded px-3 py-2" 
                   placeholder="Microscope" 
                   required>
        </div>

        <div class="flex gap-3 items-center">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
            <a href="{{ route('admin.category.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded">Cancel</a>
        </div>
    </form>
</div>
</x-admin-layout>
