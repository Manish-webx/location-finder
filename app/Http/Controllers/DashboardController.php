<?php

namespace App\Http\Controllers;

use App\Models\LocationLink;
use App\Models\LocationVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show admin login view.
     */
    public function showLogin(Request $request)
    {
        if ($request->session()->get('admin_logged_in')) {
            return redirect()->route('dashboard.index');
        }

        return view('dashboard.login');
    }

    /**
     * Authenticate admin credentials.
     */
    public function processLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $adminUser = env('ADMIN_USERNAME', 'admin');
        $adminPass = env('ADMIN_PASSWORD', 'admin123');

        if ($request->input('username') === $adminUser && $request->input('password') === $adminPass) {
            $request->session()->put('admin_logged_in', true);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard.index'))->with('success', 'Logged in to Admin Control Center.');
        }

        return back()->withInput()->with('error', 'Invalid admin credentials. Access denied.');
    }

    /**
     * Log out admin.
     */
    public function logout(Request $request)
    {
        $request->session()->forget('admin_logged_in');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home.index')->with('success', 'Admin session logged out successfully.');
    }

    /**
     * Display the main admin dashboard.
     */
    public function index()
    {
        $links = LocationLink::with(['latestVisit'])
            ->withCount('visits')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalLinks = $links->count();
        $totalVisits = LocationVisit::count();
        $recentVisits = LocationVisit::with('link')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact('links', 'totalLinks', 'totalVisits', 'recentVisits'));
    }

    /**
     * View details of a specific link and its visit history on a map.
     */
    public function show($id)
    {
        $link = LocationLink::with(['visits' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }])->findOrFail($id);

        return view('dashboard.show', compact('link'));
    }

    /**
     * Create a new tracking/sharing link.
     */
    public function createLink(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:100',
            'expires_in_hours' => 'nullable|integer|min:1|max:720',
        ]);

        // Generate unique 10-character alphanumeric token
        $token = LocationLink::generateUniqueToken(10);

        $expiresAt = null;
        if ($request->filled('expires_in_hours')) {
            $expiresAt = Carbon::now()->addHours((int) $request->input('expires_in_hours'));
        }

        LocationLink::create([
            'token' => $token,
            'title' => 'Link #' . $token,
            'is_active' => true,
            'expires_at' => $expiresAt,
        ]);

        return redirect()->route('dashboard.index')->with('success', "New link generated with token: {$token}");
    }

    /**
     * Toggle active/inactive status of a link.
     */
    public function toggleLink($id)
    {
        $link = LocationLink::findOrFail($id);
        $link->is_active = !$link->is_active;
        $link->save();

        return redirect()->back()->with('success', 'Link status updated successfully.');
    }

    /**
     * Delete a link and its recorded visits.
     */
    public function deleteLink($id)
    {
        $link = LocationLink::findOrFail($id);
        $link->delete();

        return redirect()->route('dashboard.index')->with('success', 'Link and visit history deleted.');
    }

    /**
     * JSON data endpoint for live map auto-refresh.
     */
    public function liveData($id = null)
    {
        if ($id) {
            $link = LocationLink::with('visits')->findOrFail($id);
            return response()->json([
                'link' => $link,
                'visits' => $link->visits,
            ]);
        }

        $visits = LocationVisit::with('link')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'visits' => $visits,
        ]);
    }
}
