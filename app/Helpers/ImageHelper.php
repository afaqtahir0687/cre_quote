<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ImageHelper
{
    /**
     * Compress and save an image.
     *
     * @param UploadedFile $file The uploaded file instance
     * @param string $destination The destination folder (e.g., 'uploads/quotes')
     * @param int $quality Quality from 0 to 100
     * @param int $maxWidth Max width to resize down to
     * @return string The saved filename
     */
    public static function compressAndSave(UploadedFile $file, string $destination, int $quality = 70, int $maxWidth = 1200): string
    {
        $filename = time() . '_' . Str::random(10) . '.jpg'; // Always convert to jpg for compression
        $destinationPath = public_path($destination);

        // Ensure folder exists
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $targetPath = $destinationPath . '/' . $filename;

        // Get image info
        $info = getimagesize($file->getRealPath());
        $mime = $info['mime'];

        // Create image from original file based on mime type
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($file->getRealPath());
                break;
            case 'image/png':
                $image = imagecreatefrompng($file->getRealPath());
                // Fill transparent background with white
                $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
                imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
                imagealphablending($bg, true);
                imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                imagedestroy($image);
                $image = $bg;
                break;
            case 'image/gif':
                $image = imagecreatefromgif($file->getRealPath());
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($file->getRealPath());
                break;
            default:
                // Fallback to basic move if not an image type we can process
                $originalName = time() . '_' . $file->getClientOriginalName();
                $file->move($destinationPath, $originalName);
                return $originalName;
        }

        // Check if we need to resize
        $width = imagesx($image);
        $height = imagesy($image);

        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int)($height * ($maxWidth / $width));
            
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preserve transparency for PNGs if needed (though we're saving as JPG, so we filled it with white above)
            imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            
            // Output as JPEG
            imagejpeg($resizedImage, $targetPath, $quality);
            
            imagedestroy($resizedImage);
        } else {
            // Output as JPEG without resizing
            imagejpeg($image, $targetPath, $quality);
        }

        imagedestroy($image);

        return $filename;
    }
}
