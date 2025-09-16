<x-guest-layout>
    <div class="max-w-md mx-auto py-6 px-4">
        <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition overflow-hidden">
            <div class="p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-black mb-6 text-center">
                    {{ __('เข้าสู่ระบบ') }}
                </h2>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-4">
                        <x-input-label for="email" :value="__('อีเมล')" class="text-black" />
                        <x-text-input id="email" 
                                      class="block mt-1 w-full bg-white text-black border-gray-300"
                                      type="email" 
                                      name="email" 
                                      :value="old('email')" 
                                      placeholder="อีเมล..."
                                      required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <x-input-label for="password" :value="__('รหัสผ่าน')" class="text-black" />
                        <x-text-input id="password" 
                                      class="block mt-1 w-full bg-white text-black border-gray-300"
                                      type="password"
                                      name="password"
                                      placeholder="รหัสผ่าน..."
                                      required autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center mb-4">
                        <input id="remember_me" type="checkbox" 
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" 
                               name="remember">
                        <label for="remember_me" class="ms-2 text-sm text-black">
                            {{ __('จดจำฉันไว้') }}
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col gap-3">
                        <x-primary-button class="w-full justify-center">
                            {{ __('เข้าสู่ระบบ') }}
                        </x-primary-button>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-center text-black hover:text-indigo-700"
                               href="{{ route('password.request') }}">
                                {{ __('ลืมรหัสผ่าน?') }}
                            </a>
                        @endif

                        <a href="{{ route('register') }}"
                           class="text-sm text-center text-indigo-600 hover:text-indigo-800">
                            {{ __('ยังไม่มีบัญชี? สมัครสมาชิก') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
