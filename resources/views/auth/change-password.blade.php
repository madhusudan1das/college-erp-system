@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
<div class="row justify-content-center fade-in-up">
    <div class="col-md-6">
        <div class="premium-card shadow">
            <div class="card-header bg-light py-3 border-bottom border-light">
                <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-key me-2"></i>Change Password</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('password.change.update') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted">Current Password</label>
                        <div class="input-group">
                            <input type="password" name="current_password" id="curPassword" class="form-control" required placeholder="Enter current password" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                            <button class="btn btn-outline-secondary text-secondary shadow-none" type="button" id="toggleCurPassword" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted">New Password</label>
                        <div class="input-group">
                            <input type="password" name="new_password" id="newPassword" class="form-control" required placeholder="Enter new password (min. 6 characters)" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                            <button class="btn btn-outline-secondary text-secondary shadow-none" type="button" id="toggleNewPassword" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" name="new_password_confirmation" id="newPasswordConfirm" class="form-control" required placeholder="Confirm new password" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                            <button class="btn btn-outline-secondary text-secondary shadow-none" type="button" id="toggleNewPasswordConfirm" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mt-4 text-end">
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
