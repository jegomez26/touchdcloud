<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProviderEnquiryController extends Controller
{
    /**
     * Display a listing of enquiries for the provider's properties.
     */
    public function index(Request $request)
    {
        $provider = Auth::user()->provider;
        
        if (!$provider) {
            return redirect()->route('provider.dashboard')->with('error', 'Provider profile not found.');
        }

        $query = Enquiry::whereHas('property', function($q) use ($provider) {
            $q->where('provider_id', $provider->id);
        })->with(['property']);

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply property filter
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm)
                  ->orWhere('message', 'like', $searchTerm);
            });
        }

        $enquiries = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Get provider's properties for filter dropdown
        $properties = $provider->properties()->where('status', 'available')->get();

        // Get enquiry statistics
        $stats = [
            'total' => Enquiry::whereHas('property', function($q) use ($provider) {
                $q->where('provider_id', $provider->id);
            })->count(),
            'pending' => Enquiry::whereHas('property', function($q) use ($provider) {
                $q->where('provider_id', $provider->id);
            })->where('status', 'pending')->count(),
            'tended' => Enquiry::whereHas('property', function($q) use ($provider) {
                $q->where('provider_id', $provider->id);
            })->where('status', 'tended')->count(),
            'closed' => Enquiry::whereHas('property', function($q) use ($provider) {
                $q->where('provider_id', $provider->id);
            })->where('status', 'closed')->count(),
        ];

        // Get subscription status for the provider dashboard
        $subscriptionStatus = \App\Services\SubscriptionService::getSubscriptionStatus();

        return view('company.enquiries.index', compact('enquiries', 'properties', 'stats', 'subscriptionStatus'));
    }

    /**
     * Display the specified enquiry.
     */
    public function show(Enquiry $enquiry)
    {
        $provider = Auth::user()->provider;
        
        if (!$provider) {
            return redirect()->route('provider.dashboard')->with('error', 'Provider profile not found.');
        }

        // Ensure the enquiry belongs to the provider's property
        if ($enquiry->property->provider_id !== $provider->id) {
            abort(403, 'Unauthorized access to enquiry.');
        }

        $enquiry->load(['property']);

        // Get subscription status for the provider dashboard
        $subscriptionStatus = \App\Services\SubscriptionService::getSubscriptionStatus();

        return view('company.enquiries.show', compact('enquiry', 'subscriptionStatus'));
    }

    /**
     * Update the enquiry status and notes.
     */
    public function update(Request $request, Enquiry $enquiry)
    {
        $provider = Auth::user()->provider;
        
        if (!$provider) {
            return redirect()->route('provider.dashboard')->with('error', 'Provider profile not found.');
        }

        // Ensure the enquiry belongs to the provider's property
        if ($enquiry->property->provider_id !== $provider->id) {
            abort(403, 'Unauthorized access to enquiry.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,tended,closed',
            'provider_notes' => 'nullable|string|max:1000',
        ]);

        // Update tended_at timestamp when status changes to tended
        if ($validated['status'] === 'tended' && $enquiry->status !== 'tended') {
            $validated['tended_at'] = now();
        }

        $enquiry->update($validated);

        return redirect()->route('provider.enquiries.show', $enquiry)
                        ->with('success', 'Enquiry updated successfully.');
    }

    /**
     * Store a new enquiry from the public accommodation detail page.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|max:1000',
        ]);

        // Ensure the property is available for enquiries
        $property = Property::findOrFail($validated['property_id']);
        if ($property->status !== 'available' || !$property->is_available_for_hm) {
            return redirect()->back()->with('error', 'This accommodation is not available for enquiries.');
        }

        Enquiry::create($validated);

        return redirect()->back()->with('success', 'Your enquiry has been sent successfully. The provider will contact you soon.');
    }
}