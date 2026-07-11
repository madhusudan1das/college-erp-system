@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
    <h2 class="h3 mb-0 fw-bold" style="letter-spacing: -0.5px;"><i class="fas fa-key me-2" style="color: #6366f1;"></i>Change Password</h2>
</div>

<div class="row justify-content-center fade-in-up delay-1">
    <div class="col-md-6">
        <div class="premium-card shadow">
            <div class="card-header py-3 d-flex align-items-center gap-2" style="border-bottom: 2px solid rgba(99,102,241,0.1);">
                <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #6366f1, #8b5cf6); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.85rem;">
                    <i class="fas fa-lock"></i>
                </div>
                <h5 class="m-0 fw-bold" style="color: #334155; font-size: 1rem;">Update Your Password</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('password.change.update') }}" method="POST">
                    @csrf
                    <div class="mb-4 fade-in-up" style="animation-delay: 0.2s;">
                        <label class="form-label fw-semibold text-muted small text-uppercase" style="letter-spacing: 0.5px;">Current Password</label>
                        <div class="input-group">
                            <input type="password" name="current_password" id="curPassword" class="form-control" required placeholder="Enter current password" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                            <button class="btn btn-outline-secondary text-secondary shadow-none" type="button" id="toggleCurPassword" style="border-top-left-radius: 0; border-bottom-left-radius: 0; background: #f8fafc !important; border-color: #cbd5e1 !important; color: #64748b !important;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-4 fade-in-up" style="animation-delay: 0.35s;">
                        <label class="form-label fw-semibold text-muted small text-uppercase" style="letter-spacing: 0.5px;">New Password</label>
                        <div class="input-group">
                            <input type="password" name="new_password" id="newPassword" class="form-control" required placeholder="Enter new password (min. 6 characters)" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                            <button class="btn btn-outline-secondary text-secondary shadow-none" type="button" id="toggleNewPassword" style="border-top-left-radius: 0; border-bottom-left-radius: 0; background: #f8fafc !important; border-color: #cbd5e1 !important; color: #64748b !important;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-4 fade-in-up" style="animation-delay: 0.5s;">
                        <label class="form-label fw-semibold text-muted small text-uppercase" style="letter-spacing: 0.5px;">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" name="new_password_confirmation" id="newPasswordConfirm" class="form-control" required placeholder="Confirm new password" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                            <button class="btn btn-outline-secondary text-secondary shadow-none" type="button" id="toggleNewPasswordConfirm" style="border-top-left-radius: 0; border-bottom-left-radius: 0; background: #f8fafc !important; border-color: #cbd5e1 !important; color: #64748b !important;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mt-4 text-end fade-in-up" style="animation-delay: 0.65s;">
                        <button type="submit" class="btn btn-primary px-4 py-2"><i class="fas fa-save me-2"></i>Update Password</button>
                    </div>
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

        setupToggle('toggleCurPassword', 'curPassword');
        setupToggle('toggleNewPassword', 'newPassword');
        setupToggle('toggleNewPasswordConfirm', 'newPasswordConfirm');
    });
</script>
@endsection
