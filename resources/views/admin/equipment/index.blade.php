<x-admin-layout>
    <div id="equipment-table"
        data-equipments='@json($equipments)'
        data-categories='@json($categories)'
        data-role="{{ Auth::user()->role }}">
    </div>
</x-admin-layout>
