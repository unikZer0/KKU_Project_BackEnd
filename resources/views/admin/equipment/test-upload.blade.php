{{-- Simple test form for uploading equipment --}}
<x-app-layout>
<div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Test Upload Equipment</h1>
    <form action="{{ route('admin.equipment.upload') }}" method="POST" class="space-y-4" enctype="multipart/form-data">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input name="name" type="text" class="w-full border rounded px-3 py-2" placeholder="Microscope" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">description</label>
            <input name="description" type="text" class="w-full border rounded px-3 py-2" placeholder="description" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Category</label>
            <select name="categories_id" class="w-full border rounded px-3 py-2" required>
                <option value="">Select...</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2" required>
                <option value="available">available</option>
                <option value="unavailable">unavailable</option>
                <option value="maintenance">maintenance</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Image</label>
            <input name="image" type="file" accept="image/*" class="w-full border rounded px-3 py-2">
        </div>

        <div class="flex gap-3 items-center">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Submit</button>
        </div>
    </form>
</div>
</x-app-layout>


