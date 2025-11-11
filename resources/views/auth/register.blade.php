<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Create Account</h2>
        <p class="text-gray-600">Sign up to get started</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-user text-gray-400"></i>
                </div>
                <x-text-input id="name" class="block w-full pl-10" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="John Doe" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-400"></i>
                </div>
                <x-text-input id="email" class="block w-full pl-10" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Role Selection -->
        <div>
            <x-input-label for="role" :value="__('Register As')" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-user-tag text-gray-400"></i>
                </div>
                <select id="role" name="role" class="block w-full pl-10 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required>
                    <option value="">Select your role</option>
                    <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>Student</option>
                    <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Teacher</option>
                </select>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Phone -->
            <div>
                <x-input-label for="phone" :value="__('Phone Number')" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-phone text-gray-400"></i>
                    </div>
                    <x-text-input id="phone" class="block w-full pl-10" type="tel" name="phone" :value="old('phone')" autocomplete="tel" placeholder="+62xxx" />
                </div>
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <!-- Birth Date -->
            <div>
                <x-input-label for="birth_date" :value="__('Birth Date')" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-calendar text-gray-400"></i>
                    </div>
                    <x-text-input id="birth_date" class="block w-full pl-10" type="date" name="birth_date" :value="old('birth_date')" />
                </div>
                <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
            </div>
        </div>

        <!-- Gender -->
        <div>
            <x-input-label for="gender" :value="__('Gender')" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-venus-mars text-gray-400"></i>
                </div>
                <select id="gender" name="gender" class="block w-full pl-10 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                    <option value="">Select gender</option>
                    <option value="laki-laki" {{ old('gender') == 'laki-laki' ? 'selected' : '' }}>Male</option>
                    <option value="perempuan" {{ old('gender') == 'perempuan' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
            <x-input-error :messages="$errors->get('gender')" class="mt-2" />
        </div>

        <!-- Address -->
        <div>
            <x-input-label for="address" :value="__('Address')" />
            <div class="relative mt-1">
                <div class="absolute top-3 left-3">
                    <i class="fas fa-map-marker-alt text-gray-400"></i>
                </div>
                <textarea id="address" name="address" rows="3" class="block w-full pl-10 pt-2 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" placeholder="Enter your full address">{{ old('address') }}</textarea>
            </div>
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <x-text-input id="password" class="block w-full pl-10" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <x-text-input id="password_confirmation" class="block w-full pl-10" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div>
            <x-primary-button class="w-full justify-center py-3 text-base font-semibold">
                <i class="fas fa-user-plus mr-2"></i>{{ __('Register') }}
            </x-primary-button>
        </div>

        <div class="text-center text-sm text-gray-600">
            <span>Already have an account? </span>
            <a class="text-blue-600 hover:text-blue-800 font-medium transition-colors" href="{{ route('login') }}">
                {{ __('Login here') }}
            </a>
        </div>
    </form>
</x-guest-layout>

