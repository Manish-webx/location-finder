@extends('layouts.app')

@section('title', $link ? 'Track Location - Link #' . $link->token : 'Track Location by Link - GeoTrack')

@section('content')
<div class="container py-4">

    <!-- Top Lookup Search Bar -->
    <div class="card-custom p-3 p-md-4 mb-4" style="background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%); border-color: rgba(255,255,255,0.1);">
        <form action="{{ route('home.lookup') }}" method="POST">
            @csrf
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-8 col-lg-9">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-link-45deg fs-5"></i></span>
                        <input type="text" name="query" class="form-control border-0 py-2.5 fs-6" placeholder="Paste link (e.g. {{ url('/l/7K9X2M4B1Q') }}) or code..." value="{{ $link ? url('/l/' . $link->token) : '' }}" required>
                    </div>
                </div>
                <div class="col-12 col-md-4 col-lg-3">
                    <button type="submit" class="btn btn-gradient w-100 py-2.5 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-search"></i>
                        <span>Find Location by Link</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if(!$link)
        <!-- Empty State when no token specified -->
        <div class="card-custom p-5 text-center my-5 mx-auto" style="max-width: 620px;">
            <div class="stat-icon bg-primary bg-opacity-10 text-primary mx-auto mb-3" style="width: 72px; height: 72px;">
                <i class="bi bi-crosshair fs-1"></i>
            </div>
            <h4 class="fw-bold text-dark mb-2">Track Any Live Link</h4>
            <p class="text-muted mb-4 small" style="line-height: 1.6;">
                Paste your created tracking link or alphanumeric code into the search bar above to view real-time location check-ins on the interactive map.
            </p>
            <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('home.index') }}#create-link-section" class="btn btn-gradient">
                    <i class="bi bi-plus-lg me-1"></i> Create a Tracking Link
                </a>
                @if(session('admin_logged_in'))
                <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-grid-fill me-1"></i> All Sessions
                </a>
                @endif
            </div>
        </div>
    @else
        <!-- Header & Link Details (Tracked by Link, no personal name) -->
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="{{ route('home.index') }}" class="text-muted text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('home.track') }}" class="text-muted text-decoration-none">Track</a></li>
                        <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">{{ $link->token }}</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-1 text-dark d-flex flex-wrap align-items-center gap-2">
                    <span>Link #{{ $link->token }}</span>
                    @if($link->isValid())
                        <span class="badge badge-live px-3 py-1 fs-6 rounded-pill"><i class="bi bi-circle-fill me-1 small"></i> Live</span>
                    @else
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 fs-6">Expired</span>
                    @endif
                </h3>
                <div class="text-muted small">
                    Created {{ $link->created_at->diffForHumans() }} &bull; 
                    Expires: {{ $link->expires_at ? $link->expires_at->format('M d, Y H:i') : 'Never' }}
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-secondary rounded-3 btn-sm px-3" id="manualRefreshBtn">
                    <i class="bi bi-arrow-clockwise me-1"></i> Refresh Map
                </button>
                <a href="{{ route('home.index') }}#create-link-section" class="btn btn-gradient rounded-3 btn-sm px-3">
                    <i class="bi bi-plus-lg me-1"></i> Create New Link
                </a>
            </div>
        </div>

        <!-- Share URL Bar -->
        <div class="card-custom p-3 mb-4 bg-light">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-3">
                    <span class="small fw-bold text-muted text-uppercase d-flex align-items-center gap-1">
                        <i class="bi bi-share-fill text-primary"></i> Shareable Link:
                    </span>
                </div>
                <div class="col-12 col-md-7">
                    <input type="text" class="form-control form-control-sm font-monospace bg-white border fw-bold text-primary" id="sessionShareLink" value="{{ url('/l/' . $link->token) }}" readonly>
                </div>
                <div class="col-12 col-md-2">
                    <button type="button" class="btn btn-primary btn-sm w-100 fw-semibold" id="copyTrackLinkBtn">
                        <i class="bi bi-clipboard me-1"></i> Copy Link
                    </button>
                </div>
            </div>
        </div>

        <!-- Live Map & Real-time Stats Card -->
        <div class="row g-4 mb-4">
            <!-- Map Column -->
            <div class="col-12 col-lg-8">
                <div class="card-custom p-3 p-md-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-map-fill text-primary fs-5"></i>
                            <span class="fw-bold text-dark">Interactive Location Map</span>
                        </div>
                        <div id="liveFeedStatus" class="small text-muted d-flex align-items-center gap-1">
                            <span class="spinner-grow spinner-grow-sm text-success" role="status" style="width: 8px; height: 8px;"></span>
                            <span id="liveFeedText">Auto-refreshing every 5s</span>
                        </div>
                    </div>

                    <!-- Map Container -->
                    <div id="trackingMap" style="height: 480px; width: 100%; border-radius: 16px; background: #e2e8f0; position: relative;">
                        <!-- Radar / Waiting Overlay if no visits yet -->
                        <div id="waitingOverlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-white bg-opacity-95 rounded-4 p-4 text-center" style="{{ $latestVisit ? 'display: none !important;' : '' }} z-index: 999;">
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary mb-3" style="width: 70px; height: 70px; animation: pulse-ring 2s infinite;">
                                <i class="bi bi-broadcast fs-2"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">Waiting for Link to be Opened</h5>
                            <p class="text-muted small mb-3" style="max-width: 440px;">
                                No location pings received yet for this tracking link. Send the link to your recipient and this map will automatically plot their location the moment they open it!
                            </p>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 copy-btn-trigger">
                                <i class="bi bi-clipboard me-1"></i> Copy Link to Share
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats & Details Column -->
            <div class="col-12 col-lg-4">
                <div class="d-flex flex-column gap-3 h-100">
                    <!-- Latest Coordinates Box -->
                    <div class="card-custom p-4 flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-muted small fw-bold text-uppercase">Latest Coordinates</span>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 small fw-bold" id="totalPingsBadge">
                                {{ $link->visits->count() }} Pings
                            </span>
                        </div>

                        <div id="latestCoordsContainer">
                            @if($latestVisit)
                                <div class="bg-light p-3 rounded-3 mb-3 border">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="text-muted small">Latitude:</span>
                                        <span class="fw-bold font-monospace text-dark" id="dispLat">{{ number_format($latestVisit->latitude, 6) }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="text-muted small">Longitude:</span>
                                        <span class="fw-bold font-monospace text-dark" id="dispLng">{{ number_format($latestVisit->longitude, 6) }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="text-muted small">Accuracy:</span>
                                        <span class="badge bg-info bg-opacity-10 text-info fw-semibold" id="dispAcc">&plusmn; {{ $latestVisit->accuracy ?? 'Unknown' }} m</span>
                                    </div>
                                </div>

                                <div class="small text-muted mb-3 d-flex flex-column gap-1.5">
                                    <div><i class="bi bi-clock me-1.5 text-primary"></i> <strong id="dispTime">{{ $latestVisit->created_at->diffForHumans() }}</strong> ({{ $latestVisit->created_at->format('H:i:s') }})</div>
                                    <div><i class="bi bi-laptop me-1.5 text-primary"></i> <span id="dispDevice">{{ Str::limit($latestVisit->device_info ?? $latestVisit->user_agent, 45) }}</span></div>
                                </div>

                                <a href="https://www.google.com/maps?q={{ $latestVisit->latitude }},{{ $latestVisit->longitude }}" target="_blank" class="btn btn-outline-dark btn-sm w-100 rounded-3 d-flex align-items-center justify-content-center gap-1 mb-2" id="googleMapsBtn">
                                    <i class="bi bi-box-arrow-up-right"></i> Open in Google Maps
                                </a>
                            @else
                                <div class="text-center py-4 text-muted small">
                                    <i class="bi bi-hourglass-split fs-3 text-warning d-block mb-2"></i>
                                    Awaiting first check-in...
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Link Sharing Box -->
                    <div class="card-custom p-4">
                        <h6 class="fw-bold text-dark mb-3 small text-uppercase">Share Link</h6>
                        <div class="d-flex flex-column gap-2">
                            <a href="https://api.whatsapp.com/send?text={{ urlencode('Please open this link to verify: ' . url('/l/' . $link->token)) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-3 d-flex align-items-center justify-content-center gap-1 py-2">
                                <i class="bi bi-whatsapp"></i> Share on WhatsApp
                            </a>
                            @if(session('admin_logged_in'))
                            <a href="{{ route('dashboard.show', $link->id) }}" class="btn btn-sm btn-light border rounded-3 d-flex align-items-center justify-content-center gap-1 py-2">
                                <i class="bi bi-sliders"></i> Manage in Dashboard
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Check-Ins Table -->
        <div class="card-custom p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0">Check-In Pings Log</h5>
                    <p class="text-muted small mb-0">All coordinate pings recorded for Link #{{ $link->token }}</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="py-2.5">Time</th>
                            <th scope="col" class="py-2.5">Coordinates (Lat, Lng)</th>
                            <th scope="col" class="py-2.5">Accuracy</th>
                            <th scope="col" class="py-2.5">Device / Browser</th>
                            <th scope="col" class="py-2.5 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody id="visitsTableBody">
                        @forelse($link->visits as $visit)
                            <tr>
                                <td class="fw-semibold text-dark">{{ $visit->created_at->format('M d, H:i:s') }}</td>
                                <td class="font-monospace text-primary fw-semibold">{{ number_format($visit->latitude, 6) }}, {{ number_format($visit->longitude, 6) }}</td>
                                <td><span class="badge bg-light text-dark border">&plusmn; {{ $visit->accuracy ?? '-' }} m</span></td>
                                <td class="text-muted">{{ Str::limit($visit->device_info ?? $visit->user_agent, 45) }}</td>
                                <td class="text-end">
                                    <a href="https://www.google.com/maps?q={{ $visit->latitude }},{{ $visit->longitude }}" target="_blank" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;">
                                        View Map
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyTableRow">
                                <td colspan="5" class="text-center py-4 text-muted">
                                    No location coordinates received yet. Send the tracking link to your recipient to start receiving pings.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
@if($link)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const token = "{{ $link->token }}";
    let mapInstance = null;
    let markerInstance = null;
    let circleInstance = null;

    // Copy Share Link
    const copyBtn = document.getElementById('copyTrackLinkBtn');
    const shareInput = document.getElementById('sessionShareLink');

    function copyLinkAction() {
        if (!shareInput) return;
        shareInput.select();
        navigator.clipboard.writeText(shareInput.value).then(() => {
            if (copyBtn) {
                const orig = copyBtn.innerHTML;
                copyBtn.innerHTML = '<i class="bi bi-check-lg text-success me-1"></i> Copied!';
                setTimeout(() => copyBtn.innerHTML = orig, 2000);
            }
        });
    }

    if (copyBtn) copyBtn.addEventListener('click', copyLinkAction);
    document.querySelectorAll('.copy-btn-trigger').forEach(el => el.addEventListener('click', copyLinkAction));

    // Initialize Map
    @if($latestVisit)
        const initLat = {{ $latestVisit->latitude }};
        const initLng = {{ $latestVisit->longitude }};
        const initAcc = {{ $latestVisit->accuracy ?? 20 }};
    @else
        const initLat = 20.5937;
        const initLng = 78.9629;
        const initAcc = null;
    @endif

    mapInstance = L.map('trackingMap').setView([initLat, initLng], {{ $latestVisit ? 16 : 4 }});

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(mapInstance);

    @if($latestVisit)
        markerInstance = L.marker([initLat, initLng]).addTo(mapInstance)
            .bindPopup("<b>Link #" + token + "</b><br>Latest Check-in<br>Lat: " + initLat.toFixed(5) + "<br>Lng: " + initLng.toFixed(5))
            .openPopup();

        if (initAcc) {
            circleInstance = L.circle([initLat, initLng], {
                radius: initAcc,
                color: '#4f46e5',
                fillColor: '#818cf8',
                fillOpacity: 0.2
            }).addTo(mapInstance);
        }
    @endif

    // Auto-polling function
    function fetchLiveData() {
        fetch("{{ route('home.trackData', $link->token) }}")
            .then(res => res.json())
            .then(data => {
                if (!data.success) return;

                if (data.latest_visit) {
                    const lat = parseFloat(data.latest_visit.latitude);
                    const lng = parseFloat(data.latest_visit.longitude);
                    const acc = parseFloat(data.latest_visit.accuracy) || 15;

                    // Hide waiting overlay
                    const overlay = document.getElementById('waitingOverlay');
                    if (overlay) overlay.style.setProperty('display', 'none', 'important');

                    // Update or create marker
                    if (!markerInstance) {
                        markerInstance = L.marker([lat, lng]).addTo(mapInstance)
                            .bindPopup("<b>Link #" + token + "</b><br>Live Ping<br>Lat: " + lat.toFixed(5) + "<br>Lng: " + lng.toFixed(5))
                            .openPopup();
                        mapInstance.setView([lat, lng], 16);
                    } else {
                        markerInstance.setLatLng([lat, lng]);
                        markerInstance.getPopup().setContent("<b>Link #" + token + "</b><br>Live Ping<br>Lat: " + lat.toFixed(5) + "<br>Lng: " + lng.toFixed(5));
                    }

                    if (!circleInstance) {
                        circleInstance = L.circle([lat, lng], { radius: acc, color: '#4f46e5', fillColor: '#818cf8', fillOpacity: 0.2 }).addTo(mapInstance);
                    } else {
                        circleInstance.setLatLng([lat, lng]);
                        circleInstance.setRadius(acc);
                    }

                    // Update DOM metrics
                    const dispLat = document.getElementById('dispLat');
                    const dispLng = document.getElementById('dispLng');
                    const dispAcc = document.getElementById('dispAcc');
                    const dispTime = document.getElementById('dispTime');
                    const pingsBadge = document.getElementById('totalPingsBadge');
                    const gMapsBtn = document.getElementById('googleMapsBtn');

                    if (dispLat) dispLat.textContent = lat.toFixed(6);
                    if (dispLng) dispLng.textContent = lng.toFixed(6);
                    if (dispAcc) dispAcc.textContent = '± ' + acc + ' m';
                    if (dispTime) dispTime.textContent = 'Just now';
                    if (pingsBadge) pingsBadge.textContent = data.visits_count + ' Pings';
                    if (gMapsBtn) gMapsBtn.href = "https://www.google.com/maps?q=" + lat + "," + lng;
                }
            })
            .catch(err => console.log('Polling check error:', err));
    }

    // Poll every 5 seconds
    setInterval(fetchLiveData, 5000);

    const manualRefreshBtn = document.getElementById('manualRefreshBtn');
    if (manualRefreshBtn) {
        manualRefreshBtn.addEventListener('click', function () {
            fetchLiveData();
            if (mapInstance) mapInstance.invalidateSize();
        });
    }
});
</script>
@endif
@endpush
