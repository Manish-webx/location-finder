<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GeoTrack - Instant Location Sharing & Real-Time Tracking')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --accent-gradient: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
            --success-gradient: linear-gradient(135deg, #059669 0%, #10b981 100%);
            --dark-bg: #0f172a;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .navbar-custom {
            background: rgba(15, 23, 42, 0.96);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.25s ease;
        }

        .brand-logo {
            background: linear-gradient(135deg, #818cf8 0%, #c084fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
            font-size: 1.45rem;
            letter-spacing: -0.5px;
        }

        .nav-link-custom {
            color: #cbd5e1 !important;
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.2s ease;
            padding: 0.5rem 0.85rem !important;
        }

        .nav-link-custom:hover,
        .nav-link-custom.active {
            color: #ffffff !important;
        }

        .btn-gradient {
            background: var(--primary-gradient);
            color: #ffffff !important;
            border: none;
            font-weight: 600;
            border-radius: 12px;
            padding: 0.65rem 1.4rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.45);
            color: #ffffff !important;
        }

        .btn-gradient-sm {
            padding: 0.45rem 1rem;
            font-size: 0.875rem;
            border-radius: 10px;
        }

        .card-custom {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04);
            transition: all 0.25s ease;
        }

        .card-custom:hover {
            box-shadow: 0 12px 30px -4px rgba(0, 0, 0, 0.08);
        }

        .stat-card {
            border-radius: 18px;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            position: relative;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
        }

        .badge-live {
            background-color: #10b981;
            color: white;
            animation: pulse-badge 2s infinite;
        }

        @keyframes pulse-badge {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6); }
            70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .footer-custom {
            background: #0f172a;
            color: #94a3b8;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .footer-custom .text-muted,
        .footer-custom .footer-muted {
            color: #94a3b8 !important;
        }

        .footer-custom a {
            color: #cbd5e1;
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-custom a:hover {
            color: #ffffff;
        }

        .footer-custom .form-control {
            color: #f8fafc !important;
        }

        .footer-custom .form-control::placeholder {
            color: #94a3b8 !important;
            opacity: 1;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home.index') }}">
                <div class="rounded-3 p-1 d-flex align-items-center justify-content-center" style="background: var(--primary-gradient); width: 36px; height: 36px;">
                    <i class="bi bi-geo-alt-fill text-white fs-5"></i>
                </div>
                <span class="brand-logo">GeoTrack</span>
                <span class="badge bg-primary bg-opacity-20 text-white border border-primary border-opacity-30 rounded-pill px-2.5 py-1 small" style="font-size: 0.72rem;">Live Tracker</span>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto align-items-center gap-1 my-2 my-lg-0">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('home.index') ? 'active fw-bold' : '' }}" href="{{ route('home.index') }}">
                            <i class="bi bi-house-door me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="{{ route('home.index') }}#how-it-works">
                            <i class="bi bi-diagram-3 me-1"></i> How It Works
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="{{ route('home.index') }}#features">
                            <i class="bi bi-stars me-1"></i> Features
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('home.track') ? 'active fw-bold' : '' }}" href="{{ route('home.track') }}">
                            <i class="bi bi-crosshair me-1"></i> Track Location
                        </a>
                    </li>
                    @if(session('admin_logged_in'))
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('dashboard.*') ? 'active fw-bold' : '' }}" href="{{ route('dashboard.index') }}">
                            <i class="bi bi-shield-lock-fill text-primary me-1"></i> Admin Dashboard
                        </a>
                    </li>
                    @endif
                </ul>

                <div class="d-flex align-items-center gap-2">
                    @if(session('admin_logged_in'))
                    <form action="{{ route('admin.logout') }}" method="POST" class="d-inline m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1.5 d-flex align-items-center gap-1" title="Sign out of Admin Portal">
                            <i class="bi bi-box-arrow-right"></i>
                            <span class="d-none d-sm-inline">Logout</span>
                        </button>
                    </form>
                    @endif
                    <button type="button" class="btn btn-gradient btn-gradient-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#createLinkGlobalModal">
                        <i class="bi bi-plus-lg fs-6"></i>
                        <span>Create Link</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main View Content -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Global Create Link Modal -->
    <div class="modal fade" id="createLinkGlobalModal" tabindex="-1" aria-labelledby="createLinkGlobalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form action="{{ route('home.createLink') }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 pb-0 pt-4 px-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary" style="width: 40px; height: 40px;">
                                <i class="bi bi-link-45deg fs-4"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-dark mb-0" id="createLinkGlobalModalLabel">Create Tracking Link</h5>
                                <div class="text-muted small">Generate a live session link to share with your recipient</div>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body py-4 px-4">

                        <div class="mb-3">
                            <label for="global_link_expires" class="form-label fw-semibold small text-dark">Link Expiration</label>
                            <select name="expires_in_hours" id="global_link_expires" class="form-select rounded-3 py-2.5">
                                <option value="">Never (Valid until manually deleted)</option>
                                <option value="1">1 Hour</option>
                                <option value="6">6 Hours</option>
                                <option value="24" selected>24 Hours (Recommended)</option>
                                <option value="168">7 Days</option>
                            </select>
                        </div>
                        <div class="alert alert-light border small text-muted rounded-3 mb-0 p-3">
                            <i class="bi bi-shield-check text-success me-1"></i> Your recipient opens the link, completes the check-in, and their live coordinates instantly appear on your tracking map.
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient px-4">Generate Link</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer-custom py-5 mt-auto" data-bs-theme="dark">
        <div class="container">
            <div class="row g-4 justify-content-between">
                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="rounded-3 p-1 d-flex align-items-center justify-content-center" style="background: var(--primary-gradient); width: 32px; height: 32px;">
                            <i class="bi bi-geo-alt-fill text-white fs-6"></i>
                        </div>
                        <span class="brand-logo fs-5">GeoTrack</span>
                    </div>
                    <p class="small mb-3 footer-muted" style="color: #94a3b8; line-height: 1.6;">
                        High-accuracy real-time location sharing and tracking link generator. Share temporary links, receive live coordinate pings, and visualize check-ins on interactive maps.
                    </p>
                    <div class="small footer-muted" style="color: #94a3b8;">
                        &copy; {{ date('Y') }} GeoTrack. All rights reserved.
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <h6 class="text-white fw-bold mb-3 small text-uppercase">Navigation</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                        <li><a href="{{ route('home.index') }}">Home</a></li>
                        <li><a href="{{ route('home.track') }}">Track Location</a></li>
                        <li><a href="{{ route('home.index') }}#how-it-works">How It Works</a></li>
                        <li><a href="{{ route('home.index') }}#features">Features</a></li>
                        @if(session('admin_logged_in'))
                            <li><a href="{{ route('dashboard.index') }}">Admin Dashboard</a></li>
                        @endif
                    </ul>
                </div>
                <div class="col-6 col-md-2">
                    <h6 class="text-white fw-bold mb-3 small text-uppercase">Features</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                        <li><a href="{{ route('home.index') }}#track-search">Link Finder</a></li>
                        <li><a href="{{ route('home.track') }}">Live Leaflet Map</a></li>
                        <li><a href="{{ route('home.index') }}#how-it-works">No-App Required</a></li>
                        <li><a href="{{ route('home.index') }}#features">Auto-Expiring Links</a></li>
                    </ul>
                </div>
                <div class="col-12 col-md-3">
                    <h6 class="text-white fw-bold mb-3 small text-uppercase">Direct Lookup</h6>
                    <form action="{{ route('home.lookup') }}" method="POST" class="mb-3">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="query" class="form-control form-control-sm bg-dark border-secondary text-white" placeholder="Paste link or token...">
                            <button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                    <div class="footer-muted" style="font-size: 0.75rem; color: #94a3b8;">
                        <i class="bi bi-shield-lock-fill text-success me-1"></i> End-to-end HTTPS encrypted transmission.
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @stack('scripts')
</body>
</html>
