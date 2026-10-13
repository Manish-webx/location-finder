<?php

namespace App\Http\Controllers;

use App\Models\LocationLink;
use App\Models\LocationVisit;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Display the public homepage / product landing page.
     */
    public function index()
    {
        $totalLinks = LocationLink::count();
        $totalCheckins = LocationVisit::count();
        $recentActiveCount = LocationLink::where('is_active', true)->count();

        return view('home.index', compact('totalLinks', 'totalCheckins', 'recentActiveCount'));
    }

    /**
     * Parse and lookup a pasted link or alphanumeric token, then redirect to tracking view.
     */
    public function lookup(Request $request)
    {
        $input = trim($request->input('query', ''));

        if (empty($input)) {
            return redirect()->route('home.index')->with('error', 'Please enter a valid tracking link or code.');
        }

        // Extract token: match /l/{token} in URL, bare alphanumeric string, or URL path
        $token = $input;
        if (preg_match('/\/l\/([0-9A-Za-z]+)/i', $input, $matches)) {
            $token = $matches[1];
        } elseif (preg_match('/\b([0-9A-Za-z]{6,16})\b/', $input, $matches)) {
            $token = $matches[1];
        } else {
            $token = basename(parse_url($input, PHP_URL_PATH) ?? $input);
        }
        $token = trim($token);

        $link = LocationLink::where('token', $token)
            ->orWhere('token', strtoupper($token))
            ->first();

        if (!$link) {
            return redirect()->route('home.index')
                ->withInput()
                ->with('error', "No session found for '{$input}'. Please check the tracking link or code.");
        }

        return redirect()->route('home.track', ['token' => $link->token]);
    }

    /**
     * Display the live location tracking view for a specific link/token.
     */
    public function track($token = null)
    {
        if (!$token) {
            return view('home.track', ['link' => null]);
        }

        $link = LocationLink::with(['visits' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }])->where('token', trim($token))
           ->orWhere('token', strtoupper(trim($token)))
           ->first();

        if (!$link) {
            return redirect()->route('home.index')->with('error', "Session '#{$token}' was not found or has expired.");
        }

        $latestVisit = $link->visits->first();

        return view('home.track', compact('link', 'latestVisit'));
    }

    /**
     * Generate an instant alphanumeric tracking link.
     */
    public function createLink(Request $request)
    {
        $request->validate([
            'expires_in_hours' => 'nullable|integer|min:1|max:720',
        ]);

        // Generate unique 10-character alphanumeric token
        $token = LocationLink::generateUniqueToken(10);

        $expiresAt = null;
        if ($request->filled('expires_in_hours')) {
            $expiresAt = Carbon::now()->addHours((int) $request->input('expires_in_hours'));
        }

        $link = LocationLink::create([
            'token' => $token,
            'title' => 'Link #' . $token,
            'is_active' => true,
            'expires_at' => $expiresAt,
        ]);

        // Support AJAX creation for seamless 1-click display
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'token' => $link->token,
                'url' => url('/l/' . $link->token),
                'track_url' => route('home.track', ['token' => $link->token]),
                'expires_at' => $link->expires_at ? $link->expires_at->diffForHumans() : 'Never',
            ]);
        }

        return redirect()->route('home.index')
            ->with('success', "Alphanumeric tracking link generated: " . url('/l/' . $link->token))
            ->with('generated_link', $link);
    }

    /**
     * Real-time JSON data endpoint for live map polling on tracking page.
     */
    public function trackData($token)
    {
        $link = LocationLink::with(['visits' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }])->where('token', trim($token))->first();

        if (!$link) {
            return response()->json(['success' => false, 'message' => 'Session not found'], 404);
        }

        return response()->json([
            'success' => true,
            'link' => $link,
            'visits_count' => $link->visits->count(),
            'latest_visit' => $link->visits->first(),
            'visits' => $link->visits->take(20),
        ]);
    }
}
