<x-admin-layout>
    <div id="accessories-table" 
         data-accessories='@json($accessories)'
         data-equipments='@json($equipments)'
         data-role="{{ Auth::user()->role }}">
        
        <accessories-table></accessories-table>
    </div>
</x-admin-layout>
