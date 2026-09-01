<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrganizationProfileController extends Controller
{
    public function show()
    {
        $organization = auth()->user()->organization;
        return view('organization.profile.show', compact('organization'));
    }

    public function update(Request $request)
    {
        $organization = auth()->user()->organization;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'gst_number' => 'nullable|string|max:15|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
            'default_check_in' => 'nullable|string',
            'default_check_out' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [

            'gst_number.regex' => 'Please enter a valid 15-digit GSTIN number format.',
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($organization->logo) {
                Storage::disk('public')->delete($organization->logo);
            }
            $validated['logo'] = \App\Services\ImageOptimizer::compressAndStore($request->file('logo'), 'logos', 'public', 600, 80);
        }

        $organization->update($validated);


        return redirect()->route('organization.profile')->with('success', 'Organization profile updated successfully.');
    }

    private function compressAndSaveImage($file, $directory)
    {
        $tempPath = $file->getRealPath();
        $info = getimagesize($tempPath);

        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($tempPath);
        } elseif ($info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($tempPath);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($tempPath);
        } else {
            return $file->store($directory, 'public');
        }

        // Generate custom name
        $filename = uniqid() . '.jpg';
        $destinationDir = storage_path('app/public/' . $directory);
        if (!file_exists($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }
        $destinationPath = $destinationDir . '/' . $filename;

        // Compress and save as JPEG with 60% quality
        imagejpeg($image, $destinationPath, 60);
        imagedestroy($image);

        return $directory . '/' . $filename;
    }
}
