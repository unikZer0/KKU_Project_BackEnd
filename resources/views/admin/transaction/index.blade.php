<x-admin-layout>
    <div class="bg-gray-100 p-6">
        <div class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow">
            <h1 class="text-2xl font-bold mb-4">Borrow Requests & Transactions</h1>

            @if (session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <table class="min-w-full bg-white border rounded-lg">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="p-2 border">ID</th>
                        <th class="p-2 border">วันที่จอง</th>
                        <th class="p-2 border">วันที่ส่งคืน</th>
                        <th class="p-2 border">วันที่ยืมของ</th>
                        <th class="p-2 border">วันที่คืนของ</th>
                        <th class="p-2 border">ค่าปรับ</th>
                        <th class="p-2 border">แอคชั่น</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $req)
                        <tr class="border-b">
                            <td class="p-2 border">{{ $req->req_id }}</td>
                            <td class="p-2 border">{{ $req->start_at }}</td>
                            <td class="p-2 border">{{ $req->end_at }}</td>
                            <td class="p-2 border">{{ $req->transaction?->checked_out_at ?? 'ยังไม่ได้เข้ามายืม' }}</td>
                            <td class="p-2 border">{{ $req->transaction?->checked_in_at ?? 'ยังไม่ได้ส่งคืน' }}</td>
                            <td class="p-2 border text-red-600 font-bold">
                                {{ $req->transaction?->penalty_amount ?? 'ยังไม่ได้คิดค่าปรับ' }}</td>
                            <td class="p-2 border">
                                @if ($req->transaction)
                                    @if (is_null($req->transaction->checked_out_at))
                                        <form action="{{ route('admin.transaction.checkout', $req->transaction->id) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">ยืมของ</button>
                                        </form>
                                    @elseif (is_null($req->transaction->checked_in_at))
                                        <form action="{{ route('admin.transaction.checkin', $req->transaction->id) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">คืนของ</button>
                                        </form>
                                    @else
                                        <span class="text-gray-500">สำเร็จ</span>
                                    @endif
                                @else
                                    <form action="{{ route('admin.transaction.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="equipments_id" value="1">
                                        <input type="hidden" name="end_at" value="{{ now()->addDays(3) }}">
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
