<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class AccommodationController extends Controller
{
    /**
     * Display a listing of available accommodations for public viewing.
     */
    public function index(Request $request)
    {
        $query = Property::where('status', 'available')
                        ->where('is_available_for_hm', true)
                        ->with('provider');

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm)
                  ->orWhere('suburb', 'like', $searchTerm)
                  ->orWhere('state', 'like', $searchTerm);
            });
        }

        // Apply type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Apply state filter
        if ($request->filled('state')) {
            $query->where('state', $request->state);
        }

        // Apply suburb filter
        if ($request->filled('suburb')) {
            $query->where('suburb', $request->suburb);
        }

        // Apply bedroom filter
        if ($request->filled('bedrooms')) {
            $query->where('num_bedrooms', '>=', $request->bedrooms);
        }

        // Apply bathroom filter
        if ($request->filled('bathrooms')) {
            $query->where('num_bathrooms', '>=', $request->bathrooms);
        }

        // Apply rent range filter
        if ($request->filled('max_rent')) {
            $query->where('rent_per_week', '<=', $request->max_rent);
        }

        $accommodations = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        // Ensure photos and amenities are always arrays for each accommodation
        $accommodations->getCollection()->transform(function ($accommodation) {
            if (!is_array($accommodation->photos)) {
                $accommodation->photos = json_decode($accommodation->photos, true) ?? [];
            }
            if (!is_array($accommodation->amenities)) {
                $accommodation->amenities = json_decode($accommodation->amenities, true) ?? [];
            }
            return $accommodation;
        });

        // Data for filters
        $australianStates = $this->getAustralianStates();
        $accommodationTypes = [
            'Supported Independent Living',
            'Improved Livability',
            'Fully Accessible',
            'High Physical Support',
            'Robust'
        ];

        return view('listings', compact('accommodations', 'australianStates', 'accommodationTypes'));
    }

    /**
     * Display the specified accommodation.
     */
    public function show(Property $accommodation)
    {
        // Only show available accommodations
        if ($accommodation->status !== 'available' || !$accommodation->is_available_for_hm) {
            abort(404, 'Accommodation not found or not available.');
        }

        // Load the provider relationship
        $accommodation->load('provider');

        // Ensure amenities and photos are always arrays
        if (!is_array($accommodation->amenities)) {
            $accommodation->amenities = json_decode($accommodation->amenities, true) ?? [];
        }
        if (!is_array($accommodation->photos)) {
            $accommodation->photos = json_decode($accommodation->photos, true) ?? [];
        }

        // Get related accommodations (same type or location)
        $relatedAccommodations = Property::where('status', 'available')
            ->where('is_available_for_hm', true)
            ->where('id', '!=', $accommodation->id)
            ->where(function($query) use ($accommodation) {
                $query->where('type', $accommodation->type)
                      ->orWhere('state', $accommodation->state)
                      ->orWhere('suburb', $accommodation->suburb);
            })
            ->with('provider')
            ->limit(4)
            ->get();

        // Ensure photos and amenities are arrays for related accommodations
        $relatedAccommodations->transform(function ($related) {
            if (!is_array($related->photos)) {
                $related->photos = json_decode($related->photos, true) ?? [];
            }
            if (!is_array($related->amenities)) {
                $related->amenities = json_decode($related->amenities, true) ?? [];
            }
            return $related;
        });

        return view('accommodation-detail', compact('accommodation', 'relatedAccommodations'));
    }

    /**
     * Helper method to get Australian states
     */
    protected function getAustralianStates()
    {
        return [
            'ACT' => 'Australian Capital Territory',
            'NSW' => 'New South Wales',
            'NT' => 'Northern Territory',
            'QLD' => 'Queensland',
            'SA' => 'South Australia',
            'TAS' => 'Tasmania',
            'VIC' => 'Victoria',
            'WA' => 'Western Australia',
        ];
    }
}
