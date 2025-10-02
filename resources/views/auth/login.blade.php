<x-guest-layout>
    <div class="">
        <div class="">
            <!-- Heading -->
            <h3 class="text-center mb-4 fw-bold text-primary">Welcome Back</h3>
            <p class="text-center text-muted mb-4">Login to continue to your account</p>

            <!-- Session Status -->
            @if (session('status'))
                <div class="alert alert-success mb-4">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Email address') }}</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password + Forgot -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <label for="password" class="form-label">{{ __('Password') }}</label>
                        @if (Route::has('password.request'))
                            <a class="small text-decoration-none" href="{{ route('password.request') }}">
                                {{ __('Forgot password?') }}
                            </a>
                        @endif
                    </div>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        name="password" required autocomplete="current-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                    <label class="form-check-label" for="remember_me">
                        {{ __('Remember me') }}
                    </label>
                </div>

                <!-- Login Button -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        {{ __('Log in') }}
                    </button>
                </div>
            </form>

            <!-- Register -->
            <div class="text-center mt-4">
                <span class="text-muted">Don’t have an account?</span>
                <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">
                    {{ __('Register') }}
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>