<x-admin-layout>
<div id="admin-table" data-requests='@json($requests)'>
    <admin-approve-table :requests='@json($requests)'></admin-approve-table>
</div>
</x-admin-layout>