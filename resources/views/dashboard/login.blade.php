@extends('layouts.app')

@section('title', 'Admin Portal Login - GeoTrack')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height: 65vh;">
        <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4">
            
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show rounded-4 mb-3 shadow-sm border-0 d-flex align-items-center" role="alert">
                    <i class="bi bi-shield-exclamation fs-5 me-2 flex-shrink-0"></i>
                    <div class="small fw-medium">{{ session('warning') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-3 shadow-sm border-0 d-flex align-items-center" role="alert">
                    <i class="bi bi-x-octagon-fill fs-5 me-2 flex-shrink-0"></i>
                    <div class="small fw-medium">{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 mb-3 shadow-sm border-0 d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 me-2 flex-shrink-0"></i>
                    <div class="small fw-medium">{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card-custom p-4 p-md-5 shadow-lg border-0">
                <div class="text-center mb-4">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary mx-auto mb-3" style="width: 64px; height: 64px; border-radius: 18px;">
                        <i class="bi bi-shield-lock-fill fs-2"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">Admin Portal</h3>
                    <p class="text-muted small mb-0">Authorized administration personnel only</p>
                </div>

                <form action="{{ route('admin.login.submit') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="username" class="form-label small fw-semibold text-secondary">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted rounded-start-3">
                                <i class="bi bi-person-fill"></i>
                            </span>
                            <input type="text" name="username" id="username" class="form-control bg-light border-start-0 rounded-end-3 py-2.5 @error('username') is-invalid @enderror" placeholder="Enter admin username" value="{{ old('username') }}" required autofocus>
                        </div>
                        @error('username')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label small fw-semibold text-secondary">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted rounded-start-3">
                                <i class="bi bi-key-fill"></i>
                            </span>
                            <input type="password" name="password" id="password" class="form-control bg-light border-start-0 rounded-end-3 py-2.5 @error('password') is-invalid @enderror" placeholder="Enter admin password" required>
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-gradient w-100 py-2.5 fw-semibold rounded-3 mb-3 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Authenticate & Enter</span>
                    </button>

                    <div class="text-center">
                        <a href="{{ route('home.index') }}" class="text-muted small text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i> Return to Homepage
                        </a>
                    </div>
                </form>
            </div>

            <div class="text-center mt-4 text-muted small">
                <i class="bi bi-lock-fill text-secondary me-1"></i> End-to-end protected session. Unauthorized access attempts are monitored and logged.
            </div>

        </div>
    </div>
</div>
@endsection
