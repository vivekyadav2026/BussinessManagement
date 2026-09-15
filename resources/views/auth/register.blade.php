<x-guest-layout maxWidth="max-w-2xl">
    <x-slot name="header">
        <h2 class="text-center text-xl sm:text-2xl font-bold tracking-tight text-gray-900">Create your account</h2>
        <p class="mt-1 text-center text-xs sm:text-sm text-gray-600">
            Already registered?
            <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-500">Sign in instead</a>
        </p>
    </x-slot>

    <div x-data="{ 
        step: 1, 
        validateStep1() {
            let isValid = true;
            const requiredFields = ['organization_name', 'business_type', 'business_phone', 'country', 'state', 'pincode', 'address'];
            
            requiredFields.forEach(field => {
                const el = document.getElementById(field);
                if(!el.value) {
                    isValid = false;
                    el.classList.add('border-red-500', 'ring-red-500/20');
                } else {
                    el.classList.remove('border-red-500', 'ring-red-500/20');
                }
            });

            if(isValid) { this.step = 2; window.scrollTo({top:0, behavior:'smooth'}); }
            else alert('Please fill all required business details.');
        },
        validateStep2() {
            let isValid = true;
            const requiredFields = ['name', 'admin_phone', 'email', 'password', 'password_confirmation'];
            
            requiredFields.forEach(field => {
                const el = document.getElementById(field);
                if(!el.value) {
                    isValid = false;
                    el.classList.add('border-red-500', 'ring-red-500/20');
                } else {
                    el.classList.remove('border-red-500', 'ring-red-500/20');
                }
            });

            const pass = document.getElementById('password');
            const confirm = document.getElementById('password_confirmation');
            
            if(pass.value && confirm.value && pass.value !== confirm.value) {
                alert('Passwords do not match.');
                pass.classList.add('border-red-500');
                confirm.classList.add('border-red-500');
                isValid = false;
            }

            if(isValid) { this.step = 3; window.scrollTo({top:0, behavior:'smooth'}); }
            else if(isValid === false && pass.value === confirm.value) alert('Please fill all required admin details.');
        }
    }">

        <!-- Form -->
        <form method="POST" action="{{ route('register') }}" class="w-full">
            @csrf

            <!-- Progress Bar & Steps -->
            <div class="relative mb-8">
                <div class="overflow-hidden h-1.5 mb-5 text-xs flex rounded-full bg-gray-100">
                    <div :style="`width: ${step === 1 ? '33.33%' : (step === 2 ? '66.66%' : '100%')}`" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-indigo-600 transition-all duration-500 ease-in-out"></div>
                </div>
                
                <div class="flex justify-between items-center w-full px-2">
                    <div class="flex flex-col items-center">
                        <div :class="step >= 1 ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-100 text-gray-400'" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center font-bold text-[11px] sm:text-xs transition-all duration-300">
                            <svg x-show="step > 1" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span x-show="step === 1">1</span>
                        </div>
                        <span :class="step >= 1 ? 'text-gray-900' : 'text-gray-400'" class="text-[10px] uppercase font-bold mt-1.5">Business</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div :class="step >= 2 ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-100 text-gray-400'" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center font-bold text-[11px] sm:text-xs transition-all duration-300">
                            <svg x-show="step > 2" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span x-show="step <= 2">2</span>
                        </div>
                        <span :class="step >= 2 ? 'text-gray-900' : 'text-gray-400'" class="text-[10px] uppercase font-bold mt-1.5">Admin</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div :class="step >= 3 ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-100 text-gray-400'" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center font-bold text-[11px] sm:text-xs transition-all duration-300">
                            <span x-show="step <= 3">3</span>
                        </div>
                        <span :class="step >= 3 ? 'text-gray-900' : 'text-gray-400'" class="text-[10px] uppercase font-bold mt-1.5">Plan</span>
                    </div>
                </div>
            </div>

            <!-- Global Error Display -->
            @if ($errors->any())
                <div class="bg-red-50 text-red-700 p-3 rounded-xl text-xs font-medium mb-5 border border-red-100 shadow-sm">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- STEP 1: BUSINESS DETAILS -->
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-4">
                <div class="border-b border-gray-100 pb-3 mb-2">
                    <h3 class="text-base font-bold text-gray-900">1. Business Details</h3>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label for="organization_name" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Business Name <span class="text-red-500">*</span></label>
                        <input id="organization_name" name="organization_name" type="text" required class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2.5 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition" value="{{ old('organization_name') }}" placeholder="Acme Corporation">
                    </div>

                    <div>
                        <label for="business_type" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Business Type <span class="text-red-500">*</span></label>
                        <select id="business_type" name="business_type" required class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2.5 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition bg-white">
                            <option value="business" {{ old('business_type') === 'business' ? 'selected' : '' }}>Retail & Inventory ERP</option>
                            <option value="restaurant" {{ old('business_type') === 'restaurant' ? 'selected' : '' }}>Restaurant POS</option>
                        </select>
                    </div>
                    <div>
                        <label for="business_phone" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Business Mobile <span class="text-red-500">*</span></label>
                        <input id="business_phone" name="business_phone" type="tel" required class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2.5 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition" value="{{ old('business_phone') }}" placeholder="9876543210">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="gst_number" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">GST Number <span class="text-gray-400 font-normal lowercase">(Optional)</span></label>
                        <input id="gst_number" name="gst_number" type="text" class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2.5 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition uppercase placeholder:normal-case" value="{{ old('gst_number') }}" placeholder="22AAAAA0000A1Z5">
                    </div>

                    <div>
                        <label for="country" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Country <span class="text-red-500">*</span></label>
                        <input id="country" name="country" type="text" required class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2.5 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition" value="{{ old('country', 'India') }}">
                    </div>
                    <div>
                        <label for="state" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">State <span class="text-red-500">*</span></label>
                        <input id="state" name="state" type="text" required class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2.5 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition" value="{{ old('state') }}" placeholder="Gujarat">
                    </div>
                    
                    <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-1">
                            <label for="pincode" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Pincode <span class="text-red-500">*</span></label>
                            <input id="pincode" name="pincode" type="text" required class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2.5 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition" value="{{ old('pincode') }}" placeholder="380001">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="address" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Street Address <span class="text-red-500">*</span></label>
                            <input id="address" name="address" type="text" required class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2.5 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition" value="{{ old('address') }}" placeholder="Shop No 1, Main Market...">
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="button" @click="validateStep1()" class="flex w-full sm:w-auto justify-center rounded-xl border border-transparent bg-indigo-600 py-2.5 px-6 text-sm font-bold text-white shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out cursor-pointer">
                        Next: Admin Details &rarr;
                    </button>
                </div>
            </div>

            <!-- STEP 2: ADMIN DETAILS -->
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-4" style="display: none;">
                <div class="border-b border-gray-100 pb-3 mb-2">
                    <h3 class="text-base font-bold text-gray-900">2. Admin Profile</h3>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Full Name <span class="text-red-500">*</span></label>
                        <input id="name" name="name" type="text" autocomplete="name" required class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2.5 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition" value="{{ old('name') }}" placeholder="Rahul Sharma">
                    </div>
                    
                    <div>
                        <label for="admin_phone" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Mobile Number <span class="text-red-500">*</span></label>
                        <input id="admin_phone" name="admin_phone" type="tel" required class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2.5 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition" value="{{ old('admin_phone') }}" placeholder="9876543210">
                    </div>

                    <div>
                        <label for="email" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Email Address <span class="text-red-500">*</span></label>
                        <input id="email" name="email" type="email" autocomplete="email" required class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2.5 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition" value="{{ old('email') }}" placeholder="admin@company.com">
                    </div>

                    <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div x-data="{ show: false }">
                            <label for="password" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input id="password" name="password" :type="show ? 'text' : 'password'" required class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2.5 pr-10 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition" placeholder="••••••••">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg x-show="!show" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <svg x-show="show" style="display: none;" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                                </button>
                            </div>
                        </div>

                        <div x-data="{ show: false }">
                            <label for="password_confirmation" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Confirm Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input id="password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" required class="block w-full appearance-none rounded-xl border border-gray-300 px-3.5 py-2.5 pr-10 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm transition" placeholder="••••••••">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg x-show="!show" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <svg x-show="show" style="display: none;" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex flex-col sm:flex-row justify-between gap-3">
                    <button type="button" @click="step = 1; window.scrollTo({top:0, behavior:'smooth'})" class="order-2 sm:order-1 flex w-full sm:w-auto justify-center rounded-xl border border-gray-300 bg-white py-2.5 px-6 text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none transition cursor-pointer">
                        &larr; Back
                    </button>
                    <button type="button" @click="validateStep2()" class="order-1 sm:order-2 flex w-full sm:w-auto justify-center rounded-xl border border-transparent bg-indigo-600 py-2.5 px-6 text-sm font-bold text-white shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out cursor-pointer">
                        Next: Subscription &rarr;
                    </button>
                </div>
            </div>

            <!-- STEP 3: SUBSCRIPTION -->
            <div x-show="step === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-5" style="display: none;">
                <div class="border-b border-gray-100 pb-3 mb-2">
                    <h3 class="text-base font-bold text-gray-900">3. Subscription</h3>
                </div>
                
                @if(request('plan'))
                    <input type="hidden" name="plan" value="{{ request('plan') }}">
                    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-5 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-indigo-100 mb-3 text-indigo-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h4 class="text-sm font-bold text-indigo-900">Premium Plan Selected</h4>
                        <p class="text-xs text-indigo-700 mt-1">Your chosen plan will be applied upon registration.</p>
                    </div>
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-5 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-200 mb-3 text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h4 class="text-sm font-bold text-gray-900">14-Days Free Trial</h4>
                        <p class="text-xs text-gray-600 mt-1">Explore all Premium features risk-free. No credit card required.</p>
                    </div>
                @endif

                <div class="pt-4 flex flex-col sm:flex-row justify-between gap-3">
                    <button type="button" @click="step = 2; window.scrollTo({top:0, behavior:'smooth'})" class="order-2 sm:order-1 flex w-full sm:w-auto justify-center rounded-xl border border-gray-300 bg-white py-2.5 px-6 text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none transition cursor-pointer">
                        &larr; Back
                    </button>
                    <button type="submit" class="order-1 sm:order-2 flex w-full sm:w-auto justify-center rounded-xl border border-transparent bg-indigo-600 py-2.5 px-6 text-sm font-bold text-white shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out cursor-pointer">
                        Create Account &rarr;
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-guest-layout>