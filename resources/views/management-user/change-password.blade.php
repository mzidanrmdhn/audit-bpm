@extends('layout.dashboard')
@section('title', 'Ubah Password')
@section('content')

<div class="flex flex-row justify-between items-center pb-3">
    <p class="text-xl font-bold text-gray-600 whitespace-nowrap">Ganti Password</p>

</div>
<div class="bg-white p-4 mt-3 rounded-lg shadow overflow-x-auto">
    @if (session('success'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li class="list-disc list-inside">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('user.updatePassword', ['id' => $user->id]) }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="old_password" class="text-sm text-gray-700">Password Lama</label>
            <div class="relative mt-1">
                <input type="password" id="old_password" name="old_password" class="text-sm w-full border border-gray-300 rounded-md p-2 pr-10" value="{{ old('old_password') }}" required>
                <svg id="eye-old_password" class="w-6 h-6 absolute right-3 top-1/2 transform -translate-y-1/2 icon text-jet" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-width="2" d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z"/>
                    <path stroke="currentColor" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                </svg>
                <svg id="eyeslash-old_password" class="w-6 h-6 absolute right-3 top-1/2 transform -translate-y-1/2 icon text-jet hidden" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.933 13.909A4.357 4.357 0 0 1 3 12c0-1 4-6 9-6m7.6 3.8A5.068 5.068 0 0 1 21 12c0 1-3 6-9 6-.314 0-.62-.014-.918-.04M5 19 19 5m-4 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                </svg>
            </div>
        </div>
        <div class="mb-4">
            <label for="new_password" class="text-sm text-gray-700">Password Baru</label>
            <div class="relative mt-1">
                <input type="password" id="new_password" name="new_password" class="text-sm w-full border border-gray-300 rounded-md p-2 pr-10" value="{{ old('new_password') }}" required>
                <svg id="eye-new_password" class="w-6 h-6 absolute right-3 top-1/2 transform -translate-y-1/2 icon text-jet" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-width="2" d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z"/>
                    <path stroke="currentColor" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                </svg>
                <svg id="eyeslash-new_password" class="w-6 h-6 absolute right-3 top-1/2 transform -translate-y-1/2 icon text-jet hidden" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.933 13.909A4.357 4.357 0 0 1 3 12c0-1 4-6 9-6m7.6 3.8A5.068 5.068 0 0 1 21 12c0 1-3 6-9 6-.314 0-.62-.014-.918-.04M5 19 19 5m-4 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                </svg>
            </div>
        </div>
        <div class="mb-4">
            <label for="new_password_confirmation" class="text-sm text-gray-700">Konfirmasi Password Baru</label>
            <div class="relative mt-1">
                <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="text-sm w-full border border-gray-300 rounded-md p-2 pr-10" value="{{ old('new_password_confirmation') }}" required>
                <svg id="eye-new_password_confirmation" class="w-6 h-6 absolute right-3 top-1/2 transform -translate-y-1/2 icon text-jet" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-width="2" d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z"/>
                    <path stroke="currentColor" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                </svg>
                <svg id="eyeslash-new_password_confirmation" class="w-6 h-6 absolute right-3 top-1/2 transform -translate-y-1/2 icon text-jet hidden" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.933 13.909A4.357 4.357 0 0 1 3 12c0-1 4-6 9-6m7.6 3.8A5.068 5.068 0 0 1 21 12c0 1-3 6-9 6-.314 0-.62-.014-.918-.04M5 19 19 5m-4 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                </svg>
            </div>
        </div>
        <button type="submit" class="text-sm bg-caribbean text-white px-4 py-2 rounded-lg">Ganti Password</button>
    </form>
</div>

<script>

    function setupPasswordToggle(eyeIconId, eyeSlashIconId, passwordFieldId) {
        const eyeIcon = document.getElementById(eyeIconId);
        const eyeSlashIcon = document.getElementById(eyeSlashIconId);
        const passwordField = document.getElementById(passwordFieldId);

        eyeIcon.addEventListener('click', function () {
            passwordField.type = 'text';
            eyeIcon.classList.add('hidden');
            eyeSlashIcon.classList.remove('hidden');
        });

        eyeSlashIcon.addEventListener('click', function () {
            passwordField.type = 'password';
            eyeSlashIcon.classList.add('hidden');
            eyeIcon.classList.remove('hidden');
        });
    }

    // Initialize password toggles
    setupPasswordToggle('eye-old_password', 'eyeslash-old_password', 'old_password');
    setupPasswordToggle('eye-new_password', 'eyeslash-new_password', 'new_password');
    setupPasswordToggle('eye-new_password_confirmation', 'eyeslash-new_password_confirmation', 'new_password_confirmation');
</script>
@endsection
