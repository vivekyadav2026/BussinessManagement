<section>
    <header class="border-b border-slate-200 pb-4 mb-6">
        <div class="flex items-center gap-2">
            <span class="text-base">👤</span>
            <h2 class="text-base font-extrabold text-slate-950">
                {{ __('Profile Information') }}
            </h2>
        </div>
        <p class="mt-1 text-xs text-slate-600 font-medium">
            {{ __("Update your account's display name and primary login email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                {{ __('Full Name') }} <span class="text-rose-500">*</span>
            </label>
            <input id="name" name="name" type="text" 
                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-950 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs" 
                value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                {{ __('Email Address') }} <span class="text-rose-500">*</span>
            </label>
            <input id="email" name="email" type="email" 
                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-950 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs" 
                value="{{ old('email', $user->email) }}" required autocomplete="username" />
            <x-input-error class="mt-1.5" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs">
                    <p class="text-amber-950 font-medium">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline font-bold text-amber-900 hover:text-amber-700 ml-1">
                            {{ __('Click here to re-send verification link.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-bold text-emerald-700">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ __('Save Profile') }}</span>
            </button>

            @if (session('status') === 'profile-updated')
                <span
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="inline-flex items-center gap-1.5 text-xs font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg"
                >
                    <span>✓</span> {{ __('Profile updated successfully.') }}
                </span>
            @endif
        </div>
    </form>
</section>
