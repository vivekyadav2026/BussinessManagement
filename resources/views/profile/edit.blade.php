@extends(auth()->user()->hasRole('Super Admin') ? 'layouts.super-admin' : 'layouts.sme')

@section('title', 'Profile & Account Settings')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumb & Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <span class="hover:text-slate-900 transition-colors">Settings</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="hover:text-slate-900 transition-colors">Account</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Profile Settings</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 text-xl font-black shadow-sm shrink-0">
                    👤
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Profile &amp; Account Settings</h1>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">
                        Manage your personal profile, credentials, security, and account preferences.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right User Badges -->
        @php
            $user = auth()->user();
            $roleName = 'User';
            if (method_exists($user, 'getRoleNames') && $user->getRoleNames()->isNotEmpty()) {
                $roleName = $user->getRoleNames()->first();
            } elseif (!empty($user->role)) {
                $roleName = ucfirst($user->role);
            }
        @endphp
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-900 border border-slate-300 shadow-2xs">
                <span class="text-slate-500 font-medium">Role:</span>
                <span>{{ $roleName }}</span>
            </span>

            @if($user->organization)
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-50 text-amber-950 border border-amber-300 shadow-2xs">
                <span class="text-amber-700 font-medium">Org:</span>
                <span class="truncate max-w-[150px]">{{ $user->organization->name }}</span>
            </span>
            @endif
        </div>
    </div>

    <!-- 2. Profile User Identity Summary Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-950 to-slate-900 text-white rounded-xl p-5 sm:p-6 shadow-sm border border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center font-black text-2xl shadow-md shrink-0">
                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-lg sm:text-xl font-black text-white tracking-tight">{{ $user->name }}</h2>
                    @if($user->hasVerifiedEmail())
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                            <span>✓</span> Verified Email
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-300 font-mono mt-0.5">{{ $user->email }}</p>
                <p class="text-[11px] text-slate-400 font-medium mt-1">
                    Member since {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                </p>
            </div>
        </div>
        
        <div class="flex items-center gap-2 self-end sm:self-center">
            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-800 text-slate-300 border border-slate-700">
                ID: #{{ $user->id }}
            </span>
        </div>
    </div>

    <!-- 3. Section 1: Profile Information -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-6 sm:p-8">
        <div class="max-w-2xl">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <!-- 4. Section 2: Password & Authentication Security -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-6 sm:p-8">
        <div class="max-w-2xl">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <!-- 5. Section 3: Danger Zone / Delete Account -->
    <div class="bg-white rounded-xl border border-rose-200 shadow-2xs p-6 sm:p-8">
        <div class="max-w-2xl">
            @include('profile.partials.delete-user-form')
        </div>
    </div>

</div>
@endsection
