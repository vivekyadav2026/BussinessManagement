@extends(auth()->user()->hasRole('Super Admin') ? 'layouts.super-admin' : 'layouts.sme')

@section('content')
<div class="dash-head mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Profile Settings</h1>
    <p class="text-gray-500 mt-1">Update your personal information and security settings.</p>
</div>

<div class="space-y-6 max-w-4xl">
    <div class="panel p-6 shadow-sm">
        <div class="max-w-xl">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <div class="panel p-6 shadow-sm">
        <div class="max-w-xl">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class="panel p-6 shadow-sm">
        <div class="max-w-xl">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
