<section class="space-y-4">
    <header class="border-b border-rose-100 pb-4">
        <div class="flex items-center gap-2">
            <span class="text-base">⚠️</span>
            <h2 class="text-base font-extrabold text-rose-950">
                {{ __('Danger Zone: Delete Account') }}
            </h2>
        </div>
        <p class="mt-1 text-xs text-rose-700 font-medium">
            {{ __('Permanently remove your login profile, associations, and revoke active sessions.') }}
        </p>
    </header>

    <div class="p-4 bg-rose-50/70 border border-rose-200/90 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="text-xs text-rose-900 font-medium leading-relaxed max-w-xl">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before proceeding, please export or download any records you wish to retain.') }}
        </div>

        <button
            type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-extrabold text-xs rounded-lg shadow-xs transition"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            <span>{{ __('Delete Account') }}</span>
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 space-y-4">
            @csrf
            @method('delete')

            <div class="flex items-center gap-3 border-b border-slate-200 pb-3">
                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-lg shrink-0">
                    ⚠️
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-950">
                        {{ __('Are you sure you want to delete your account?') }}
                    </h2>
                    <p class="text-xs text-slate-500 font-medium">
                        This action cannot be undone.
                    </p>
                </div>
            </div>

            <p class="text-xs text-slate-600 font-medium leading-relaxed">
                {{ __('All data associated with this user profile will be permanently wiped. Please enter your account password to confirm deletion.') }}
            </p>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                    {{ __('Your Password') }} <span class="text-rose-500">*</span>
                </label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-950 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition shadow-2xs"
                    placeholder="{{ __('Enter password to confirm...') }}"
                    required
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1.5" />
            </div>

            <div class="pt-2 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition">
                    {{ __('Cancel') }}
                </button>

                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-extrabold text-xs rounded-lg shadow-xs transition">
                    {{ __('Confirm Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
