@extends('layouts.app')

@section('title', 'Admin Dashboard - GeoTrack')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Admin Header & Actions -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 border-bottom gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-danger bg-opacity-10 text-danger px-2.5 py-1 rounded-pill small fw-semibold">
                    <i class="bi bi-shield-lock-fill me-1"></i> Admin Portal
                </span>
                <span class="badge bg-success bg-opacity-10 text-success px-2.5 py-1 rounded-pill small fw-semibold">
                    <i class="bi bi-check-circle-fill me-1"></i> Authenticated Session
                </span>
            </div>
            <h3 class="fw-bold text-dark mb-0">System Control Center</h3>
            <p class="text-muted small mb-0">Private administration view &bull; All alphanumeric session links & coordinate logs</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-gradient btn-sm px-3 rounded-3" data-bs-toggle="modal" data-bs-target="#createLinkModal">
                <i class="bi bi-plus-lg me-1"></i> Generate New Link
            </button>
            <form action="{{ route('admin.logout') }}" method="POST" class="d-inline m-0">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm px-3 rounded-3 d-flex align-items-center gap-1">
                    <i class="bi bi-box-arrow-right"></i> Log Out
                </button>
            </form>
        </div>
    </div>

    <!-- Top Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="stat-card shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Links</div>
                        <div class="fs-2 fw-bold text-dark mt-1">{{ $totalLinks }}</div>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-link-45deg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="stat-card shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total GPS Pings</div>
                        <div class="fs-2 fw-bold text-dark mt-1">{{ $totalVisits }}</div>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-geo-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="stat-card shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Active Sessions</div>
                        <div class="fs-2 fw-bold text-dark mt-1">{{ $links->where('is_active', true)->count() }}</div>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-broadcast"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Map Card -->
    <div class="card-custom p-4 mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
            <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-map me-2 text-primary"></i>Live Overview</h5>
                <p class="text-muted small mb-0">Visualizing recent check-ins across all active sessions</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge badge-live px-3 py-2 rounded-pill"><i class="bi bi-circle-fill me-1 small"></i> Live Feed</span>
                <button id="refreshMapBtn" class="btn btn-sm btn-outline-secondary rounded-pill">
                    <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                </button>
            </div>
        </div>
        <div id="overviewMap" style="height: 420px; width: 100%; border-radius: 12px; background: #e2e8f0;"></div>
    </div>

    <!-- Links Management Table -->
    <div class="card-custom p-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
            <div>
                <h5 class="fw-bold mb-0">Generated Links</h5>
                <p class="text-muted small mb-0">Share these links to verify sessions and capture coordinates</p>
            </div>
            <button class="btn btn-gradient" data-bs-toggle="modal" data-bs-target="#createLinkModal">
                <i class="bi bi-plus-lg me-1"></i> Create New Link
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="py-3">Title & Token</th>
                        <th scope="col" class="py-3">Public Sharing URL</th>
                        <th scope="col" class="py-3 text-center">GPS Visits</th>
                        <th scope="col" class="py-3">Latest Coordinates</th>
                        <th scope="col" class="py-3">Status</th>
                        <th scope="col" class="py-3 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($links as $link)
                    @php
                        $shareUrl = url('/l/' . $link->token);
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $link->title }}</div>
                            <span class="badge bg-light text-dark border font-monospace">{{ $link->token }}</span>
                            <div class="text-muted small mt-1">{{ $link->created_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm" style="max-width: 320px;">
                                <input type="text" class="form-control font-monospace form-control-sm bg-light" value="{{ $shareUrl }}" readonly id="url-{{ $link->id }}">
                                <button class="btn btn-outline-primary copy-btn" data-clipboard-target="#url-{{ $link->id }}" title="Copy Link">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                                <a href="{{ $shareUrl }}" target="_blank" class="btn btn-outline-secondary" title="Open in new tab">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            </div>
                            @if($link->expires_at)
                                <div class="text-muted small mt-1">
                                    <i class="bi bi-clock me-1"></i>Expires: {{ $link->expires_at->format('M d, Y H:i') }}
                                </div>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $link->visits_count > 0 ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3 py-2">
                                {{ $link->visits_count }} {{ Str::plural('ping', $link->visits_count) }}
                            </span>
                        </td>
                        <td>
                            @if($link->latestVisit)
                                <div class="font-monospace small fw-semibold text-primary">
                                    {{ number_format($link->latestVisit->latitude, 5) }}, {{ number_format($link->latestVisit->longitude, 5) }}
                                </div>
                                <div class="text-muted small">
                                    Acc: ~{{ round($link->latestVisit->accuracy ?? 0) }}m &bull; {{ $link->latestVisit->created_at->diffForHumans() }}
                                </div>
                            @else
                                <span class="text-muted small fst-italic">No coordinates yet</span>
                            @endif
                        </td>
                        <td>
                            @if(!$link->isValid())
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">Expired / Off</span>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Active</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('home.track', ['token' => $link->token]) }}" class="btn btn-sm btn-outline-info" title="Live Tracker View" target="_blank">
                                    <i class="bi bi-crosshair"></i>
                                </a>
                                <a href="{{ route('dashboard.show', $link->id) }}" class="btn btn-sm btn-outline-primary" title="View Detailed Map & Logs">
                                    <i class="bi bi-geo-alt me-1"></i> Map
                                </a>
                                <form action="{{ route('dashboard.toggleLink', $link->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="{{ $link->is_active ? 'Deactivate' : 'Activate' }}">
                                        <i class="bi bi-power"></i>
                                    </button>
                                </form>
                                <form action="{{ route('dashboard.deleteLink', $link->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this link and all its visits?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-geo-alt display-4 d-block mb-3 text-secondary opacity-50"></i>
                            <h6 class="fw-bold">No location links created yet</h6>
                            <p class="small mb-3">Generate your first unique sharing link to start receiving real-time coordinates.</p>
                            <button class="btn btn-sm btn-gradient" data-bs-toggle="modal" data-bs-target="#createLinkModal">
                                <i class="bi bi-plus-lg me-1"></i> Create First Link
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Link Modal -->
<div class="modal fade" id="createLinkModal" tabindex="-1" aria-labelledby="createLinkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('dashboard.createLink') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="createLinkModalLabel">
                        <i class="bi bi-link-45deg text-primary me-1"></i> Generate Link
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">

                    <div class="mb-3">
                        <label for="expires_in_hours" class="form-label fw-semibold">Link Expiration</label>
                        <select name="expires_in_hours" id="expires_in_hours" class="form-select rounded-3">
                            <option value="">Never (Until manually deactivated)</option>
                            <option value="1">1 Hour</option>
                            <option value="6">6 Hours</option>
                            <option value="24" selected>24 Hours (1 Day)</option>
                            <option value="168">7 Days</option>
                        </select>
                    </div>
                    <div class="alert alert-info border-0 rounded-3 small mb-0">
                        <i class="bi bi-shield-check me-1"></i> <strong>Note:</strong> When visitors open this link, they will be prompted with a verification check to confirm their session.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gradient rounded-3">Generate Link</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Copy URL to Clipboard
    document.querySelectorAll('.copy-btn').forEach(button => {
        button.addEventListener('click', function () {
            const targetSelector = this.getAttribute('data-clipboard-target');
            const input = document.querySelector(targetSelector);
            if (input) {
                input.select();
                navigator.clipboard.writeText(input.value).then(() => {
                    const originalHTML = this.innerHTML;
                    this.innerHTML = '<i class="bi bi-check-lg text-success"></i>';
                    setTimeout(() => {
                        this.innerHTML = originalHTML;
                    }, 2000);
                });
            }
        });
    });

    // Initialize Leaflet Map
    const map = L.map('overviewMap').setView([20.5937, 78.9629], 5); // Default India or worldwide zoom

    // OpenStreetMap Tile Layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let markersLayer = L.featureGroup().addTo(map);

    function loadLocations() {
        fetch('{{ route("dashboard.liveData") }}')
            .then(res => res.json())
            .then(data => {
                markersLayer.clearLayers();
                if (data.visits && data.visits.length > 0) {
                    const bounds = [];
                    data.visits.forEach(visit => {
                        const lat = parseFloat(visit.latitude);
                        const lng = parseFloat(visit.longitude);
                        const accuracy = parseFloat(visit.accuracy) || 0;
                        const token = visit.link ? visit.link.token : 'Unknown';
                        const title = visit.link ? visit.link.title : 'Session';
                        const dateStr = new Date(visit.created_at).toLocaleString();

                        const popupHtml = `
                            <div style="font-size: 13px;">
                                <strong style="color: #4f46e5;">${title} (${token})</strong><br>
                                <strong>Coords:</strong> ${lat.toFixed(6)}, ${lng.toFixed(6)}<br>
                                <strong>Accuracy:</strong> ~${Math.round(accuracy)}m<br>
                                <strong>Time:</strong> ${dateStr}<br>
                                <a href="/admin/links/${visit.location_link_id}" class="btn btn-xs btn-primary text-white mt-1 py-0 px-2" style="font-size: 11px; text-decoration: none; border-radius: 4px; display: inline-block;">View Session</a>
                            </div>
                        `;

                        const marker = L.marker([lat, lng]).bindPopup(popupHtml);
                        markersLayer.addLayer(marker);

                        if (accuracy > 0) {
                            const circle = L.circle([lat, lng], {
                                radius: accuracy,
                                color: '#4f46e5',
                                fillColor: '#818cf8',
                                fillOpacity: 0.15,
                                weight: 1.5
                            });
                            markersLayer.addLayer(circle);
                        }

                        bounds.push([lat, lng]);
                    });

                    if (bounds.length > 0) {
                        map.fitBounds(bounds, { padding: [50, 50], maxZoom: 14 });
                    }
                }
            })
            .catch(err => console.error('Error fetching live map locations:', err));
    }

    loadLocations();

    document.getElementById('refreshMapBtn').addEventListener('click', function() {
        loadLocations();
    });

    // Auto-refresh map every 15 seconds
    setInterval(loadLocations, 15000);
});
</script>
@endpush
