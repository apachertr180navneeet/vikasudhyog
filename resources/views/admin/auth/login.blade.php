<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Vikas Udhyog Herbal Products ERP</title>
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/responsive.css') }}">

    <style>
        :root {
            --brand-primary: #5B841E;
            --brand-primary-hover: #486A16;
            --brand-secondary: #8FBF26;
            --brand-dark: #0F2813;
            --brand-emerald: #1B4D25;
            --brand-accent: #D4A017;
            --bg-dark-mesh: #09190D;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark-mesh);
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(143, 191, 38, 0.18) 0%, transparent 40%),
                radial-gradient(circle at 90% 85%, rgba(91, 132, 30, 0.22) 0%, transparent 45%),
                radial-gradient(circle at 50% 50%, rgba(15, 40, 19, 0.95) 0%, #08150A 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient glowing background elements */
        .ambient-glow-1 {
            position: absolute;
            width: 450px;
            height: 450px;
            top: -100px;
            left: -100px;
            background: radial-gradient(circle, rgba(143, 191, 38, 0.15) 0%, transparent 70%);
            filter: blur(60px);
            pointer-events: none;
            z-index: 0;
        }

        .ambient-glow-2 {
            position: absolute;
            width: 550px;
            height: 550px;
            bottom: -150px;
            right: -150px;
            background: radial-gradient(circle, rgba(91, 132, 30, 0.2) 0%, transparent 70%);
            filter: blur(70px);
            pointer-events: none;
            z-index: 0;
        }

        /* Login Container Card */
        .login-card {
            position: relative;
            z-index: 1;
            display: flex;
            width: 100%;
            max-width: 1120px;
            min-height: 640px;
            background: #FFFFFF;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 
                0 30px 70px -15px rgba(0, 0, 0, 0.55),
                0 0 0 1px rgba(255, 255, 255, 0.15),
                0 10px 30px rgba(0, 0, 0, 0.3);
            animation: fadeInCard 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeInCard {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Left Hero Banner */
        .login-hero {
            flex: 1.15;
            background: 
                linear-gradient(145deg, rgba(10, 28, 14, 0.92) 0%, rgba(20, 52, 26, 0.85) 50%, rgba(45, 90, 22, 0.78) 100%),
                url('{{ asset('admin/images/herbal_bg.jpg') }}') center/cover no-repeat;
            padding: 3.5rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: #FFFFFF;
            position: relative;
        }

        .login-hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top left, rgba(255, 255, 255, 0.12), transparent 60%);
            pointer-events: none;
        }

        .hero-top {
            position: relative;
            z-index: 2;
        }

        .brand-badge-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .brand-logo-wrap {
            width: 58px;
            height: 58px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.98);
            padding: 6px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .brand-logo-wrap img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.65rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #FFFFFF;
            line-height: 1.15;
        }

        .brand-tagline {
            font-size: 0.82rem;
            color: #A3E635;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            margin-top: 0.2rem;
        }

        /* Center Content */
        .hero-main {
            position: relative;
            z-index: 2;
            margin: 2rem 0;
        }

        .hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(163, 230, 53, 0.16);
            border: 1px solid rgba(163, 230, 53, 0.35);
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.8px;
            color: #BEF264;
            text-transform: uppercase;
            margin-bottom: 1.25rem;
            backdrop-filter: blur(8px);
        }

        .hero-headline {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 2.15rem;
            font-weight: 800;
            line-height: 1.25;
            color: #FFFFFF;
            margin-bottom: 1rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .hero-headline span {
            color: #BEF264;
        }

        .hero-description {
            color: rgba(255, 255, 255, 0.88);
            font-size: 0.96rem;
            line-height: 1.6;
            margin-bottom: 1.75rem;
        }

        /* Feature Cards */
        .feature-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .feature-card {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.7rem 0.9rem;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px);
            transition: all 0.25s ease;
        }

        .feature-card:hover {
            background: rgba(255, 255, 255, 0.14);
            transform: translateX(4px);
            border-color: rgba(190, 242, 100, 0.3);
        }

        .feature-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: rgba(163, 230, 53, 0.2);
            color: #BEF264;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .feature-text {
            font-size: 0.88rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.95);
        }

        /* Hero Footer */
        .hero-bottom {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            padding-top: 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
        }

        .trust-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            padding: 0.45rem 0.9rem;
            border-radius: 30px;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.4px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #FFFFFF;
        }

        .trust-badge i {
            color: #FBBF24;
        }

        /* Right Form Section */
        .login-form-panel {
            flex: 1;
            padding: 3.5rem 3.25rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: #FFFFFF;
        }

        .form-header-wrap {
            margin-bottom: 1.75rem;
        }

        .portal-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--brand-primary);
            background: #F1F8E9;
            padding: 0.3rem 0.75rem;
            border-radius: 20px;
            margin-bottom: 0.75rem;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            background: #10B981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
            animation: pulseDot 2s infinite;
        }

        @keyframes pulseDot {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .form-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.95rem;
            font-weight: 800;
            color: #111827;
            letter-spacing: -0.5px;
            margin-bottom: 0.4rem;
        }

        .form-subtitle {
            color: #6B7280;
            font-size: 0.92rem;
            line-height: 1.5;
        }

        /* Quick Demo Fill Banner */
        .demo-pill-banner {
            background: #F8FAF5;
            border: 1px dashed #A3E635;
            border-radius: 12px;
            padding: 0.85rem 1rem;
            margin-bottom: 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .demo-info {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.82rem;
            color: #374151;
        }

        .demo-info i {
            color: var(--brand-primary);
            font-size: 1rem;
        }

        .demo-info code {
            background: #E8F5E9;
            color: #1E3A1A;
            padding: 0.15rem 0.4rem;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.8rem;
        }

        .btn-fill-demo {
            background: var(--brand-primary);
            color: #FFFFFF;
            font-size: 0.76rem;
            font-weight: 700;
            padding: 0.4rem 0.75rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .btn-fill-demo:hover {
            background: var(--brand-primary-hover);
            transform: translateY(-1px);
        }

        /* Error alerts */
        .error-alert-box {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-left: 4px solid #EF4444;
            padding: 0.85rem 1rem;
            border-radius: 10px;
            font-size: 0.85rem;
            color: #991B1B;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        /* Inputs */
        .input-group-custom {
            margin-bottom: 1.35rem;
        }

        .input-label {
            display: block;
            font-size: 0.86rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.45rem;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon-left {
            position: absolute;
            left: 1.1rem;
            color: #9CA3AF;
            font-size: 1rem;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .input-field {
            width: 100%;
            height: 50px;
            padding: 0 2.8rem 0 3rem;
            background: #FAFAFA;
            border: 1.5px solid #E5E7EB;
            border-radius: 12px;
            font-size: 0.95rem;
            color: #111827;
            font-weight: 500;
            outline: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-field:focus {
            background: #FFFFFF;
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 4px rgba(91, 132, 30, 0.15);
        }

        .input-field:focus + .input-icon-left,
        .input-wrapper:focus-within .input-icon-left {
            color: var(--brand-primary);
        }

        .toggle-password-btn {
            position: absolute;
            right: 1rem;
            background: transparent;
            border: none;
            color: #9CA3AF;
            cursor: pointer;
            padding: 0.3rem;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
        }

        .toggle-password-btn:hover {
            color: #374151;
        }

        /* Form Actions Row */
        .form-options-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.85rem;
            font-size: 0.86rem;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #4B5563;
            cursor: pointer;
            user-select: none;
            font-weight: 500;
        }

        .remember-checkbox {
            width: 17px;
            height: 17px;
            accent-color: var(--brand-primary);
            border-radius: 4px;
            cursor: pointer;
        }

        .forgot-link {
            color: var(--brand-primary);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .forgot-link:hover {
            color: var(--brand-primary-hover);
            text-decoration: underline;
        }

        /* Submit Button */
        .btn-submit-login {
            width: 100%;
            height: 52px;
            background: linear-gradient(135deg, #5B841E 0%, #436614 100%);
            color: #FFFFFF;
            border: none;
            border-radius: 12px;
            font-size: 1.02rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.65rem;
            box-shadow: 0 10px 20px -5px rgba(91, 132, 30, 0.4);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-submit-login:hover {
            background: linear-gradient(135deg, #689622 0%, #4B7417 100%);
            transform: translateY(-2px);
            box-shadow: 0 14px 25px -5px rgba(91, 132, 30, 0.55);
        }

        .btn-submit-login:active {
            transform: translateY(0);
            box-shadow: 0 6px 15px -3px rgba(91, 132, 30, 0.4);
        }

        .btn-submit-login i {
            transition: transform 0.25s ease;
        }

        .btn-submit-login:hover i {
            transform: translateX(4px);
        }

        /* Footer */
        .form-footer {
            margin-top: 2rem;
            text-align: center;
            font-size: 0.8rem;
            color: #9CA3AF;
            line-height: 1.6;
        }

        .form-footer a {
            color: #6B7280;
            font-weight: 500;
        }

        /* Responsive Breakpoints */
        @media (max-width: 980px) {
            .login-card {
                flex-direction: column;
                max-width: 540px;
                min-height: auto;
            }
            .login-hero {
                padding: 2.5rem 2rem;
            }
            .login-form-panel {
                padding: 2.5rem 2rem;
            }
            .hero-headline {
                font-size: 1.75rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 0.75rem;
            }
            .login-hero, .login-form-panel {
                padding: 1.75rem 1.25rem;
            }
            .brand-title {
                font-size: 1.4rem;
            }
            .hero-headline {
                font-size: 1.45rem;
            }
            .demo-pill-banner {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>

    <!-- Ambient glowing lights -->
    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <div class="login-card">
        <!-- Left Side Hero Section -->
        <div class="login-hero">
            <div class="hero-top">
                <div class="brand-badge-container">
                    <div class="brand-logo-wrap">
                        <img src="{{ asset('admin/images/logo.png') }}" alt="Vikas Udhyog Logo">
                    </div>
                    <div>
                        <div class="brand-title">VIKAS UDHYOG</div>
                        <div class="brand-tagline">
                            <i class="fa-solid fa-seedling"></i> Herbal Products & ERP
                        </div>
                    </div>
                </div>
            </div>

            <div class="hero-main">
                <div class="hero-pill">
                    <i class="fa-solid fa-shield-halved"></i> ENTERPRISE SUITE v3.0
                </div>
                <h1 class="hero-headline">
                    Empowering <span>Herbal &amp; Ayurvedic</span> Manufacturing
                </h1>
                <p class="hero-description">
                    End-to-end specialized enterprise solution for Sojat Henna powder, herbal formulations, raw materials procurement, and global distribution.
                </p>

                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
                        <div class="feature-text">Batch-level Expiry, QC &amp; Formulation Tracking</div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                        <div class="feature-text">GST Compliant E-Invoicing &amp; Automated Tax Filing</div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fa-solid fa-chart-line"></i></div>
                        <div class="feature-text">Real-time Sales, Procurement &amp; Inventory Analytics</div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fa-brands fa-whatsapp"></i></div>
                        <div class="feature-text">WhatsApp Business API Automated Dispatch Alerts</div>
                    </div>
                </div>
            </div>

            <div class="hero-bottom">
                <div class="trust-badge">
                    <i class="fa-solid fa-award"></i> ISO 9001:2015 Certified
                </div>
                <div class="trust-badge">
                    <i class="fa-solid fa-lock"></i> 256-Bit Encrypted Session
                </div>
            </div>
        </div>

        <!-- Right Side Login Form -->
        <div class="login-form-panel">
            <div>
                <div class="form-header-wrap">
                    <div class="portal-status-pill">
                        <span class="status-dot"></span> System Live &amp; Ready
                    </div>
                    <h2 class="form-title">Welcome Back</h2>
                    <p class="form-subtitle">Enter your credentials to securely access your ERP workspace</p>
                </div>

                @if(session('error'))
                    <div class="error-alert-box">
                        <i class="fa-solid fa-circle-exclamation" style="font-size: 1.1rem; color: #DC2626;"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @if(isset($errors) && $errors->any())
                    <div class="error-alert-box">
                        <i class="fa-solid fa-circle-exclamation" style="font-size: 1.1rem; color: #DC2626;"></i>
                        <div>{{ $errors->first() }}</div>
                    </div>
                @endif

                <!-- Interactive Demo Access Quick Fill -->
                <div class="demo-pill-banner">
                    <div class="demo-info">
                        <i class="fa-solid fa-key"></i>
                        <div>
                            Demo: <code>admin</code> / <code>admin123</code>
                        </div>
                    </div>
                    <button type="button" class="btn-fill-demo" id="auto-fill-btn">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Auto Fill
                    </button>
                </div>

                <form id="login-form" action="{{ route('admin.login.post') }}" method="POST">
                    @csrf
                    
                    <div class="input-group-custom">
                        <label class="input-label" for="username">Username or Email</label>
                        <div class="input-wrapper">
                            <input type="text" name="username" id="username" class="input-field" placeholder="e.g. admin or admin@vikasudhyog.com" value="{{ old('username', 'admin') }}" required autofocus>
                            <i class="fa-regular fa-user input-icon-left"></i>
                        </div>
                    </div>

                    <div class="input-group-custom">
                        <label class="input-label" for="password">Password</label>
                        <div class="input-wrapper">
                            <input type="password" name="password" id="password" class="input-field" placeholder="Enter your password" value="admin123" required>
                            <i class="fa-solid fa-lock input-icon-left"></i>
                            <button type="button" class="toggle-password-btn" id="toggle-password" title="Toggle password visibility">
                                <i class="fa-regular fa-eye" id="toggle-icon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options-row">
                        <label class="remember-label">
                            <input type="checkbox" name="remember" class="remember-checkbox" checked>
                            <span>Remember this device</span>
                        </label>
                        <a href="javascript:void(0)" onclick="alert('Quick Reset: Please use the default credentials admin / admin123 to log in.')" class="forgot-link">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-submit-login" id="submit-btn">
                        <span>SIGN IN TO ERP</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>
            </div>

            <div class="form-footer">
                <div>&copy; 2026 <strong>VIKAS UDHYOG</strong>. All Rights Reserved.</div>
                <div>Sojat City, Pali District, Rajasthan &bull; ISO 9001:2015</div>
            </div>
        </div>
    </div>

    <script>
        // Password toggle visibility
        const togglePassword = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggle-icon');

        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                toggleIcon.classList.toggle('fa-eye');
                toggleIcon.classList.toggle('fa-eye-slash');
            });
        }

        // Demo autofill button
        const autoFillBtn = document.getElementById('auto-fill-btn');
        if (autoFillBtn) {
            autoFillBtn.addEventListener('click', function() {
                document.getElementById('username').value = 'admin';
                document.getElementById('password').value = 'admin123';
                
                autoFillBtn.innerHTML = '<i class="fa-solid fa-check"></i> Filled!';
                setTimeout(() => {
                    autoFillBtn.innerHTML = '<i class="fa-solid fa-wand-magic-sparkles"></i> Auto Fill';
                }, 1500);
            });
        }

        // Session storage helper
        document.getElementById('login-form').addEventListener('submit', function(e) {
            const u = document.getElementById('username').value.trim();
            sessionStorage.setItem('vu_logged_in', 'true');
            sessionStorage.setItem('vu_user_role', 'Super Administrator');
            sessionStorage.setItem('vu_user_name', u === 'admin' ? 'Administrator' : u);
        });
    </script>
</body>
</html>
