<x-guest-layout maxWidth="max-w-xl">
    <x-slot name="header">
        <h2 class="mt-4 text-center text-2xl sm:text-3xl font-bold tracking-tight text-gray-900">Create your business account</h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Or
            <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-500">sign in to your existing account</a>
        </p>
    </x-slot>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Organization Name -->
        <div>
            <label for="organization_name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Business Name</label>
            <div class="mt-1">
                <input id="organization_name" name="organization_name" type="text" required autofocus class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2.5 placeholder-gray-400 shadow-2xs focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition" value="{{ old('organization_name') }}" placeholder="e.g. Sharma Traders or Cafe Mocha">
            </div>
            <x-input-error :messages="$errors->get('organization_name')" class="mt-1.5" />
        </div>

        <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-2">
            <!-- Name -->
            <div>
                <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Your Full Name</label>
                <div class="mt-1">
                    <input id="name" name="name" type="text" autocomplete="name" required class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2.5 placeholder-gray-400 shadow-2xs focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition" value="{{ old('name') }}" placeholder="Rahul Sharma">
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
            </div>
            
            <!-- Phone -->
            <div>
                <label for="phone" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Phone Number</label>
                <div class="mt-1">
                    <input id="phone" name="phone" type="tel" required class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2.5 placeholder-gray-400 shadow-2xs focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition" value="{{ old('phone') }}" placeholder="9876543210">
                </div>
                <x-input-error :messages="$errors->get('phone')" class="mt-1.5" />
            </div>
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Email address</label>
            <div class="mt-1">
                <input id="email" name="email" type="email" autocomplete="email" required class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2.5 placeholder-gray-400 shadow-2xs focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition" value="{{ old('email') }}" placeholder="name@company.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-2">
            <!-- Password -->
            <div x-data="{ show: false }">
                <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Password</label>
                <div class="mt-1 relative rounded-xl shadow-2xs">
                    <input id="password" name="password" :type="show ? 'text' : 'password'" autocomplete="new-password" required class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2.5 pr-11 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition" placeholder="••••••••">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3.5">
                        <button type="button" @click="show = !show" class="text-gray-400 hover:text-gray-600 focus:outline-none p-1">
                            <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg x-show="show" style="display: none;" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            </div>

            <!-- Confirm Password -->
            <div x-data="{ show: false }">
                <label for="password_confirmation" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Confirm Password</label>
                <div class="mt-1 relative rounded-xl shadow-2xs">
                    <input id="password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" autocomplete="new-password" required class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2.5 pr-11 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition" placeholder="••••••••">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3.5">
                        <button type="button" @click="show = !show" class="text-gray-400 hover:text-gray-600 focus:outline-none p-1">
                            <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg x-show="show" style="display: none;" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="flex w-full justify-center rounded-xl border border-transparent bg-indigo-600 py-3 px-4 text-sm font-bold text-white shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out cursor-pointer">
                Create Account &rarr;
            </button>
        </div>
    </form>
</x-guest-layout>
