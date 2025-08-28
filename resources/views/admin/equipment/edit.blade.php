<x-app-layout>
<div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Edit Equipment</h1>

    <form action="{{ route('admin.equipment.update', $equipment->id) }}" 
          method="POST" 
          class="space-y-4" 
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input name="name" 
                   type="text" 
                   class="w-full border rounded px-3 py-2" 
                   value="{{ old('name', $equipment->name) }}" 
                   required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Description</label>
            <input name="description" 
                   type="text" 
                   class="w-full border rounded px-3 py-2" 
                   value="{{ old('description', $equipment->description) }}" 
                   required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Category</label>
            <select name="categories_id" class="w-full border rounded px-3 py-2" required>
                <option value="">Select...</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" 
                        {{ $equipment->categories_id == $c->id ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2" required>
                <option value="available" {{ $equipment->status == 'available' ? 'selected' : '' }}>available</option>
                <option value="unavailable" {{ $equipment->status == 'unavailable' ? 'selected' : '' }}>unavailable</option>
                <option value="maintenance" {{ $equipment->status == 'maintenance' ? 'selected' : '' }}>maintenance</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Image</label>
            <input name="image" type="file" accept="image/*" class="w-full border rounded px-3 py-2">
            
            @if($equipment->photo_path)
                <div class="mt-2">
                    <p class="text-sm text-gray-600">Current image:</p>
                    <img src="{{ $equipment->photo_path }}" 
                         alt="Current Image" 
                         class="w-32 h-auto border rounded mt-1">
                </div>
            @endif
        </div>

        <div class="flex gap-3 items-center">
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Update</button>
            <a href="{{ route('admin.equipment.index') }}" 
               class="px-4 py-2 bg-gray-400 text-white rounded">Cancel</a>
        </div>
    </form>
</div>
</x-app-layout>