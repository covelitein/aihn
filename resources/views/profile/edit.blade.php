<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile Settings') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="d-flex flex-column gap-4">
                        <!-- Profile Information Section -->
                        <div class="card profile-section border-0 shadow-sm">
                            <div class="card-header bg-light border-0 py-4">
                                <div class="d-flex align-items-center">
                                    <div class="header-icon me-3">
                                        <i class="bi bi-person-circle fs-4"></i>
                                    </div>
                                    <div>
                                        <h3 class="section-title mb-1">{{ __('Profile Information') }}</h3>
                                        <p class="section-description mb-0 text-muted">
                                            {{ __("Update your account's profile information and email address.") }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                                    @csrf
                                </form>

                                <form method="post" action="{{ route('profile.update') }}">
                                    @csrf
                                    @method('patch')

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label fw-semibold">{{ __('Name') }}</label>
                                            <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                                            @error('name')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label fw-semibold">{{ __('Email') }}</label>
                                            <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="username">
                                            @error('email')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                        <div class="alert alert-warning mt-3">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-exclamation-triangle me-2"></i>
                                                <div>
                                                    <p class="mb-1">{{ __('Your email address is unverified.') }}</p>
                                                    <button form="send-verification" class="btn btn-link btn-sm p-0 text-warning-emphasis">
                                                        {{ __('Click here to re-send the verification email.') }}
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            @if (session('status') === 'verification-link-sent')
                                                <div class="alert alert-success mt-2 mb-0 py-2">
                                                    <i class="bi bi-check-circle me-2"></i>
                                                    {{ __('A new verification link has been sent to your email address.') }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="d-flex align-items-center gap-3 mt-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle me-2"></i>{{ __('Save') }}
                                        </button>

                                        @if (session('status') === 'profile-updated')
                                            <div class="alert alert-success mb-0 py-2" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)">
                                                <i class="bi bi-check-circle me-2"></i>{{ __('Saved.') }}
                                            </div>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Update Password Section -->
                        <div class="card profile-section border-0 shadow-sm">
                            <div class="card-header bg-light border-0 py-4">
                                <div class="d-flex align-items-center">
                                    <div class="header-icon me-3">
                                        <i class="bi bi-shield-lock fs-4"></i>
                                    </div>
                                    <div>
                                        <h3 class="section-title mb-1">{{ __('Update Password') }}</h3>
                                        <p class="section-description mb-0 text-muted">
                                            {{ __('Ensure your account is using a long, random password to stay secure.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <form method="post" action="{{ route('password.update') }}">
                                    @csrf
                                    @method('put')

                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label for="update_password_current_password" class="form-label fw-semibold">{{ __('Current Password') }}</label>
                                            <input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password">
                                            @error('current_password', 'updatePassword')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="update_password_password" class="form-label fw-semibold">{{ __('New Password') }}</label>
                                            <input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password">
                                            @error('password', 'updatePassword')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="update_password_password_confirmation" class="form-label fw-semibold">{{ __('Confirm Password') }}</label>
                                            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password">
                                            @error('password_confirmation', 'updatePassword')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center gap-3 mt-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle me-2"></i>{{ __('Save') }}
                                        </button>

                                        @if (session('status') === 'password-updated')
                                            <div class="alert alert-success mb-0 py-2" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)">
                                                <i class="bi bi-check-circle me-2"></i>{{ __('Saved.') }}
                                            </div>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Delete Account Section -->
                        <div class="card profile-section border-0 shadow-sm border-start border-4 border-danger">
                            <div class="card-header bg-light border-0 py-4">
                                <div class="d-flex align-items-center">
                                    <div class="header-icon me-3 bg-danger bg-opacity-10 text-danger">
                                        <i class="bi bi-exclamation-triangle fs-4"></i>
                                    </div>
                                    <div>
                                        <h3 class="section-title mb-1 text-danger">{{ __('Delete Account') }}</h3>
                                        <p class="section-description mb-0 text-muted">
                                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletion">
                                    <i class="bi bi-trash me-2"></i>{{ __('Delete Account') }}
                                </button>

                                <!-- Bootstrap Modal -->
                                <div class="modal fade" id="confirmUserDeletion" tabindex="-1" aria-labelledby="confirmUserDeletionLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fs-5 text-danger" id="confirmUserDeletionLabel">
                                                    {{ __('Are you sure you want to delete your account?') }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="text-muted mb-4">
                                                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                                                </p>
                                                
                                                <form method="post" action="{{ route('profile.destroy') }}" id="deleteAccountForm">
                                                    @csrf
                                                    @method('delete')
                                                    
                                                    <div class="mb-3">
                                                        <label for="password" class="form-label visually-hidden">{{ __('Password') }}</label>
                                                        <input id="password" name="password" type="password" class="form-control" placeholder="{{ __('Password') }}">
                                                        @error('password', 'userDeletion')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer border-0">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    {{ __('Cancel') }}
                                                </button>
                                                <button type="submit" form="deleteAccountForm" class="btn btn-danger">
                                                    {{ __('Delete Account') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .profile-section {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .profile-section:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .header-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4361ee;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #212529;
        }

        .section-description {
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #4361ee;
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.15);
        }

        .btn {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: #4361ee;
            border-color: #4361ee;
        }

        .btn-primary:hover {
            background: #3a56d4;
            border-color: #3a56d4;
            transform: translateY(-1px);
        }

        .btn-danger {
            background: #ef4444;
            border-color: #ef4444;
        }

        .btn-danger:hover {
            background: #dc2626;
            border-color: #dc2626;
            transform: translateY(-1px);
        }

        .card-header {
            background: #f8f9fa !important;
        }

        .alert {
            border-radius: 8px;
            border: none;
        }

        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
        }

        .modal-header {
            padding: 1.5rem 1.5rem 0.5rem 1.5rem;
        }

        .modal-body {
            padding: 0 1.5rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem 1.5rem 1.5rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-header .d-flex {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }
            
            .header-icon {
                margin: 0 auto;
            }
            
            .d-flex.align-items-center.gap-3 {
                flex-direction: column;
                align-items: stretch;
                gap: 1rem !important;
            }
            
            .d-flex.align-items-center.gap-3 .alert {
                text-align: center;
            }
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 1.5rem !important;
            }
            
            .card-header {
                padding: 1.5rem !important;
            }
        }
    </style>
</x-app-layout>