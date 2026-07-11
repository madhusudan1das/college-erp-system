<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart College Assistant</title>
    <meta name="description" content="Smart College Assistant - Secure login portal for students, faculty, and administrators.">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Premium Stylesheet -->
    <link href="{{ asset('css/premium.css') }}" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* ===== MAIN LOGIN CONTAINER ===== */
        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(ellipse at 20% 50%, #1e1b4b 0%, #0f172a 60%, #020617 100%);
            position: relative;
            overflow: hidden;
            padding: 20px;
        }

        /* Background glow orbs */
        .bg-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
        }

        .bg-orb-1 {
            width: 500px; height: 500px;
            background: rgba(99, 102, 241, 0.12);
            top: -10%; left: -5%;
            animation: breathe 6s ease infinite;
        }

        .bg-orb-2 {
            width: 400px; height: 400px;
            background: rgba(139, 92, 246, 0.08);
            bottom: -10%; right: -5%;
            animation: breathe 8s ease infinite 2s;
        }

        .bg-orb-3 {
            width: 300px; height: 300px;
            background: rgba(6, 182, 212, 0.06);
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            animation: breathe 10s ease infinite 4s;
        }

        /* ===== SPLIT LAYOUT ===== */
        .login-wrapper {
            display: flex;
            width: 100%;
            max-width: 1100px;
            min-height: 580px;
            position: relative;
            z-index: 2;
            gap: 0;
        }

        /* ===== LEFT PANEL - LOGIN FORM ===== */
        .login-form-panel {
            flex: 0 0 42%;
            max-width: 42%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
            animation: fadeInLeft 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255,255,255,0.1);
            padding: 40px 35px;
        }

        .login-card .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-card .logo-icon {
            width: 60px; height: 60px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: #fff;
            margin-bottom: 15px;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
            animation: scaleIn 0.5s ease 0.3s both;
        }

        .login-card h3 {
            color: #fff;
            font-weight: 800;
            font-size: 1.5rem;
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }

        .login-card .subtitle {
            color: #94a3b8;
            font-size: 0.9rem;
            font-weight: 400;
        }

        .login-card label {
            color: #cbd5e1;
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 6px;
            letter-spacing: 0.3px;
        }

        .login-card .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #fff;
            padding: 12px 16px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .login-card .form-control:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: #818cf8;
            box-shadow: 0 0 0 4px rgba(129, 140, 248, 0.15);
            transform: translateY(-1px);
        }

        .login-card .form-control::placeholder {
            color: #4b5563;
        }

        .login-card .btn-login {
            background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
            border: none !important;
            border-radius: 12px !important;
            padding: 13px 20px;
            font-weight: 700;
            font-size: 1rem;
            color: #fff !important;
            letter-spacing: 0.3px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.35) !important;
            position: relative;
            overflow: hidden;
        }

        .login-card .btn-login:hover {
            transform: translateY(-3px) scale(1.02) !important;
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.5) !important;
            background: linear-gradient(135deg, #4f46e5, #7c3aed) !important;
        }

        .login-card .btn-login:active {
            transform: translateY(-1px) scale(0.98) !important;
        }

        .login-card .input-group .btn {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-left: none !important;
            color: #94a3b8 !important;
            box-shadow: none !important;
        }

        .login-card .input-group .btn:hover {
            background: rgba(255, 255, 255, 0.1) !important;
            color: #e2e8f0 !important;
            transform: none !important;
        }

        .forgot-link {
            color: #a5b4fc !important;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s;
        }

        .forgot-link:hover {
            color: #c7d2fe !important;
            text-decoration: underline;
        }

        .alert {
            border-radius: 12px;
            font-size: 0.9rem;
            border: none;
            animation: fadeInDown 0.4s ease forwards;
        }

        /* ===== RIGHT PANEL - ANIMATION ===== */
        .login-animation-panel {
            flex: 0 0 58%;
            max-width: 58%;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Ground */
        .ground {
            position: absolute;
            bottom: 12%;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(99,102,241,0.4), rgba(99,102,241,0.4), transparent);
        }

        .ground::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 10%;
            right: 10%;
            height: 40px;
            background: linear-gradient(180deg, rgba(99,102,241,0.05), transparent);
        }

        /* Welcome text in right panel */
        .welcome-text {
            position: absolute;
            top: 18%;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            z-index: 1;
            opacity: 0;
            animation: fadeInDown 0.8s ease 3s forwards;
        }

        .welcome-text h2 {
            color: rgba(255,255,255,0.8);
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }

        .welcome-text p {
            color: rgba(148, 163, 184, 0.7);
            font-size: 0.95rem;
        }

        /* Decoration elements */
        .deco-circle {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(99,102,241,0.15);
            pointer-events: none;
        }

        .deco-circle-1 {
            width: 200px; height: 200px;
            top: 20%; right: 10%;
            animation: breathe 6s ease infinite;
        }

        .deco-circle-2 {
            width: 120px; height: 120px;
            bottom: 25%; left: 15%;
            animation: breathe 5s ease infinite 1s;
        }

        .deco-circle-3 {
            width: 80px; height: 80px;
            top: 40%; left: 30%;
            animation: breathe 7s ease infinite 2s;
        }

        /* Building silhouette */
        .college-building {
            position: absolute;
            bottom: calc(12% + 2px);
            right: 8%;
            opacity: 0;
            animation: fadeInUp 1s ease 2.5s forwards;
        }

        .building-main {
            width: 120px;
            height: 90px;
            background: linear-gradient(180deg, rgba(99,102,241,0.15), rgba(99,102,241,0.08));
            border-radius: 4px 4px 0 0;
            position: relative;
            border: 1px solid rgba(99,102,241,0.1);
            border-bottom: none;
        }

        .building-main::before {
            content: '';
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 30px solid transparent;
            border-right: 30px solid transparent;
            border-bottom: 20px solid rgba(99,102,241,0.15);
        }

        /* Building windows */
        .building-windows {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
            padding: 15px 10px;
        }

        .b-window {
            width: 18px;
            height: 14px;
            background: rgba(251, 191, 36, 0.25);
            border-radius: 2px;
            animation: breathe 3s ease infinite;
        }

        .b-window:nth-child(2) { animation-delay: 0.5s; }
        .b-window:nth-child(3) { animation-delay: 1s; }
        .b-window:nth-child(5) { animation-delay: 1.5s; }
        .b-window:nth-child(7) { animation-delay: 0.8s; }

        .building-door {
            width: 20px;
            height: 25px;
            background: rgba(99,102,241,0.2);
            border-radius: 10px 10px 0 0;
            margin: 0 auto;
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
        }

        /* Flag on building */
        .building-flag {
            position: absolute;
            top: -35px;
            left: 50%;
            transform: translateX(-50%);
        }

        .flag-pole {
            width: 2px;
            height: 20px;
            background: rgba(255,255,255,0.3);
            margin: 0 auto;
        }

        .flag-cloth {
            width: 15px;
            height: 10px;
            background: linear-gradient(135deg, #ef4444, #f97316);
            border-radius: 0 2px 2px 0;
            position: absolute;
            top: 0;
            left: 2px;
            animation: hairBlow 1.5s ease infinite;
            transform-origin: left center;
        }

        /* Tree */
        .tree {
            position: absolute;
            bottom: calc(12% + 2px);
            opacity: 0;
            animation: fadeInUp 0.8s ease 2.8s forwards;
        }

        .tree-1 { left: 10%; }
        .tree-2 { left: 35%; }

        .tree-trunk {
            width: 6px;
            height: 30px;
            background: rgba(120, 80, 40, 0.4);
            margin: 0 auto;
            border-radius: 2px;
        }

        .tree-leaves {
            width: 30px;
            height: 35px;
            background: rgba(16, 185, 129, 0.2);
            border-radius: 50% 50% 50% 50%;
            margin: 0 auto -5px auto;
            position: relative;
        }

        .tree-2 .tree-leaves {
            width: 25px;
            height: 28px;
            background: rgba(16, 185, 129, 0.15);
        }

        .tree-2 .tree-trunk {
            height: 22px;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 991px) {
            .login-wrapper {
                flex-direction: column;
                max-width: 460px;
            }

            .login-form-panel {
                flex: 0 0 100%;
                max-width: 100%;
                padding: 20px;
            }

            .login-animation-panel {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .login-card {
                padding: 30px 24px;
            }
        }

        /* Form group animations */
        .form-group-animated {
            opacity: 0;
            animation: fadeInUp 0.5s ease forwards;
        }

        .form-group-animated:nth-child(1) { animation-delay: 0.5s; }
        .form-group-animated:nth-child(2) { animation-delay: 0.65s; }
        .form-group-animated:nth-child(3) { animation-delay: 0.8s; }
        .form-group-animated:nth-child(4) { animation-delay: 0.95s; }
    </style>
</head>
<body>

<div class="login-page">
    <!-- Background Orbs -->
    <div class="bg-orb bg-orb-1"></div>
    <div class="bg-orb bg-orb-2"></div>
    <div class="bg-orb bg-orb-3"></div>

    <!-- Floating Particles -->
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>

    <!-- Main Wrapper -->
    <div class="login-wrapper">

        <!-- LEFT: Login Form -->
        <div class="login-form-panel">
            <div class="login-card">
                <div class="logo-section">
                    <div class="logo-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>Smart College Assistant</h3>
                    <p class="subtitle">Sign in to your account</p>
                </div>

                @if (session('error'))
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3">
                        {{ session('error') }}
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-3">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                    @csrf
                    <div class="mb-3 form-group-animated">
                        <label class="form-label">Username or Email</label>
                        <input type="text" name="username" class="form-control" required placeholder="Enter your username" value="{{ old('username') }}" id="loginUsername">
                    </div>
                    <div class="mb-3 form-group-animated">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="loginPassword" class="form-control" required placeholder="Enter your password" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                            <button class="btn" type="button" id="toggleLoginPassword" style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-radius: 0 12px 12px 0;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mb-4 form-group-animated">
                        <a href="{{ route('password.forgot') }}" class="forgot-link">Forgot Password?</a>
                    </div>
                    <div class="form-group-animated">
                        <button type="submit" class="btn btn-login w-100" id="loginButton">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- RIGHT: Animation Panel -->
        <div class="login-animation-panel">
            <!-- Decorative circles -->
            <div class="deco-circle deco-circle-1"></div>
            <div class="deco-circle deco-circle-2"></div>
            <div class="deco-circle deco-circle-3"></div>

            <!-- Welcome text (appears after boy arrives) -->
            <div class="welcome-text">
                <h2>Welcome Back! 👋</h2>
                <p>Ready to explore your campus world?</p>
            </div>

            <!-- Trees -->
            <div class="tree tree-1">
                <div class="tree-leaves"></div>
                <div class="tree-trunk"></div>
            </div>
            <div class="tree tree-2">
                <div class="tree-leaves"></div>
                <div class="tree-trunk"></div>
            </div>

            <!-- College Building silhouette -->
            <div class="college-building">
                <div class="building-flag">
                    <div class="flag-cloth"></div>
                    <div class="flag-pole"></div>
                </div>
                <div class="building-main">
                    <div class="building-windows">
                        <div class="b-window"></div>
                        <div class="b-window"></div>
                        <div class="b-window"></div>
                        <div class="b-window"></div>
                        <div class="b-window"></div>
                        <div class="b-window"></div>
                        <div class="b-window"></div>
                        <div class="b-window"></div>
                    </div>
                    <div class="building-door"></div>
                </div>
            </div>

            <!-- Ground line -->
            <div class="ground"></div>

            <!-- THE RUNNING BOY -->
            <div class="runner-container" id="runnerBoy">
                <div class="runner-body">
                    <!-- Hair -->
                    <div class="runner-hair"></div>
                    <!-- Head -->
                    <div class="runner-head">
                        <div class="runner-eyes"></div>
                        <div class="runner-smile"></div>
                    </div>
                    <!-- Torso (shirt) -->
                    <div class="runner-torso"></div>
                    <!-- Backpack -->
                    <div class="runner-backpack"></div>
                    <!-- Arms -->
                    <div class="runner-arm-left"></div>
                    <div class="runner-arm-right"></div>
                    <!-- Legs -->
                    <div class="runner-leg-left"></div>
                    <div class="runner-leg-right"></div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password toggle
        const toggleBtn = document.getElementById('toggleLoginPassword');
        const passwordField = document.getElementById('loginPassword');

        toggleBtn.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });

        // Stop running animation after the boy "arrives" (3.5s matches the runAcross duration)
        const runner = document.getElementById('runnerBoy');
        setTimeout(function() {
            runner.classList.add('arrived');
        }, 3500);

        // Create dynamic dust puffs during run
        function createDust() {
            const runnerRect = runner.getBoundingClientRect();
            const panel = document.querySelector('.login-animation-panel');
            if (!panel || runner.classList.contains('arrived')) return;

            const dust = document.createElement('div');
            dust.className = 'dust-puff';
            dust.style.position = 'absolute';
            dust.style.bottom = '12%';
            dust.style.right = (window.innerWidth - runnerRect.right + 30) + 'px';
            panel.appendChild(dust);

            setTimeout(() => dust.remove(), 600);
        }

        const dustInterval = setInterval(function() {
            if (runner.classList.contains('arrived')) {
                clearInterval(dustInterval);
                return;
            }
            createDust();
        }, 200);

        // Add login button ripple effect
        const loginBtn = document.getElementById('loginButton');
        loginBtn.addEventListener('click', function(e) {
            const rect = this.getBoundingClientRect();
            const ripple = document.createElement('span');
            ripple.style.cssText = `
                position: absolute;
                width: 20px; height: 20px;
                background: rgba(255,255,255,0.4);
                border-radius: 50%;
                top: ${e.clientY - rect.top - 10}px;
                left: ${e.clientX - rect.left - 10}px;
                animation: ripple 0.6s ease forwards;
                pointer-events: none;
            `;
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
    });
</script>

</body>
</html>
