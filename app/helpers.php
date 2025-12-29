<?php

if (!function_exists('accommodation_image_url')) {
    /**
     * Get the URL for an accommodation image
     * 
     * @param string $photoPath The path to the photo
     * @return string The URL to the image
     */
    function accommodation_image_url($photoPath) {
        if (empty($photoPath)) {
            return asset('images/accommodations/placeholder.jpg');
        }
        
        // For cPanel hosting, try public/images first, then fallback to storage
        $publicPath = 'images/accommodations/' . basename($photoPath);
        $publicFullPath = public_path($publicPath);
        
        if (file_exists($publicFullPath)) {
            return asset($publicPath);
        }
        
        // Fallback to storage path
        if (strpos($photoPath, 'storage/') === 0) {
            return asset($photoPath);
        }
        
        return asset('storage/' . $photoPath);
    }
}

