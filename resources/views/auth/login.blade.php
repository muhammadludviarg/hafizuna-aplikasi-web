<x-guest-layout>
    <div class="flex h-screen bg-white">

        <div
            class="w-1/2 bg-gradient-to-b from-green-400 to-green-600 flex flex-col justify-center items-center p-12 text-white">

            <div class="bg-white bg-opacity-30 p-4 rounded-lg">
                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253">
                    </path>
                </svg>
            </div>

            <h1 class="text-4xl font-bold mt-4">HAFIZUNA</h1>
            <p class="text-lg mt-2">Sistem Manajemen Hafalan Al-Qur'an</p>

            <div class="bg-white bg-opacity-30 p-4 rounded-lg mt-8 text-center">
                <p class="text-xl font-semibold">SD ISLAM AL-AZHAR 27</p>
                <p class="text-md">Cibinong, Bogor</p>
            </div>
        </div>

        <div class="w-1/2 flex justify-center items-center p-12">
            <div class="w-full max-w-md">
                <h2 class="text-3xl font-bold mb-2">Selamat Datang</h2>
                <p class="text-gray-600 mb-8">Silakan masuk dengan akun Anda</p>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input id="email"
                            class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                            type="email" name="email" :value="old('email')" required autofocus />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                        <input id="password"
                            class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                            type="password" name="password" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="mt-8">
                        <button type="submit"
                            class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg shadow-lg transition duration-300">
                            Masuk
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-guest-layout>