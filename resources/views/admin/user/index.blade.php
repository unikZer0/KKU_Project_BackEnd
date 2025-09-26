<x-admin-layout>
    <div id="user-table" 
        data-users='@json($users)'
        data-role="{{ Auth::user()->role }}">
    </div>
</x-admin-layout>