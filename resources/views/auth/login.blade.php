<x-guest-layout>
    <div class="max-w-md mx-auto py-6 px-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-lg transition overflow-hidden">
            <div class="p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6 text-center">
                    {{ __('เข้าสู่ระบบ') }}
                </h2>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-4">
                        <x-input-label for="email" :value="__('อีเมล')" />
                        <x-text-input id="email" 
                                      class="block mt-1 w-full"
                                      type="email" 
                                      name="email" 
                                      :value="old('email')" 
                                      placeholder="อีเมล..."
                                      required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <x-input-label for="password" :value="__('รหัสผ่าน')" />
                        <x-text-input id="password" 
                                      class="block mt-1 w-full"
                                      type="password"
                                      name="password"
                                      placeholder="รหัสผ่าน..."
                                      required autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center mb-4">
                        <input id="remember_me" type="checkbox" 
                               class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" 
                               name="remember">
                        <label for="remember_me" class="ms-2 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('จดจำฉันไว้') }}
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col gap-3">
                        <x-primary-button class="w-full justify-center">
                            {{ __('เข้าสู่ระบบ') }}
                        </x-primary-button>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100"
                               href="{{ route('password.request') }}">
                                {{ __('ลืมรหัสผ่าน?') }}
                            </a>
                        @endif

                        <a href="{{ route('register') }}"
                           class="text-sm text-center text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                            {{ __('ยังไม่มีบัญชี? สมัครสมาชิก') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
