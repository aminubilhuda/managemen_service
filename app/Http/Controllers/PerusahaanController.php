<?php

namespace App\Http\Controllers;

use App\Models\Perusahaan;
use Illuminate\Http\Request;

class PerusahaanController extends Controller
{
    public function edit()
    {
        $perusahaan = Perusahaan::first() ?? new Perusahaan;

        return view('pengaturan.perusahaan', compact('perusahaan'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'alamat' => 'nullable|string',
            'telp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'npwp' => 'nullable|string|max:30',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logo', 'public');
            $fullPath = storage_path('app/public/'.$path);

            if (extension_loaded('gd') && file_exists($fullPath)) {
                $info = @getimagesize($fullPath);
                if ($info) {
                    $width = $info[0];
                    $height = $info[1];
                    $maxDim = 600;

                    if ($width > $maxDim || $height > $maxDim) {
                        @ini_set('memory_limit', '512M');
                        $scale = min($maxDim / $width, $maxDim / $height);
                        $newW = (int) round($width * $scale);
                        $newH = (int) round($height * $scale);

                        $mime = $info['mime'] ?? '';
                        $src = match ($mime) {
                            'image/jpeg' => @imagecreatefromjpeg($fullPath),
                            'image/png' => @imagecreatefrompng($fullPath),
                            'image/webp' => @imagecreatefromwebp($fullPath),
                            default => null,
                        };

                        if ($src) {
                            $dst = imagecreatetruecolor($newW, $newH);
                            if ($mime === 'image/png' || $mime === 'image/webp') {
                                imagealphablending($dst, false);
                                imagesavealpha($dst, true);
                                $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
                                imagefilledrectangle($dst, 0, 0, $newW, $newH, $transparent);
                            }

                            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);

                            match ($mime) {
                                'image/jpeg' => imagejpeg($dst, $fullPath, 90),
                                'image/png' => imagepng($dst, $fullPath, 6),
                                'image/webp' => imagewebp($dst, $fullPath, 90),
                                default => null,
                            };
                        }
                    }
                }
            }

            $validated['logo'] = $path;
        }

        Perusahaan::updateOrCreate(['id' => 1], $validated);

        return back()->with('success', 'Data perusahaan berhasil diperbarui.');
    }
}
