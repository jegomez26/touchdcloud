<?php

namespace App\Http\Controllers;

use App\Models\NdisBusiness;
use Illuminate\Http\Request;

class NdisBusinessController extends Controller
{
    /**
     * Display a listing of the NDIS businesses.
     */
    public function index(Request $request)
    {
        $query = NdisBusiness::query();

        // Apply filter if search term is provided
        if ($request->has('search') && $request->input('search') != '') {
            $search = $request->input('search');
            $query->where('business_name', 'like', '%' . $search . '%')
                  ->orWhere('abn', 'like', '%' . $search . '%')
                  ->orWhere('services_offered', 'like', '%' . $search . '%');
        }

        $ndisBusinesses = $query->latest()->paginate(10); // Paginate results

        return view('supadmin.ndis-businesses', compact('ndisBusinesses'));
    }

    /**
     * Store a newly created NDIS business in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'abn' => 'required|string|unique:ndis_businesses,abn|max:11|min:11', // ABN is typically 11 digits
            'services_offered' => 'nullable|array|min:1',
        ]);
        // dd($validated);

        NdisBusiness::create($validated);

        return back()->with('success', 'NDIS Business added successfully!');
    }

    /**
     * Update the specified NDIS business in storage.
     */
    public function update(Request $request, NdisBusiness $ndisBusiness)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'abn' => 'required|string|max:11|min:11|unique:ndis_businesses,abn,' . $ndisBusiness->id,
            'services_offered' => 'nullable|array|min:1',
        ]);

        $ndisBusiness->update($validated);

        return back()->with('success', 'NDIS Business updated successfully!');
    }

    /**
     * Remove the specified NDIS business from storage.
     */
    public function destroy(NdisBusiness $ndisBusiness)
    {
        $ndisBusiness->delete();

        return back()->with('success', 'NDIS Business deleted successfully!');
    }
}