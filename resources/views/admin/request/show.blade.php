<x-admin-layout>
    <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-xl font-bold mb-4">Request #{{ $requests->id }}</h2>
        <p><strong>ผู้ใช้:</strong> {{ $requests->user->name }}</p>
        <p><strong>อุปกรณ์:</strong> {{ $requests->equipment->name }}</p>
        <p><strong>วันที่ส่งคำขอ:</strong> {{ $requests->created_at->format('Y-m-d H:i') }}</p>
        <!-- Add more fields as needed -->
    </div>
</x-admin-layout>
