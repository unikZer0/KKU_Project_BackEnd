<x-guest-layout>
    <div class="max-w-md mx-auto mt-10 bg-white shadow-lg rounded-lg p-8">
        <h2 class="text-2xl font-bold text-black mb-6 text-center">ลงทะเบียน</h2>
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Student/Employee ID -->
            <div class="mb-4">
                <x-input-label for="uid" :value="__('รหัสนักศึกษา/รหัสพนักงาน')" class="text-black !important" />
                <x-text-input id="uid" class="block mt-1 w-full border-gray-300 rounded-md px-4 py-2 bg-black-50 text-black focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    type="text" name="uid" :value="old('uid')" placeholder="รหัสนักศึกษา/รหัสพนักงาน..." required autofocus autocomplete="uid" />
                <x-input-error :messages="$errors->get('uid')" class="mt-2" />
            </div>

            <!-- Name -->
            <div class="mb-4">
                <x-input-label for="name" :value="__('ชื่อผู้ใข้')" class="text-black" />
                <x-text-input id="name" class="block mt-1 w-full border-gray-300 rounded-md px-4 py-2 bg-gray-50 text-black focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    type="text" name="name" :value="old('name')" placeholder="ชื่อผู้ใช้..." required autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mb-4">
                <x-input-label for="email" :value="__('อีเมล')" class="text-black" />
                <x-text-input id="email" class="block mt-1 w-full border-gray-300 rounded-md px-4 py-2 bg-gray-50 text-black focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    type="email" name="email" :value="old('email')" placeholder="อีเมล..." required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Phone Number -->
            <div class="mb-4">
                <x-input-label for="phonenumber" :value="__('หมายเลขโทรศัพท์')" class="text-black" />
                <x-text-input id="phonenumber" class="block mt-1 w-full border-gray-300 rounded-md px-4 py-2 bg-gray-50 text-black focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    type="text" name="phonenumber" :value="old('phonenumber')" placeholder="หมายเลขโทรศัพท์..." required autocomplete="phonenumber" />
                <x-input-error :messages="$errors->get('phonenumber')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mb-4">
                <x-input-label for="password" :value="__('รหัสผ่าน')" class="text-black" />
                <x-text-input id="password" class="block mt-1 w-full border-gray-300 rounded-md px-4 py-2 bg-gray-50 text-black focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    type="password" name="password" placeholder="อย่างน้อย 8 ตัวอักษร ตัวพิมพ์ใหญ่ ตัวเลข และ ตัวอักษรพิเศษ" required autocomplete="new-password" />
                <ul id="password-errors" class="text-red-500 mt-2 text-sm"></ul>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <x-input-label for="password_confirmation" :value="__('ยืนยันรหัสผ่าน')" class="text-black" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full border-gray-300 rounded-md px-4 py-2 bg-gray-50 text-black focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    type="password" name="password_confirmation" placeholder="ยืนยันรหัสผ่าน..." required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('login') }}">
                    {{ __('ลงทะเบียนไว้ก่อนหน้าแล้ว?') }}
                </a>
                <x-primary-button class="ml-4">
                    {{ __('ลงทะเบียน') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById("password").addEventListener("input", function () {
            const password = this.value;
            const errors = [];

            if (password.length < 8) {
                errors.push("รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร");
            }
            if (!/[A-Z]/.test(password)) {
                errors.push("ต้องมีอักษรตัวพิมพ์ใหญ่ (A-Z)");
            }
            if (!/[a-z]/.test(password)) {
                errors.push("ต้องมีอักษรตัวพิมพ์เล็ก (a-z)");
            }
            if (!/[0-9]/.test(password)) {
                errors.push("ต้องมีตัวเลข (0-9)");
            }
            if (!/[!@#$%^&*(),.?\":{}|<>]/.test(password)) {
                errors.push("ต้องมีอักษรพิเศษ (!@#$...)");
            }

            const errorList = document.getElementById("password-errors");
            errorList.innerHTML = "";
            errors.forEach(err => {
                const li = document.createElement("li");
                li.textContent = err;
                errorList.appendChild(li);
            });
        });
    </script>
</x-guest-layout>
