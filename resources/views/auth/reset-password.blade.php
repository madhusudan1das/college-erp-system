<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - College ERP</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Premium Stylesheet -->
    <link href="{{ asset('css/premium.css') }}" rel="stylesheet">
</head>
<body class="futuristic-login-bg min-vh-100 d-flex align-items-center justify-content-center py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="glass-login-card card p-4 p-md-5">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-white"><i class="fas fa-key me-2 text-indigo"></i>Reset Password</h3>
                    <p class="mb-0 text-muted">Create a new secure password</p>
                </div>

                @if (session('error'))
                    <div class="alert alert-danger border-0 shadow-sm rounded-3">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('password.reset.post') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-white">New Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="resetPassword" class="form-control" placeholder="Enter new password (min. 6 chars)" required style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                            <button class="btn btn-outline-secondary text-white shadow-none" type="button" id="toggleResetPassword" style="border-top-left-radius: 0; border-bottom-left-radius: 0; background: rgba(255, 255, 255, 0.05); border-color: rgba(255, 255, 255, 0.1);">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-white">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="resetPasswordConfirm" class="form-control" placeholder="Confirm new password" required style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                            <button class="btn btn-outline-secondary text-white shadow-none" type="button" id="toggleResetPasswordConfirm" style="border-top-left-radius: 0; border-bottom-left-radius: 0; background: rgba(255, 255, 255, 0.05); border-color: rgba(255, 255, 255, 0.1);">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3"><i class="fas fa-check-circle me-2"></i>Reset Password</button>
                </form>
            </div>
        </div>
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
