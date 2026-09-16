<section>
    <header class="border-b border-slate-200 pb-4 mb-6">
        <div class="flex items-center gap-2">
            <span class="text-base">🔑</span>
            <h2 class="text-base font-extrabold text-slate-950">
                {{ __('Update Password') }}
            </h2>
        </div>
        <p class="mt-1 text-xs text-slate-600 font-medium">
            {{ __('Ensure your account is using a long, strong password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                {{ __('Current Password') }} <span class="text-rose-500">*</span>
            </label>
            <input id="update_password_current_password" name="current_password" type="password" 
                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-950 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs" 
                autocomplete="current-password" required />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1.5" />
        </div>

        <div>
            <label for="update_password_password" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                {{ __('New Password') }} <span class="text-rose-500">*</span>
            </label>
            <input id="update_password_password" name="password" type="password" 
                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-950 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs" 
                autocomplete="new-password" required />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1.5" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                {{ __('Confirm Password') }} <span class="text-rose-500">*</span>
            </label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" 
                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-950 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs" 
                autocomplete="new-password" required />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1.5" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ __('Save Password') }}</span>
            </button>

            @if (session('status') === 'password-updated')
                <span
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="inline-flex items-center gap-1.5 text-xs font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg"
                >
                    <span>✓</span> {{ __('Password updated successfully.') }}
                </span>
            @endif
        </div>
    </form>
</section>
