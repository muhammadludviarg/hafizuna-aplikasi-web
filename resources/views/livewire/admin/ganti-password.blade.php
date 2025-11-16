<div>
    {{--
    Layout Admin Anda (layouts/admin.blade.php)
    juga tidak memiliki slot 'header',
    jadi kita langsung ke konten.
    --}}

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- 1. Sertakan form info profil --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    {{-- Kita teruskan variabel $user --}}
                    @include('profile.partials.update-profile-information-form', ['user' => $user])
                </div>
            </div>

            {{-- 2. Sertakan form ganti password --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- 3. Sertakan form hapus akun --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>