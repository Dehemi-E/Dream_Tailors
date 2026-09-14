<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0f172a">
    <title>Dream Tailors - Sign In</title>
    
    <!-- Fonts and Icons matching Landing Page -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-dark: #0f172a; 
            --accent-copper: #4f46e5; 
            --text-light: #ffffff;
            --font-serif: 'Playfair Display', serif;
            --font-sans: 'Montserrat', sans-serif;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { 
            font-family: var(--font-sans); 
            color: var(--text-light); 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(15, 23, 42, 0.65), rgba(15, 23, 42, 0.85)), url('https://images.unsplash.com/photo-1593032465175-481ac7f401a0?auto=format&fit=crop&q=80&w=1920');
            background-size: cover; 
            background-position: center top;
            background-attachment: fixed;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 40px 30px;
            margin: 20px;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            text-align: center;
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ඔරිජිනල් JPEG එක ලස්සනට රවුම් කරලා බෝඩර් එකක් දැම්මා */
        .logo {
            width: 95px;
            height: 95px;
            margin: 0 auto 20px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(244, 235, 208, 0.4);
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }

        h1 {
            font-family: var(--font-serif);
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: 1px;
            color: #f4ebd0;
        }

        p.subtitle {
            font-size: 13px;
            font-weight: 300;
            color: rgba(244, 235, 208, 0.7);
            margin-bottom: 35px;
        }

        /* Error Messages */
        .error-box {
            background: rgba(225, 29, 72, 0.15);
            border: 1px solid rgba(225, 29, 72, 0.3);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            text-align: left;
        }
        .error-box h4 {
            color: #f43f5e;
            font-size: 13px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .error-box ul {
            list-style: none;
            color: #fda4af;
            font-size: 12px;
            margin-left: 22px;
        }
        .error-box ul li {
            margin-bottom: 3px;
        }

        /* Form Inputs */
        .form-group {
            margin-bottom: 22px;
            text-align: left;
            position: relative;
        }

        .form-group label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, 0.8);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .input-icon {
            position: absolute;
            bottom: 14px;
            left: 15px;
            color: rgba(255, 255, 255, 0.4);
            font-size: 16px;
            transition: color 0.3s;
        }

        .form-control {
            width: 100%;
            padding: 14px 15px 14px 45px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            color: #ffffff;
            font-family: var(--font-sans);
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-copper);
            background: rgba(255, 255, 255, 0.1);
        }
        .form-control:focus + .input-icon {
            color: var(--accent-copper);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .toggle-password {
            position: absolute;
            bottom: 14px;
            right: 15px;
            color: rgba(255, 255, 255, 0.4);
            cursor: pointer;
            font-size: 16px;
            transition: color 0.3s;
            background: transparent;
            border: none;
            outline: none;
        }
        .toggle-password:hover {
            color: #ffffff;
        }

        /* Remember Me & Forgot Password */
        .options-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
        }
        .remember-label input {
            accent-color: var(--accent-copper);
            width: 14px;
            height: 14px;
            cursor: pointer;
        }

        .forgot-link {
            font-size: 12px;
            color: #a5b4fc;
            text-decoration: none;
            transition: color 0.3s;
        }
        .forgot-link:hover {
            color: #ffffff;
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 15px;
            background-color: var(--accent-copper);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-family: var(--font-sans);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        }

        .btn-submit:hover {
            background-color: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(79, 70, 229, 0.4);
        }

        /* Back to Home Link */
        .back-home {
            position: absolute;
            top: -45px;
            left: 0;
            color: rgba(255, 255, 255, 0.6);
            font-size: 13px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: color 0.3s;
        }
        .back-home:hover {
            color: #ffffff;
        }

        /* Loading Overlay */
        #loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(5px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            flex-direction: column;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 3px solid rgba(255,255,255,0.1);
            border-radius: 50%;
            border-top-color: var(--accent-copper);
            animation: spin 1s ease-in-out infinite;
            margin-bottom: 20px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .footer-text {
            margin-top: 30px;
            font-size: 11px;
            color: rgba(255,255,255,0.4);
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <a href="{{ url('/') }}" class="back-home"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>

        <!-- ආයෙත් ඔරිජිනල් logo.jpeg එකම දැම්මා -->
        <img src="{{ asset('images/logo.jpeg') }}" alt="Dream Tailors" class="logo" onerror="this.src='https://ui-avatars.com/api/?name=DT&background=4f46e5&color=fff'">
        
        <h1>Dream Tailors</h1>
        <p class="subtitle">Premium Bespoke Tailoring Management</p>

        @if ($errors->any())
            <div class="error-box">
                <h4><i class="fa-solid fa-circle-exclamation"></i> Authentication Error</h4>
                <ul>
                    @foreach ($errors->all() as $error) 
                        <li>&bull; {{ $error }}</li> 
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" autocomplete="off" onsubmit="document.getElementById('loading-overlay').style.display = 'flex';">
            @csrf
            
            <input type="text" style="display:none" autocomplete="false">
            <input type="password" style="display:none" autocomplete="false">

            <div class="form-group">
                <label>Email or Mobile Number</label>
                <input type="text" name="login" class="form-control" placeholder="you@example.com or 07XXXXXXXX" required
                       autocomplete="new-password" 
                       readonly onfocus="this.removeAttribute('readonly');">
                <i class="fa-regular fa-user input-icon"></i>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" id="password-field" class="form-control" placeholder="Enter your password" required
                       autocomplete="new-password"
                       readonly onfocus="this.removeAttribute('readonly');">
                <i class="fa-solid fa-lock input-icon"></i>
                
                <button type="button" class="toggle-password" onclick="togglePasswordVisibility()">
                    <i id="eye-icon" class="fa-regular fa-eye-slash"></i>
                </button>
            </div>

            <div class="options-flex">
                <label class="remember-label">
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="#" class="forgot-link">Forgot password?</a>
            </div>

            <button type="submit" class="btn-submit">
                Sign In <i class="fa-solid fa-arrow-right-to-bracket"></i>
            </button>
        </form>
        
        <div class="footer-text">
            &copy; {{ date('Y') }} Dream Tailors. Crafted with precision.
        </div>
    </div>

    <!-- Minimalist Loading Overlay -->
    <div id="loading-overlay">
        <div class="spinner"></div>
        <div style="font-size: 12px; color: white; letter-spacing: 3px; font-weight: 600;">AUTHENTICATING</div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password-field');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>
</html>