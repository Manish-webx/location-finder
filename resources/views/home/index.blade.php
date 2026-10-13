@extends('layouts.app')

@section('title', 'GeoTrack - Instant 10-Digit Location Sharing & Tracking Links')

@section('content')
<style>
    .hero-section {
        background: radial-gradient(circle at 50% 10%, #1e1b4b 0%, #0f172a 100%);
        color: #ffffff;
        padding: 5rem 0 6rem;
        position: relative;
        overflow: hidden;
    }

    .hero-glow {
        position: absolute;
        top: -150px;
        left: 50%;
        transform: translateX(-50%);
        width: 650px;
        height: 650px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.28) 0%, rgba(15, 23, 42, 0) 70%);
        pointer-events: none;
    }

    .search-card-hero {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 24px;
        padding: 2rem;
        box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.5);
    }

    .search-input-group {
        background: #ffffff;
        border-radius: 16px;
        padding: 0.4rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .search-input-field {
        border: none;
        outline: none;
        font-size: 1.05rem;
        padding: 0.75rem 1rem;
        width: 100%;
        color: #1e293b;
        font-weight: 500;
    }

    .search-input-field::placeholder {
        color: #94a3b8;
        font-weight: 400;
    }

    .step-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 2.25rem 2rem;
        position: relative;
        transition: all 0.25s ease;
        height: 100%;
    }

    .step-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }

    .step-number {
        position: absolute;
        top: -16px;
        left: 2rem;
        background: var(--primary-gradient);
        color: #ffffff;
        font-weight: 800;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        box-shadow: 0 6px 14px rgba(79, 70, 229, 0.4);
    }

    .feature-icon-box {
        width: 58px;
        height: 58px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 1.25rem;
    }

    .generator-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.05);
    }

    .link-result-box {
        background: #f0fdf4;
        border: 2px dashed #86efac;
        border-radius: 20px;
        padding: 1.75rem;
        animation: fadeInBox 0.3s ease-out;
    }

    @keyframes fadeInBox {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<!-- Hero Section -->
<section class="hero-section text-center position-relative">
    <div class="hero-glow"></div>
    <div class="container position-relative" style="z-index: 2;">

        <!-- Notifications -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 max-w-xl mx-auto mb-4 border-0 shadow-lg text-start" role="alert" style="max-width: 680px;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                    <div>
                        <strong>Success!</strong> {{ session('success') }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 max-w-xl mx-auto mb-4 border-0 shadow-lg text-start" role="alert" style="max-width: 680px;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                    <div>
                        <strong>Notice:</strong> {{ session('error') }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Generated Link Highlight Banner (Session or AJAX) -->
        <div id="ajaxLinkResultWrapper" class="mx-auto mb-5 text-start" style="{{ session('generated_link') ? '' : 'display: none;' }} max-width: 760px;">
            @php $newLink = session('generated_link'); @endphp
            <div class="link-result-box shadow-lg text-dark bg-white border border-success border-2">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                    <span class="badge bg-success text-white px-3 py-1.5 rounded-pill fw-bold">
                        <i class="bi bi-check2-circle me-1"></i> Your 10-Digit Link is Ready!
                    </span>
                    <span class="text-muted small">Expires: <strong id="resExpiresText">{{ $newLink && $newLink->expires_at ? $newLink->expires_at->diffForHumans() : '24 Hours' }}</strong></span>
                </div>

                <div class="text-secondary small mb-3">Copy your link below to share it with your recipient. Track its live location anytime using this link.</div>

                <div class="input-group mb-3">
                    <input type="text" class="form-control font-monospace bg-light fs-6 fw-bold text-primary py-2.5" id="activeShareUrlInput" value="{{ $newLink ? url('/l/' . $newLink->token) : '' }}" readonly>
                    <button class="btn btn-success px-4 fw-bold d-flex align-items-center gap-1.5" id="activeCopyBtn" type="button">
                        <i class="bi bi-clipboard"></i> <span>Copy Link</span>
                    </button>
                    <a href="{{ $newLink ? route('home.track', ['token' => $newLink->token]) : '#' }}" id="activeTrackBtn" class="btn btn-dark px-3.5 fw-semibold d-flex align-items-center gap-1.5">
                        <i class="bi bi-crosshair"></i> <span>Track Location</span>
                    </a>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="{{ $newLink ? 'https://api.whatsapp.com/send?text=' . urlencode('Please open this link to verify: ' . url('/l/' . $newLink->token)) : '#' }}" id="activeWhatsAppBtn" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3">
                        <i class="bi bi-whatsapp me-1"></i> Share on WhatsApp
                    </a>
                    <a href="{{ $newLink ? 'sms:?body=' . urlencode('Please open this link: ' . url('/l/' . $newLink->token)) : '#' }}" id="activeSmsBtn" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i class="bi bi-chat-dots me-1"></i> Send via SMS
                    </a>
                    <button type="button" class="btn btn-sm btn-link text-muted ms-auto text-decoration-none" onclick="document.getElementById('ajaxLinkResultWrapper').style.display='none'">
                        Dismiss
                    </button>
                </div>
            </div>
        </div>

        <!-- Hero Title Badge -->
        <div class="d-inline-flex align-items-center gap-2 px-3.5 py-1.5 rounded-pill mb-3" style="background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(129, 140, 248, 0.3);">
            <i class="bi bi-link-45deg text-info fs-5"></i>
            <span class="small fw-semibold text-light">Alphanumeric Link Tracking System</span>
        </div>

        <!-- Main Headline -->
        <h1 class="display-4 fw-extrabold text-white mb-3" style="letter-spacing: -1px; font-weight: 800;">
            Paste Tracking Link & <br class="d-none d-md-block">
            <span style="background: linear-gradient(135deg, #a5b4fc 0%, #38bdf8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                Find Real-Time Location
            </span>
        </h1>

        <p class="lead text-light text-opacity-75 mb-5 mx-auto" style="max-width: 680px; font-size: 1.15rem; line-height: 1.6;">
            Every session uses a unique alphanumeric link. Track location strictly by the link itself with no names needed. Once opened, live GPS coordinates appear instantly on your interactive map.
        </p>

        <!-- PROMINENT SEARCH BAR: Paste Created Link to Find Location -->
        <div class="search-card-hero mx-auto text-start mb-5" style="max-width: 760px;" id="track-search">
            <label class="form-label text-white small fw-bold text-uppercase mb-2 d-flex align-items-center justify-content-between">
                <span><i class="bi bi-search me-1 text-info"></i> Paste Created Link to Find Location</span>
                <span class="text-light text-opacity-75 small fw-normal">e.g. {{ url('/l/7K9X2M4B1Q') }}</span>
            </label>

            <form action="{{ route('home.lookup') }}" method="POST">
                @csrf
                <div class="search-input-group">
                    <span class="ps-3 text-muted fs-5"><i class="bi bi-link-45deg"></i></span>
                    <input type="text" name="query" id="heroSearchInput" class="search-input-field" placeholder="Paste link or code here (e.g. {{ url('/l/7K9X2M4B1Q') }})..." value="{{ old('query') }}" required autofocus>
                    
                    <button type="button" id="pasteClipboardBtn" class="btn btn-light btn-sm text-secondary px-3 py-2 rounded-3 d-none d-sm-inline-flex align-items-center gap-1">
                        <i class="bi bi-clipboard"></i> Paste
                    </button>

                    <button type="submit" class="btn btn-gradient px-4 py-2.5 text-nowrap d-flex align-items-center gap-2">
                        <i class="bi bi-crosshair fs-5"></i>
                        <span>Find Location</span>
                    </button>
                </div>
            </form>

            <div class="d-flex flex-wrap align-items-center justify-content-between mt-3 text-light text-opacity-75 small">
                <span><i class="bi bi-shield-check text-success me-1"></i> Tracking strictly identified by unique alphanumeric link</span>
                <span><i class="bi bi-lightning-charge-fill text-warning me-1"></i> Live coordinates auto-poll every 5s</span>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row g-3 justify-content-center mx-auto" style="max-width: 820px;">
            <div class="col-6 col-md-4">
                <div class="p-3 rounded-4" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);">
                    <div class="fs-3 fw-bold text-white">{{ $totalLinks }}</div>
                    <div class="small text-light text-opacity-75">Tracking Links Created</div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="p-3 rounded-4" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);">
                    <div class="fs-3 fw-bold text-success">{{ $totalCheckins }}</div>
                    <div class="small text-light text-opacity-75">Live Location Check-ins</div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="p-3 rounded-4" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);">
                    <div class="fs-3 fw-bold text-info">{{ $recentActiveCount }}</div>
                    <div class="small text-light text-opacity-75">Active Sessions Ready</div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- STEP BY STEP SECTION: How to create and track links -->
<section class="py-6 py-md-7 bg-white border-bottom" id="how-it-works" style="padding: 5rem 0;">
    <div class="container">
        <div class="text-center mb-5 max-w-xl mx-auto" style="max-width: 680px;">
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-1.5 rounded-pill fw-bold mb-2">
                Simple 3-Step Process
            </span>
            <h2 class="fw-bold text-dark mb-3">How 10-Digit Link Tracking Works</h2>
            <p class="text-muted">
                Track locations simply by link with no names required. Follow these three quick steps:
            </p>
        </div>

        <div class="row g-4 mb-5">
            <!-- Step 1 -->
            <div class="col-12 col-md-4">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <div class="feature-icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-link-45deg"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Create Alphanumeric Link</h5>
                    <p class="text-muted small mb-3">
                        Click the generate button below. The system automatically creates a unique alphanumeric link (e.g. <code>{{ url('/l/7K9X2M4B1Q') }}</code>).
                    </p>
                    <div class="bg-light p-2.5 rounded-3 border small text-secondary">
                        <i class="bi bi-check2 text-success me-1"></i> Generated instantly &mdash; no names needed
                    </div>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="col-12 col-md-4">
                <div class="step-card">
                    <div class="step-number">2</div>
                    <div class="feature-icon-box bg-success bg-opacity-10 text-success">
                        <i class="bi bi-send-fill"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Copy & Share Link</h5>
                    <p class="text-muted small mb-3">
                        Click the <strong>Copy Link</strong> button and share via WhatsApp, SMS, or Messenger. Your recipient simply taps the link in their browser.
                    </p>
                    <div class="bg-light p-2.5 rounded-3 border small text-secondary">
                        <i class="bi bi-check2 text-success me-1"></i> 100% web-based &mdash; zero app install
                    </div>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="col-12 col-md-4">
                <div class="step-card">
                    <div class="step-number">3</div>
                    <div class="feature-icon-box bg-info bg-opacity-10 text-info">
                        <i class="bi bi-crosshair"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Track Location by Link</h5>
                    <p class="text-muted small mb-3">
                        Paste the link in the search bar anytime to view their real-time coordinates, accuracy radius, and check-in pin on an interactive map.
                    </p>
                    <div class="bg-light p-2.5 rounded-3 border small text-secondary">
                        <i class="bi bi-check2 text-success me-1"></i> Tracked solely by link ID
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action Banner -->
        <div class="p-4 p-md-5 rounded-4 text-white text-center text-md-start d-flex flex-wrap align-items-center justify-content-between gap-4" style="background: var(--primary-gradient);">
            <div>
                <h4 class="fw-bold mb-1">Generate your alphanumeric tracking link now</h4>
                <p class="mb-0 text-white text-opacity-80">Takes 1 click. Copy and share immediately.</p>
            </div>
            <a href="#create-link-section" class="btn btn-light px-4 py-2.5 fw-bold text-primary rounded-3 shadow">
                <i class="bi bi-plus-circle me-1"></i> Create Link Below
            </a>
        </div>
    </div>
</section>

<!-- INSTANT LINK CREATOR SECTION: Alphanumeric generator, no name required -->
<section class="py-6 py-md-7" id="create-link-section" style="padding: 5rem 0; background: #f8fafc;">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-12 col-lg-5">
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-1.5 rounded-pill fw-bold mb-3">
                    1-Click Alphanumeric Link Creator
                </span>
                <h2 class="fw-bold text-dark mb-3">Instant Random Link Generator</h2>
                <p class="text-muted mb-4" style="line-height: 1.6;">
                    No personal names or complex registration. Choose an expiration duration, click generate, copy your link, and track directly.
                </p>

                <div class="d-flex flex-column gap-3 mb-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="stat-icon bg-white shadow-sm text-primary flex-shrink-0" style="width: 44px; height: 44px;">
                            <i class="bi bi-shield-check fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Alphanumeric Security</h6>
                            <p class="text-muted small mb-0">Every link is assigned a 10-character alphanumeric code like <code>7K9X2M4B1Q</code> that uniquely identifies the session.</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3">
                        <div class="stat-icon bg-white shadow-sm text-success flex-shrink-0" style="width: 44px; height: 44px;">
                            <i class="bi bi-clipboard-check-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Instant Copy & Share</h6>
                            <p class="text-muted small mb-0">The created link appears right here so you can copy and send it in one click.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="col-12 col-lg-7">
                <div class="generator-card" id="instantGeneratorCard">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h4 class="fw-bold text-dark mb-0">Generate Tracking Link</h4>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 small fw-semibold">
                            <i class="bi bi-lightning-charge-fill me-1"></i> Instant 10-Digit
                        </span>
                    </div>

                    <!-- Ajax generated link container -->
                    <div id="inlineResultBox" class="link-result-box mb-4" style="display: none;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-success text-white px-2.5 py-1 rounded-pill small fw-bold">
                                <i class="bi bi-check-lg me-1"></i> Link Generated!
                            </span>
                            <span class="text-muted small">Expires: <strong id="inlineExpires">24 Hours</strong></span>
                        </div>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control font-monospace bg-white fw-bold text-primary" id="inlineUrlInput" readonly>
                            <button type="button" class="btn btn-success fw-bold px-3" id="inlineCopyBtn">
                                <i class="bi bi-clipboard me-1"></i> Copy Link
                            </button>
                        </div>
                        <div class="d-flex flex-wrap gap-2 pt-1">
                            <a href="#" id="inlineTrackBtn" class="btn btn-dark btn-sm rounded-pill px-3">
                                <i class="bi bi-crosshair me-1"></i> Track Location Now
                            </a>
                            <a href="#" id="inlineWhatsAppBtn" target="_blank" class="btn btn-outline-success btn-sm rounded-pill px-3">
                                <i class="bi bi-whatsapp me-1"></i> WhatsApp
                            </a>
                        </div>
                    </div>

                    <form id="ajaxCreateLinkForm" action="{{ route('home.createLink') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="link_expires" class="form-label fw-bold text-dark small text-uppercase">Link Expiration Period</label>
                            <select name="expires_in_hours" id="link_expires" class="form-select form-select-lg rounded-3 fs-6">
                                <option value="1">1 Hour</option>
                                <option value="6">6 Hours</option>
                                <option value="24" selected>24 Hours (1 Day - Recommended)</option>
                                <option value="168">7 Days (1 Week)</option>
                                <option value="">Never (Until deleted)</option>
                            </select>
                            <div class="form-text small">Coordinates will be captured exclusively by this alphanumeric link until it expires.</div>
                        </div>

                        <button type="submit" id="generateLinkBtn" class="btn btn-gradient btn-lg w-100 py-3 rounded-3 d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-magic fs-5"></i>
                            <span id="generateBtnText">Create Alphanumeric Link</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES SECTION -->
<section class="py-6 py-md-7 bg-white border-top border-bottom" id="features" style="padding: 5rem 0;">
    <div class="container">
        <div class="text-center mb-5 max-w-xl mx-auto" style="max-width: 680px;">
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-1.5 rounded-pill fw-bold mb-2">
                Core Capabilities
            </span>
            <h2 class="fw-bold text-dark mb-3">Designed for Speed, Accuracy, and Ease</h2>
            <p class="text-muted">Everything you need to share, find, and monitor real-time locations effortlessly.</p>
        </div>

        <div class="row g-4">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="p-4 rounded-4 border bg-light h-100">
                    <div class="feature-icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Alphanumeric Security Code</h5>
                    <p class="text-muted small mb-0">Every link is generated with an unguessable alphanumeric code (letters & numbers). No names or personal identifiers required.</p>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="p-4 rounded-4 border bg-light h-100">
                    <div class="feature-icon-box bg-success bg-opacity-10 text-success">
                        <i class="bi bi-broadcast"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Auto-Refreshing Live Feed</h5>
                    <p class="text-muted small mb-0">No manual page reloads needed. The tracking screen automatically pings for new coordinates every 5 seconds.</p>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="p-4 rounded-4 border bg-light h-100">
                    <div class="feature-icon-box bg-info bg-opacity-10 text-info">
                        <i class="bi bi-link-45deg"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Track Solely by Link</h5>
                    <p class="text-muted small mb-0">Paste the tracking link into the search bar at any time to open the live map view immediately.</p>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="p-4 rounded-4 border bg-light h-100">
                    <div class="feature-icon-box bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-stopwatch-fill"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Flexible Expiration</h5>
                    <p class="text-muted small mb-0">Set your link to expire after 1 hour, 1 day, or 1 week to protect privacy once the tracking purpose is complete.</p>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="p-4 rounded-4 border bg-light h-100">
                    <div class="feature-icon-box bg-purple bg-opacity-10 text-purple" style="color: #9333ea; background: rgba(147, 51, 234, 0.1);">
                        <i class="bi bi-laptop"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Device & Telemetry Data</h5>
                    <p class="text-muted small mb-0">View precision metrics including latitude, longitude, altitude, speed, device operating system, and browser.</p>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="p-4 rounded-4 border bg-light h-100">
                    <div class="feature-icon-box bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Instant Copy & Share</h5>
                    <p class="text-muted small mb-0">One-click copy and pre-formatted WhatsApp/SMS sharing directly from the creation box.</p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1-Click Clipboard Paste button for Hero Search Bar
    const pasteBtn = document.getElementById('pasteClipboardBtn');
    const searchInput = document.getElementById('heroSearchInput');

    if (pasteBtn && searchInput) {
        pasteBtn.addEventListener('click', async function () {
            try {
                const text = await navigator.clipboard.readText();
                if (text) {
                    searchInput.value = text.trim();
                    searchInput.focus();
                }
            } catch (err) {
                console.log('Clipboard permission not granted:', err);
            }
        });
    }

    // 1-Click Copy for Session Generated Link Banner
    const activeCopyBtn = document.getElementById('activeCopyBtn');
    const activeShareUrlInput = document.getElementById('activeShareUrlInput');

    if (activeCopyBtn && activeShareUrlInput) {
        activeCopyBtn.addEventListener('click', function () {
            activeShareUrlInput.select();
            navigator.clipboard.writeText(activeShareUrlInput.value).then(() => {
                const orig = activeCopyBtn.innerHTML;
                activeCopyBtn.innerHTML = '<i class="bi bi-check2"></i> <span>Copied!</span>';
                setTimeout(() => activeCopyBtn.innerHTML = orig, 2000);
            });
        });
    }

    // AJAX Link Creation Handler (Creates 10-digit link & displays it immediately without full reload)
    const ajaxForm = document.getElementById('ajaxCreateLinkForm');
    const generateLinkBtn = document.getElementById('generateLinkBtn');
    const generateBtnText = document.getElementById('generateBtnText');
    const inlineResultBox = document.getElementById('inlineResultBox');
    const inlineUrlInput = document.getElementById('inlineUrlInput');
    const inlineCopyBtn = document.getElementById('inlineCopyBtn');
    const inlineTrackBtn = document.getElementById('inlineTrackBtn');
    const inlineWhatsAppBtn = document.getElementById('inlineWhatsAppBtn');
    const inlineExpires = document.getElementById('inlineExpires');

    if (ajaxForm) {
        ajaxForm.addEventListener('submit', function (e) {
            e.preventDefault();
            generateLinkBtn.disabled = true;
            generateBtnText.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating 10-Digit Link...';

            const formData = new FormData(ajaxForm);

            fetch("{{ route('home.createLink') }}", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                generateLinkBtn.disabled = false;
                generateBtnText.textContent = 'Create Another 10-Digit Link';

                if (data.success) {
                    inlineUrlInput.value = data.url;
                    inlineExpires.textContent = data.expires_at || '24 Hours';
                    inlineTrackBtn.href = data.track_url;
                    inlineWhatsAppBtn.href = 'https://api.whatsapp.com/send?text=' + encodeURIComponent('Please open this link to verify: ' + data.url);

                    inlineResultBox.style.display = 'block';
                    inlineResultBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            })
            .catch(err => {
                console.error(err);
                generateLinkBtn.disabled = false;
                generateBtnText.textContent = 'Create 10-Digit Link';
                ajaxForm.submit(); // fallback to standard submit
            });
        });
    }

    if (inlineCopyBtn && inlineUrlInput) {
        inlineCopyBtn.addEventListener('click', function () {
            inlineUrlInput.select();
            navigator.clipboard.writeText(inlineUrlInput.value).then(() => {
                const orig = inlineCopyBtn.innerHTML;
                inlineCopyBtn.innerHTML = '<i class="bi bi-check2"></i> Copied!';
                setTimeout(() => inlineCopyBtn.innerHTML = orig, 2000);
            });
        });
    }
});
</script>
@endpush
