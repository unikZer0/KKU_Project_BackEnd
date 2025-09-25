<x-admin-layout>
    <div id="equipment-items-table" 
         data-equipment-items='@json($equipmentItems)'
         data-equipments='@json($equipments)'
         data-categories='@json($categories)'
         data-role="{{ Auth::user()->role }}">
        
        <equipment-items-table></equipment-items-table>
    </div>
</x-admin-layout>
