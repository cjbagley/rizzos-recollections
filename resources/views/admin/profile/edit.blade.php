<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <x-admin-card-holder>
        <x-admin-card>
            @include('admin.profile.partials.update-profile-information-form')
        </x-admin-card>
        <x-admin-card>
            @include('admin.profile.partials.update-password-form')
        </x-admin-card>
        <x-admin-card>
            @include('admin.profile.partials.delete-user-form')
        </x-admin-card>
    </x-admin-card-holder>
</x-admin-layout>
