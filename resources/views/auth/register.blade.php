<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="uid" :value="__('รหัสนักศึกษา/รหัสพนักงาน')" />
            <x-text-input id="uid" class="block mt-1 w-full" type="text" name="uid" :value="old('uid')"
                placeholder="รหัสนักศึกษา/รหัสพนักงาน..." required autofocus autocomplete="uid" />
            <x-input-error :messages="$errors->get('uid')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="name" :value="__('ชื่อผู้ใข้')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')"
                placeholder="ໍชื่อผู้ใช้..." required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('อีเมล')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                placeholder="อีเมล..." required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Phone Number -->
        <div class="mt-4">
            <x-input-label for="phonenumber" :value="__('หมายเลขโทรศัพท์')" />
            <x-text-input id="phonenumber" class="block mt-1 w-full" type="text" name="phonenumber"
                :value="old('phonenumber')" placeholder="หมายเลขโทรศัพท์..." required autofocus autocomplete="phonenumber" />
            <x-input-error :messages="$errors->get('phonenumber')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('รหัสผ่าน')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                placeholder="รหัสผ่าน..." required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('ยืนยันรหัสผ่าน')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                placeholder="ยืนยันรหัสผ่าน..." name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                href="{{ route('login') }}">
                {{ __('ลงทะเบียนไว้ก่อนหน้าแล้ว?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('ลงทะเบียน') }}
            </x-primary-button>
        </div>


    </form>
</x-guest-layout>
