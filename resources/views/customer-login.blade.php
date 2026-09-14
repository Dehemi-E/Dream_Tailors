<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in · Dream Tailors</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class', // Enables dark mode toggling via class
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* ----- Light Mode (Default) Styles ----- */
        .glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.06);
        }
        .input-modern {
            background: rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(0, 0, 0, 0.06);
            transition: all 0.2s ease;
            font-size: 0.95rem;
            color: #0f172a;
        }
        .input-modern:focus {
            outline: none;
            border-color: #0f172a;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.04);
        }
        .input-modern::placeholder {
            color: #94a3b8;
            font-weight: 300;
            font-size: 0.9rem;
        }
        .btn-primary {
            background: #0f172a;
            color: #ffffff;
            font-weight: 600;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }
        .btn-primary:hover {
            background: #1e293b;
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(15, 23, 42, 0.12);
        }
        .btn-primary:active {
            transform: translateY(0);
        }
        .error-banner {
            background: rgba(220, 38, 38, 0.04);
            border: 1px solid rgba(220, 38, 38, 0.12);
            color: #b91c1c;
            font-size: 0.875rem;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            text-align: center;
            font-weight: 450;
        }
        .divider-subtle {
            background: rgba(0, 0, 0, 0.04);
            height: 1px;
            width: 100%;
        }

        /* ----- Dark Mode Styles ----- */
        .dark .glass-card {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .dark .input-modern {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #f8fafc;
        }
        .dark .input-modern:focus {
            border-color: #818cf8;
            background: rgba(15, 23, 42, 0.9);
            box-shadow: 0 0 0 4px rgba(129, 140, 248, 0.15);
        }
        .dark .input-modern::placeholder {
            color: #64748b;
        }
        .dark .btn-primary {
            background: #ffffff;
            color: #0f172a;
        }
        .dark .btn-primary:hover {
            background: #f1f5f9;
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.15);
        }
        .dark .error-banner {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #fca5a5;
        }
        .dark .divider-subtle {
            background: rgba(255, 255, 255, 0.1);
        }

        ::selection {
            background: #818cf8;
            color: #ffffff;
        }
    </style>

    <!-- Theme Initialization Script -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="min-h-screen flex items-center justify-center p-6 bg-slate-50 dark:bg-[#0f172a] dark:bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] dark:from-slate-800 dark:via-slate-900 dark:to-black">

    <!-- 🌙 Theme Toggle Button -->
    <button id="themeToggle" class="fixed top-6 right-6 p-2.5 rounded-full bg-white/80 dark:bg-slate-800/80 backdrop-blur-md border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 shadow-sm hover:scale-105 transition-all z-50 focus:outline-none">
        <!-- Sun Icon -->
        <svg id="themeToggleLight" class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <!-- Moon Icon -->
        <svg id="themeToggleDark" class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
    </button>

    <div class="w-full max-w-sm">

        <!-- Card -->
        <div class="glass-card rounded-3xl p-8 md:p-10 transition-all duration-300">

            <!-- Brand & Logo -->
            <div class="text-center mb-9">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full overflow-hidden border border-slate-200 dark:border-white/10 bg-white shadow-lg flex items-center justify-center">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Dream Tailors Logo" class="w-full h-full object-cover">
                </div>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-white transition-colors">
                    Dream Tailors
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-light tracking-wide mt-1 transition-colors">
                    Customer sign in
                </p>
                <div class="divider-subtle mt-5"></div>
            </div>

            <!-- Error -->
            @if ($errors->any())
                <div class="error-banner mb-7 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('customer.login') }}" method="POST" autocomplete="off" class="space-y-5">
                @csrf

                <!-- Mobile -->
                <div>
                    <label class="block text-[11px] font-medium uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2 transition-colors">
                        Mobile number
                    </label>
                    <input
                        type="text"
                        name="mobile"
                        required
                        autocomplete="off"
                        placeholder="07XXXXXXXX"
                        class="w-full input-modern rounded-xl px-4 py-3.5 focus:ring-0"
                        autofocus
                    >
                </div>

                <!-- Password with Show/Hide -->
                <div>
                    <label class="block text-[11px] font-medium uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2 transition-colors">
                        Password
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••"
                            class="w-full input-modern rounded-xl px-4 py-3.5 pr-12 focus:ring-0"
                        >
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 px-4 flex items-center text-slate-400 hover:text-slate-700 dark:hover:text-white transition-colors focus:outline-none">
                            <!-- Eye Icon -->
                            <svg id="eyeOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <!-- Eye Slash Icon -->
                            <svg id="eyeClosed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <div class="pt-3">
                    <button type="submit" class="btn-primary w-full rounded-xl py-3.5 text-sm tracking-wide">
                        Sign in
                    </button>
                </div>

            </form>

        </div>

        <!-- Footer -->
        <p class="text-center text-[11px] font-mono text-slate-400 dark:text-slate-500 tracking-widest uppercase mt-7 transition-colors">
            · tailored experience ·
        </p>

    </div>

    <!-- Scripts -->
    <script>
        // --- Dark/Light Mode Toggle Logic ---
        const themeToggleBtn = document.getElementById('themeToggle');
        const htmlElement = document.documentElement;

        themeToggleBtn.addEventListener('click', function() {
            htmlElement.classList.toggle('dark');
            
            if (htmlElement.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
            } else {
                localStorage.setItem('theme', 'light');
            }
        });

        // --- Show/Hide Password Logic ---
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        });
    </script>
</body>
</html>