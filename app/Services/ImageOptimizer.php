<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageOptimizer
{
    /**
     * Compress and optimize an uploaded image before storing it on disk.
     *
     * @param UploadedFile $file The uploaded file instance
     * @param string $directory Storage directory (e.g. 'products', 'logos')
     * @param string $disk Storage disk ('public')
     * @param int $maxWidth Max width to downscale large images (default: 1200px)
     * @param int $quality Compression quality (1-100, default: 75)
     * @return string Relative storage path of compressed file
     */
    public static function compressAndStore(
        UploadedFile $file,
        string $directory = 'uploads',
        string $disk = 'public',
        int $maxWidth = 1200,
        int $quality = 75
    ): string {
        $filename = uniqid() . '_' . time() . '.jpg';
        $relativeFolder = trim($directory, '/');
        $destinationDir = public_path("uploads/{$relativeFolder}");
        $destinationPath = "{$destinationDir}/{$filename}";

        // Ensure target directory exists
        if (!file_exists($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        $imageInfo = @getimagesize($file->getRealPath());
        if (!$imageInfo) {
            return $file->store($directory, $disk);
        }

        $mime = $imageInfo['mime'];
        $srcImage = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($file->getRealPath()),
            'image/png' => @imagecreatefrompng($file->getRealPath()),
            'image/webp' => @imagecreatefromwebp($file->getRealPath()),
            'image/gif' => @imagecreatefromgif($file->getRealPath()),
            default => null,
        };

        if (!$srcImage) {
            return $file->store($directory, $disk);
        }

        $origWidth = imagesx($srcImage);
        $origHeight = imagesy($srcImage);

        // Downscale oversized images proportionately
        if ($origWidth > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) round(($origHeight / $origWidth) * $maxWidth);
        } else {
            $newWidth = $origWidth;
            $newHeight = $origHeight;
        }

        // Create truecolor canvas
        $dstImage = imagecreatetruecolor($newWidth, $newHeight);

        // Fill white background for transparent images
        $white = imagecolorallocate($dstImage, 255, 255, 255);
        imagefill($dstImage, 0, 0, $white);

        // Resample with high anti-aliasing quality
        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        // Save as compressed JPEG with 75% quality
        imagejpeg($dstImage, $destinationPath, $quality);

        // Clean up GD memory buffers
        imagedestroy($srcImage);
        imagedestroy($dstImage);

        return "{$relativeFolder}/{$filename}";
    }
}
