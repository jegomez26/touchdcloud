<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage; // Import Storage facade

class ProviderAccommodationController extends Controller
{
    // ... (index, show, edit, destroy methods remain largely the same, but update `edit` method for types)

    public function index(Request $request)
    {
        $providerId = Auth::user()->provider->id;

        $query = Accommodation::where('provider_id', $providerId);

        // Apply Search Filter
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm)
                  ->orWhere('address', 'like', $searchTerm)
                  ->orWhere('suburb', 'like', $searchTerm)
                  ->orWhere('post_code', 'like', $searchTerm);
            });
        }

        // Apply Type Filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Apply Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply State Filter
        if ($request->filled('state')) {
            $query->where('state', $request->state);
        }

        // Apply Suburb Filter
        if ($request->filled('suburb')) {
            $query->where('suburb', $request->suburb);
        }

        $accommodations = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Data for filters
        $australianStates = $this->getAustralianStates();
        $accommodationTypes = [
            'Supported Independent Living',
            'Improved Livability',
            'Fully Accessible',
            'High Physical Support',
            'Robust'
        ];
        $accommodationStatuses = [
            'draft', 'available', 'occupied', 'archived'
        ];

        return view('company.accommodations.index', compact('accommodations', 'australianStates', 'accommodationTypes', 'accommodationStatuses'));
    }

    /**
     * Show the form for creating a new accommodation.
     */
    public function create()
    {
        // Australian States
        $australianStates = [
            'ACT' => 'Australian Capital Territory',
            'NSW' => 'New South Wales',
            'NT' => 'Northern Territory',
            'QLD' => 'Queensland',
            'SA' => 'South Australia',
            'TAS' => 'Tasmania',
            'VIC' => 'Victoria',
            'WA' => 'Western Australia',
        ];

        // Accommodation Types with Optgroup structure
        $accommodationTypes = [
            'Supported Independent Living',
            'Specialist Disability Accommodation (SDA)' => [
                'Improved Livability',
                'Fully Accessible',
                'High Physical Support',
                'Robust',
            ],
        ];

        $amenitiesOptions = [
            'Wheelchair Accessible', 'Private Bathroom', 'Air Conditioning',
            'Heating', 'Furnished', 'Garden Access', 'Pet Friendly',
            'Wi-Fi', 'Laundry Facilities', 'Parking'
        ];

        return view('company.accommodations.create', compact('australianStates', 'accommodationTypes', 'amenitiesOptions'));
    }

    /**
     * Store a newly created accommodation in storage.
     */
    public function store(Request $request)
    {
        $provider = Auth::user()->provider;
        if (!$provider) {
            abort(403, 'No provider profile found for this user.');
        }

        // Flatten accommodation types for validation rules
        $validAccommodationTypes = [
            'Supported Independent Living',
            'Improved Livability',
            'Fully Accessible',
            'High Physical Support',
            'Robust',
        ];

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => ['required', 'string', Rule::in($validAccommodationTypes)],
            'address' => 'required|string|max:255',
            'suburb' => 'required|string|max:255', // Suburb validation will be against loaded data
            'state' => ['required', 'string', Rule::in(array_keys($this->getAustralianStates()))], // Validate against actual state codes
            'post_code' => 'required|string|max:10', // Consider adding regex validation for Australian postcodes
            'num_bedrooms' => 'required|integer|min:1',
            'num_bathrooms' => 'required|integer|min:1',
            'rent_per_week' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/', // Ensures 2 decimal places if present
            'is_available_for_hm' => 'boolean',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:255',
            'photos' => 'nullable|array|max:5', // Max 5 photos
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:1024', // Max 1MB per photo (1024 KB)
            'status' => ['required', 'string', Rule::in(['available', 'occupied', 'draft', 'archived'])],
            'total_vacancies' => 'required|integer|min:0',
            'current_occupancy' => 'required|integer|min:0|lte:total_vacancies',
        ]);

        // Handle amenities (ensure it's stored as JSON)
        $validatedData['amenities'] = json_encode($validatedData['amenities'] ?? []);

        // Handle photo uploads
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                // Store in 'public/accommodations' folder
                $path = $photo->store('accommodations', 'public');
                $photoPaths[] = $path;
            }
        }
        $validatedData['photos'] = json_encode($photoPaths);

        $accommodation = $provider->accommodations()->create($validatedData);

        return redirect()->route('provider.accommodations.show', $accommodation)
                         ->with('status', 'Accommodation created successfully.');
    }

    /**
     * Show the form for editing the specified accommodation.
     */
    public function edit(Accommodation $accommodation)
    {
        // Ensure the authenticated provider owns this accommodation
        if ($accommodation->provider_id !== Auth::user()->provider->id) {
            abort(403, 'Unauthorized action.');
        }

        // Australian States
        $australianStates = [
            'ACT' => 'Australian Capital Territory',
            'NSW' => 'New South Wales',
            'NT' => 'Northern Territory',
            'QLD' => 'Queensland',
            'SA' => 'South Australia',
            'TAS' => 'Tasmania',
            'VIC' => 'Victoria',
            'WA' => 'Western Australia',
        ];

        // Accommodation Types with Optgroup structure
        $accommodationTypes = [
            'Supported Independent Living',
            'Specialist Disability Accommodation (SDA)' => [
                'Improved Livability',
                'Fully Accessible',
                'High Physical Support',
                'Robust',
            ],
        ];

        $amenitiesOptions = [
            'Wheelchair Accessible', 'Private Bathroom', 'Air Conditioning',
            'Heating', 'Furnished', 'Garden Access', 'Pet Friendly',
            'Wi-Fi', 'Laundry Facilities', 'Parking'
        ];

        // Convert amenities and photos from JSON string back to array for forms
        $accommodation->amenities = json_decode($accommodation->amenities, true) ?? [];
        $accommodation->photos = json_decode($accommodation->photos, true) ?? [];

        return view('company.accommodations.edit', compact('accommodation', 'australianStates', 'accommodationTypes', 'amenitiesOptions'));
    }

    /**
     * Update the specified accommodation in storage.
     */
    public function update(Request $request, Accommodation $accommodation)
    {
        // Ensure the authenticated provider owns this accommodation
        if ($accommodation->provider_id !== Auth::user()->provider->id) {
            abort(403, 'Unauthorized action.');
        }

        // Flatten accommodation types for validation rules
        $validAccommodationTypes = [
            'Supported Independent Living',
            'Improved Livability',
            'Fully Accessible',
            'High Physical Support',
            'Robust',
        ];

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => ['required', 'string', Rule::in($validAccommodationTypes)],
            'address' => 'required|string|max:255',
            'suburb' => 'required|string|max:255',
            'state' => ['required', 'string', Rule::in(array_keys($this->getAustralianStates()))],
            'post_code' => 'required|string|max:10',
            'num_bedrooms' => 'required|integer|min:1',
            'num_bathrooms' => 'required|integer|min:1',
            'rent_per_week' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'is_available_for_hm' => 'boolean',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:255',
            'photos_to_keep' => 'nullable|array', // Array of paths of photos to keep
            'photos_to_keep.*' => 'string',
            'new_photos' => 'nullable|array|max:' . (5 - count($request->input('photos_to_keep', []))), // Max new photos based on remaining slots
            'new_photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:1024',
            'status' => ['required', 'string', Rule::in(['available', 'occupied', 'draft', 'archived'])],
            'total_vacancies' => 'required|integer|min:0',
            'current_occupancy' => 'required|integer|min:0|lte:total_vacancies',
        ]);

        // Handle amenities (ensure it's stored as JSON)
        $validatedData['amenities'] = json_encode($validatedData['amenities'] ?? []);

        // Handle photo updates
        $existingPhotos = json_decode($accommodation->photos, true) ?? [];
        $photosToKeep = $validatedData['photos_to_keep'] ?? [];
        $finalPhotoPaths = [];

        // Remove photos not in 'photos_to_keep'
        foreach ($existingPhotos as $path) {
            if (!in_array($path, $photosToKeep)) {
                Storage::disk('public')->delete($path);
            } else {
                $finalPhotoPaths[] = $path; // Keep the ones that are marked to be kept
            }
        }

        // Add new photos
        if ($request->hasFile('new_photos')) {
            foreach ($request->file('new_photos') as $photo) {
                $path = $photo->store('accommodations', 'public');
                $finalPhotoPaths[] = $path;
            }
        }
        $validatedData['photos'] = json_encode($finalPhotoPaths);

        $accommodation->update($validatedData);

        return redirect()->route('provider.accommodations.show', $accommodation)
                         ->with('status', 'Accommodation updated successfully.');
    }

    public function show(Accommodation $accommodation)
    {
        // Ensure the authenticated provider owns this accommodation
        if (!Auth::user()->provider || $accommodation->provider_id !== Auth::user()->provider->id) {
            abort(403, 'Unauthorized action.');
        }

        // Convert amenities and photos from JSON string back to array for display
        // The model's $casts property should handle this automatically if defined,
        // but explicit decoding here ensures it in case of casting issues or for older Laravel versions.
        $accommodation->amenities = json_decode($accommodation->amenities, true) ?? [];
        $accommodation->photos = json_decode($accommodation->photos, true) ?? [];

        return view('company.accommodations.show', compact('accommodation'));
    }


    // Helper method to get Australian states
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