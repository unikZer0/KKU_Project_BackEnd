<x-admin-layout>
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Categories</h2>

        <ul class="list-disc ml-6">
            @foreach($categories as $category)
                <li>{{ $category->name }} ({{ $category->equipments_count }} items)</li>
            @endforeach
        </ul>
    </div>
</x-admin-layout>
