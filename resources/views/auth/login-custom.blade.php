<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - FiskalCode</title>

    <!-- Load CSS -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body style="
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f0f9ff 0%, #f5f3ff 100%);
    margin: 0;
    font-family: 'Inter', sans-serif;
">

    <!-- From Uiverse.io by santhosh_2608 -->
    <div class="login-wrapper">
        <div class="login-card">
            <input type="checkbox" id="reg-toggle" class="reg-toggle" style="display: none;" />
            <div class="glow-blob blob-1"></div>
            <div class="glow-blob blob-2"></div>
            <div class="dark-overlay"></div>

            <div class="view-container">
                <!-- LOGIN VIEW -->
                <div class="form-view" id="login-view">
                    <div class="header">
                        <div class="decorative-dot"></div>
                        <div class="title">Welcome Back</div>
                        <p class="subtitle">Please enter your details to sign in.</p>
                    </div>

                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5; padding: 12px; border-radius: 8px; margin-bottom: 16px; font-size: 13px;">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="input-group">
                            <input type="email" name="email" class="input-field" placeholder="Email address" value="{{ old('email') }}" required autocomplete="email" autofocus />
                        </div>

                        <div class="input-group">
                            <input type="password" name="password" class="input-field" placeholder="Password" required autocomplete="current-password" />
                        </div>

                        <a href="#" class="forgot-link">Forgot password?</a>

                        <button type="submit" class="btn-submit">Sign In</button>
                    </form>

                    <div class="signup-prompt">
                        Don't have an account?
                        <label for="reg-toggle" class="toggle-link">Sign up</label>
                    </div>
                </div>

                <!-- SIGNUP VIEW -->
                <div class="form-view" id="signup-view">
                    <div class="header">
                        <div class="decorative-dot"></div>
                        <div class="title">Create Account</div>
                        <p class="subtitle">Join us to get started.</p>
                    </div>

                    @if ($errors->any())
                        <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5; padding: 12px; border-radius: 8px; margin-bottom: 16px; font-size: 13px;">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="input-group">
                            <input type="text" name="name" class="input-field" placeholder="Full Name" value="{{ old('name') }}" required autocomplete="name" />
                        </div>

                        <div class="input-group">
                            <input type="email" name="email" class="input-field" placeholder="Email address" value="{{ old('email') }}" required autocomplete="email" />
                        </div>

                        <div class="input-group">
                            <input type="password" name="password" class="input-field" placeholder="Password" required autocomplete="new-password" />
                        </div>

                        <div class="input-group">
                            <input type="password" name="password_confirmation" class="input-field" placeholder="Confirm Password" required autocomplete="new-password" />
                        </div>

                        <button type="submit" class="btn-submit">Sign Up</button>
                    </form>

                    <div class="signup-prompt">
                        Already have an account?
                        <label for="reg-toggle" class="toggle-link">Sign in</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
