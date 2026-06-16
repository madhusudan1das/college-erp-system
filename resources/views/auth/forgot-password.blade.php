<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - College ERP</title>
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
                    <h3 class="fw-bold text-white"><i class="fas fa-shield-alt me-2 text-indigo"></i>Forgot Password</h3>
                    <p class="mb-0 text-muted">Verify your details to reset password</p>
                </div>

                @if (session('error'))
                    <div class="alert alert-danger border-0 shadow-sm rounded-3">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('password.forgot.verify') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-white">I am a...</label>
                        <select name="role" id="roleSelect" class="form-select text-white bg-transparent border-secondary" required style="background-color: rgba(255, 255, 255, 0.05) !important;">
                            <option value="student" {{ old('role') == 'student' ? 'selected' : '' }} style="background-color: #0f172a; color: #fff;">Student</option>
                            <option value="faculty" {{ old('role') == 'faculty' ? 'selected' : '' }} style="background-color: #0f172a; color: #fff;">Faculty</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-white">Username or Email</label>
                        <input type="text" name="username_or_email" class="form-control" placeholder="Enter username or email" required value="{{ old('username_or_email') }}">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-white" id="verLabel">Enrollment Number</label>
                        <input type="text" name="verification_info" id="verInput" class="form-control" placeholder="Enter enrollment number" required value="{{ old('verification_info') }}">
                        <div class="form-text small mt-1" id="verHelp" style="color: #a5b4fc;">Students: verify using your registration number.</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3"><i class="fas fa-fingerprint me-2"></i>Verify Details</button>
                    
                    <div class="text-center mt-4">
                        <a href="{{ route('login') }}" class="text-decoration-none small" style="color: #a5b4fc;"><i class="fas fa-arrow-left me-1"></i>Back to Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('roleSelect');
        const verLabel = document.getElementById('verLabel');
        const verInput = document.getElementById('verInput');
        const verHelp = document.getElementById('verHelp');

        function updateLabels() {
            if (roleSelect.value === 'faculty') {
                verLabel.textContent = 'Registered Phone Number';
                verInput.placeholder = 'Enter registered phone number';
                verHelp.textContent = 'Faculty: verify using your phone number.';
            } else {
                verLabel.textContent = 'Enrollment Number';
                verInput.placeholder = 'Enter enrollment number';
                verHelp.textContent = 'Students: verify using your registration number.';
            }
        }

        roleSelect.addEventListener('change', updateLabels);
        updateLabels(); // run on load
    });
</script>

</body>
</html>
