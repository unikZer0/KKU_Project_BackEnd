<x-admin-layout>
    <div class="max-w-screen-2xl mx-auto py-6 px-3 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">จัดการคำขอยืมอุปกรณ์</h1>
        
        <div class="bg-white rounded-2xl shadow p-6">
            <div id="admin-table" data-requests='@json($requests)'>
                <admin-approve-table :requests='@json($requests)'></admin-approve-table>
            </div>
        </div>
    </div>
</x-admin-layout>
