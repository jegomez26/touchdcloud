<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response; // Required for JSON response

class LocationController extends Controller
{
    /**
     * Get suburbs based on the selected state.
     * This is a simplified example. In a real application, you'd fetch from a database or a comprehensive list.
     */
    public function getSuburbs($state)
    {
        // A placeholder for Australian states and their major suburbs.
        // In a real application, you'd likely fetch these from a database table
        // (e.g., a `suburbs` table with `state_id` or `state_code` foreign key).
        $suburbs = [];

        switch (strtoupper($state)) {
            case 'ACT':
                $suburbs = [
                    'Canberra', 'Belconnen', 'Gungahlin', 'Tuggeranong', 'Woden Valley',
                    'Queanbeyan', 'Googong', 'Jerrabomberra'
                ];
                break;
            case 'NSW':
                $suburbs = [
                    'Sydney', 'Parramatta', 'Newcastle', 'Wollongong', 'Central Coast',
                    'Blacktown', 'Liverpool', 'Penrith', 'Bondi', 'Manly'
                ];
                break;
            case 'NT':
                $suburbs = [
                    'Darwin', 'Palmerston', 'Alice Springs', 'Katherine', 'Howard Springs'
                ];
                break;
            case 'QLD':
                $suburbs = [
                    'Brisbane', 'Gold Coast', 'Sunshine Coast', 'Cairns', 'Townsville',
                    'Toowoomba', 'Logan', 'Ipswich'
                ];
                break;
            case 'SA':
                $suburbs = [
                    'Adelaide', 'Mawson Lakes', 'Glenelg', 'Port Adelaide', 'Noarlunga Centre'
                ];
                break;
            case 'TAS':
                $suburbs = [
                    'Hobart', 'Launceston', 'Devonport', 'Burnie', 'Kingston'
                ];
                break;
            case 'VIC':
                $suburbs = [
                    'Melbourne', 'Geelong', 'Ballarat', 'Bendigo', 'Frankston',
                    'Dandenong', 'Footscray', 'Richmond'
                ];
                break;
            case 'WA':
                $suburbs = [
                    'Perth', 'Fremantle', 'Rockingham', 'Mandurah', 'Joondalup',
                    'Bunbury', 'Albany'
                ];
                break;
            default:
                $suburbs = []; // No suburbs for unknown state
                break;
        }

        // Sort the suburbs alphabetically for a better user experience
        sort($suburbs);

        return Response::json($suburbs);
    }
}