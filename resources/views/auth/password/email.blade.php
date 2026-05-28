<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Reset password akun Anda">
    <title>Reset Password — {{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (Filament Style) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'media', // Uses system preference for dark mode
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                            950: '#451a03',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-950 text-gray-950 dark:text-white font-sans antialiased flex flex-col justify-center py-12 sm:px-6 lg:px-8">


    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Logo -->
        <div class="flex justify-center">
            <div class="h-12 w-12 rounded-lg bg-primary-600 flex items-center justify-center shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="white" class="w-7 h-7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
            </div>
        </div>
        
        <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-950 dark:text-white">
            Reset Password
        </h2>
    </div>

<div class="mt-8 sm:mx-auto sm:w-full sm:max-w-[28rem]">
    <div class="bg-white dark:bg-gray-900 px-6 py-8 shadow-sm sm:rounded-xl sm:px-12 ring-1 ring-gray-950/5 dark:ring-white/10">
        <p style="color: #6b7280; margin-bottom: 1.5rem; text-align: center;">Masukkan alamat email anda</p>

        @if (session('status'))
            <div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 text-sm dark:bg-green-950/50 dark:text-green-400">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->has('email'))
            <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm dark:bg-red-950/50 dark:text-red-400">
                {{ $errors->first('email') }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email" class="block text-sm font-semibold leading-6 text-gray-950 dark:text-white mb-2">Alamat Email</label>
                <input type="email" id="email" name="email" class="block w-full rounded-lg border-0 py-2.5 text-gray-900 dark:text-white dark:bg-white/5 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-white/10 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 pl-3 pr-10 transition duration-75"
                 value="{{ old('email') }}" required autofocus placeholder="masukkan email anda">
            </div>

            <button type="submit" class="mt-5 flex w-full justify-center rounded-lg bg-primary-600 px-3 py-2.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-primary-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition duration-75">Kirim Link Reset</button>
        </form>
        
        <div style="margin-top: 1.5rem; text-align: center;">
            <a href="{{ route('login') }}" class="inline-flex items-center text-sm font-medium text-orange-600 hover:text-orange-500 hover:underline transition duration-75 ">
                <span class="mr-1.5 " aria-hidden="true">&larr;</span> Kembali ke Login
            </a>
        </div>
    </div>
</div>

</body>
</html>