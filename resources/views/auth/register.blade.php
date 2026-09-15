<x-guest-layout maxWidth="max-w-lg">
    <x-slot name="header">
        <h2 class="text-center text-xl sm:text-2xl font-bold tracking-tight text-gray-900">Create your business account</h2>
        <p class="mt-1 text-center text-xs sm:text-sm text-gray-600">
            Or
            <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-500">sign in to your existing account</a>
        </p>
    </x-slot>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Organization Name -->
        <div>
            <label for="organization_name" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Business Name</label>
            <div class="relative rounded-xl shadow-2xs">
                <input id="organization_name" name="organization_name" type="text" required autofocus class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition" value="{{ old('organization_name') }}" placeholder="e.g. Sharma Traders or Cafe Mocha">
            </div>
            <x-input-error :messages="$errors->get('organization_name')" class="mt-1" />
        </div>

        <!-- Business Type -->
        <div>
            <label for="business_type" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Business Type</label>
            <div class="relative rounded-xl shadow-2xs">
                <select id="business_type" name="business_type" required class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition bg-white cursor-pointer pr-10">
                    <option value="business" {{ old('business_type', request('type')) !== 'restaurant' ? 'selected' : '' }}>Retail & General Business</option>
                    <option value="restaurant" {{ old('business_type', request('type')) === 'restaurant' ? 'selected' : '' }}>Restaurant / Cafe / Cloud Kitchen</option>
                </select>
                <!-- Custom Arrow -->
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
            <x-input-error :messages="$errors->get('business_type')" class="mt-1" />
        </div>

        @if(request('plan'))
            <input type="hidden" name="plan" value="{{ request('plan') }}">
        @endif

        <div class="grid grid-cols-1 gap-y-4 gap-x-3.5 sm:grid-cols-2">
            <!-- Name -->
            <div>
                <label for="name" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Your Full Name</label>
                <div class="relative rounded-xl shadow-2xs">
                    <input id="name" name="name" type="text" autocomplete="name" required class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition" value="{{ old('name') }}" placeholder="Rahul Sharma">
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>
            
            <!-- Phone -->
            <div>
                <label for="phone" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Phone Number</label>
                <div class="relative rounded-xl shadow-2xs">
                    <input id="phone" name="phone" type="tel" required class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition" value="{{ old('phone') }}" placeholder="9876543210">
                </div>
                <x-input-error :messages="$errors->get('phone')" class="mt-1" />
            </div>
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Email address</label>
            <div class="relative rounded-xl shadow-2xs">
                <input id="email" name="email" type="email" autocomplete="email" required class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition" value="{{ old('email') }}" placeholder="name@company.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div class="grid grid-cols-1 gap-y-4 gap-x-3.5 sm:grid-cols-2">
            <!-- Password -->
            <div x-data="{ show: false }">
                <label for="password" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Password</label>
                <div class="relative rounded-xl shadow-2xs">
                    <input id="password" name="password" :type="show ? 'text' : 'password'" autocomplete="new-password" required class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2 pr-10 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition" placeholder="••••••••">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <button type="button" @click="show = !show" class="text-gray-400 hover:text-gray-600 focus:outline-none p-0.5">
                            <svg x-show="!show" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg x-show="show" style="display: none;" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <!-- Confirm Password -->
            <div x-data="{ show: false }">
                <label for="password_confirmation" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Confirm Password</label>
                <div class="relative rounded-xl shadow-2xs">
                    <input id="password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" autocomplete="new-password" required class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2 pr-10 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition" placeholder="••••••••">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <button type="button" @click="show = !show" class="text-gray-400 hover:text-gray-600 focus:outline-none p-0.5">
                            <svg x-show="!show" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg x-show="show" style="display: none;" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="flex w-full justify-center rounded-xl border border-transparent bg-indigo-600 py-2.5 px-4 text-sm font-bold text-white shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out cursor-pointer">
                Create Account &rarr;
            </button>
        </div>
    </form>
</x-guest-layout>
