<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - College ERP</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            backdrop-filter: blur(10px);
        }
        .login-header {
            background: #fff;
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid #eaeaea;
        }
        .login-header h3 {
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        .login-body {
            padding: 40px 30px;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #cbd5e1;
            margin-bottom: 20px;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #667eea;
        }
        i{
            padding:10px;
            margin:auto;
        }
        .btn-primary {
            background: #667eea;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background: #5a6cd6;
            transform: translateY(-2px);
        }
        .alert {
            border-radius: 8px;
            font-size: 0.9rem;
        }
    </style>
    <!-- Premium Stylesheet -->
    <link href="{{ asset('css/premium.css') }}" rel="stylesheet">
</head>
<body class="futuristic-login-bg min-vh-100 d-flex align-items-center justify-content-center">

<div class="glass-login-card card p-4 p-md-5 w-100" style="max-width: 450px;">
    <div class="text-center mb-4">
        <h3 class="fw-bold text-white"><i class="fas fa-graduation-cap me-2 text-indigo"></i>College ERP</h3>
        <p class="mb-0 text-muted">Sign in to your account</p>
    </div>
    <div class="card-body p-0">
        @if (session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-3">
                {{ session('error') }}
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3">
                {{ session('success') }}
            </div>
        @endif
        
        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold text-white">Username or Email</label>
                <input type="text" name="username" class="form-control" required placeholder="Enter username" value="{{ old('username') }}">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold text-white">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="loginPassword" class="form-control" required placeholder="Enter password" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                    <button class="btn btn-outline-secondary text-white" type="button" id="toggleLoginPassword" style="border-top-left-radius: 0; border-bottom-left-radius: 0; background: rgba(255, 255, 255, 0.05); border-color: rgba(255, 255, 255, 0.1);">
                        <i class="fas fa-eye "></i>
                    </button>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-4">
              
                <a href="{{ route('password.forgot') }}" class="text-decoration-none small" style="color: #a5b4fc;">Forgot Password?</a>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3"><i class="fas fa-sign-in-alt me-2"></i>Login</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('toggleLoginPassword');
        const passwordField = document.getElementById('loginPassword');
        
        toggleBtn.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    });
</script>

</body>
</html>
