<?php

namespace App\Http\Controllers;

use App\Models\LocationLink;
use App\Models\LocationVisit;
use Illuminate\Http\Request;

class LocationShareController extends Controller
{
    /**
     * Display the transparent consent & location sharing landing page.
     */
    public function show($token)
    {
        $link = LocationLink::where('token', $token)
            ->orWhere('token', strtoupper($token))
            ->first();

        if (!$link) {
            return view('share.invalid', ['message' => 'This link does not exist.']);
        }

        if (!$link->isValid()) {
            return view('share.invalid', [
                'message' => 'This link has expired or has been deactivated.',
                'link' => $link
            ]);
        }

        return view('share.index', compact('link'));
    }

    /**
     * Process coordinates submitted.
     */
    public function store(Request $request, $token)
    {
        $link = LocationLink::where('token', $token)
            ->orWhere('token', strtoupper($token))
            ->first();

        if (!$link || !$link->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'This link is invalid or expired.',
            ], 403);
        }

        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric',
            'altitude' => 'nullable|numeric',
            'speed' => 'nullable|numeric',
            'heading' => 'nullable|numeric',
            'device_info' => 'nullable|string|max:255',
        ]);

        $visit = LocationVisit::create([
            'location_link_id' => $link->id,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'accuracy' => $validated['accuracy'] ?? null,
            'altitude' => $validated['altitude'] ?? null,
            'speed' => $validated['speed'] ?? null,
            'heading' => $validated['heading'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_info' => $validated['device_info'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Verification completed successfully.',
            'visit_id' => $visit->id,
            'timestamp' => $visit->created_at->toIso8601String(),
        ]);
    }
}
