<x-admin-layout>
    <div id="category-table" 
        data-categories='@json($categories)'
        data-role="{{ Auth::user()->role }}"
        >
    </div>
</x-admin-layout>
