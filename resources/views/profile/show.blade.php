<x-app-layout>
    <div class="max-w-3xl mx-auto py-6 px-4">
        @if (session('success'))
            <script>
                Swal.fire({ icon: 'success', title: 'สำเร็จ!', text: '{{ session('success') }}', timer: 2200, showConfirmButton: false });
            </script>
        @endif

        <h1 class="text-2xl font-bold mb-4">โปรไฟล์ของฉัน</h1>
        <div class="bg-white border rounded-lg p-4 space-y-3">
            <p><span class="font-semibold">รหัสผู้ใช้:</span> {{ $user->uid }}</p>
            <p><span class="font-semibold">ชื่อ:</span> {{ $user->name }}</p>
            <p><span class="font-semibold">อีเมล:</span> {{ $user->email }}</p>
            <p><span class="font-semibold">สถานะการยืนยันอีเมล:</span> {{ $user->email_verified_at ? 'ยืนยันแล้ว' : 'ยังไม่ยืนยัน' }}</p>
            <p><span class="font-semibold">สถานะการยืนยันตัวตน:</span> {{ ($user->verified ?? 0) ? 'ยืนยันแล้ว' : 'รอการยืนยัน/ยังไม่ยืนยัน' }}</p>
        </div>

        <form class="mt-4" action="{{ route('profile.requestVerification') }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">ขอยืนยันตัวตน</button>
        </form>
    </div>
</x-app-layout>


