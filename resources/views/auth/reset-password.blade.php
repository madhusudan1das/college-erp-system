<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - College ERP</title>
    <meta name="description" content="Create a new secure password for your College ERP account.">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Premium Stylesheet -->
    <link href="{{ asset('css/premium.css') }}" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; margin: 0; overflow-x: hidden; }

        .auth-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(ellipse at 20% 50%, #1e1b4b 0%, #0f172a 60%, #020617 100%);
            position: relative;
            overflow: hidden;
            padding: 40px 20px;
        }

        .bg-orb { position: absolute; border-radius: 50%; filter: blur(80px); pointer-events: none; }
        .bg-orb-1 { width: 500px; height: 500px; background: rgba(16,185,129,0.1); top: -10%; left: -5%; animation: breathe 6s ease infinite; }
        .bg-orb-2 { width: 400px; height: 400px; background: rgba(6,182,212,0.08); bottom: -10%; right: -5%; animation: breathe 8s ease infinite 2s; }

        .auth-card {
            width: 100%; max-width: 460px;
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255,255,255,0.1);
            padding: 40px 35px;
            position: relative; z-index: 2;
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .auth-card .icon-box {
            width: 60px; height: 60px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 16px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.5rem; color: #fff; margin-bottom: 15px;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
            animation: scaleIn 0.5s ease 0.3s both;
        }

        .auth-card h3 { color: #fff; font-weight: 800; font-size: 1.4rem; margin-bottom: 6px; }
        .auth-card .subtitle { color: #94a3b8; font-size: 0.9rem; }
        .auth-card label { color: #cbd5e1; font-weight: 600; font-size: 0.85rem; }

        .auth-card .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px; color: #fff; padding: 12px 16px;
            transition: all 0.3s ease;
        }

        .auth-card .form-control:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: #34d399;
            box-shadow: 0 0 0 4px rgba(52, 211, 153, 0.15);
            color: #fff;
        }

        .auth-card .form-control::placeholder { color: #4b5563; }

        .auth-card .btn-submit {
            background: linear-gradient(135deg, #10b981, #059669) !important;
            border: none !important; border-radius: 12px !important;
            padding: 13px 20px; font-weight: 700; color: #fff !important;
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35) !important;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .auth-card .btn-submit:hover {
            transform: translateY(-3px) scale(1.02) !important;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.5) !important;
        }

        .auth-card .input-group .btn {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-left: none !important; color: #94a3b8 !important;
            box-shadow: none !important;
        }

        .auth-card .input-group .btn:hover {
            background: rgba(255, 255, 255, 0.1) !important;
            color: #e2e8f0 !important; transform: none !important;
        }

        .alert { border-radius: 12px; border: none; animation: fadeInDown 0.4s ease forwards; }

        .form-group-animated { opacity: 0; animation: fadeInUp 0.5s ease forwards; }
        .form-group-animated:nth-child(1) { animation-delay: 0.3s; }
        .form-group-animated:nth-child(2) { animation-delay: 0.45s; }
        .form-group-animated:nth-child(3) { animation-delay: 0.6s; }
    </style>
</head>
<body>

<div class="auth-page">
    <div class="bg-orb bg-orb-1"></div>
    <div class="bg-orb bg-orb-2"></div>

    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>

    <div class="auth-card">
        <div class="text-center mb-4">
            <div class="icon-box">
                <i class="fas fa-key"></i>
            </div>
            <h3>Reset Password</h3>
            <p class="subtitle">Create a new secure password</p>
        </div>

        @if (session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('password.reset.post') }}" method="POST">
            @csrf

            <div class="mb-3 form-group-animated">
                <label class="form-label fw-semibold">New Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="resetPassword" class="form-control" placeholder="Enter new password (min. 6 chars)" required style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                    <button class="btn" type="button" id="toggleResetPassword" style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-radius: 0 12px 12px 0;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="mb-4 form-group-animated">
                <label class="form-label fw-semibold">Confirm New Password</label>
                <div class="input-group">
                    <input type="password" name="password_confirmation" id="resetPasswordConfirm" class="form-control" placeholder="Confirm new password" required style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                    <button class="btn" type="button" id="toggleResetPasswordConfirm" style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-radius: 0 12px 12px 0;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group-animated">
                <button type="submit" class="btn btn-submit w-100 py-3 fw-bold"><i class="fas fa-check-circle me-2"></i>Reset Password</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const setupToggle = (btnId, inputId) => {
            const btn = document.getElementById(btnId);
            const input = document.getElementById(inputId);
            btn.addEventListener('click', function() {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        };

        setupToggle('toggleResetPassword', 'resetPassword');
        setupToggle('toggleResetPasswordConfirm', 'resetPasswordConfirm');
    });
</script>

</body>
</html>
