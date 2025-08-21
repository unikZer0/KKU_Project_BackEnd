<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('ลืมรหัสผ่าน? ไม่ต้องกังวล เพียงแค่บอกเราว่าที่อยู่อีเมลของคุณคืออะไร และเราจะส่งลิงก์รีเซ็ตรหัสผ่านไปยังอีเมลของคุณ ซึ่งจะช่วยให้คุณเลือกอีเมลใหม่ได้') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <!-- Go Back Button -->
            <a href="{{ url()->previous() }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                ⬅ {{ __('ย้อนกลับ') }}
            </a>

            <!-- Submit -->
            <x-primary-button>
                {{ __('ลิงก์รีเซ็ตรหัสผ่านอีเมล') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
