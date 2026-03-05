<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Serve protected customer media files.
     *
     * @param string $type
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function serveCustomerMedia($type, $filename)
    {
        $path = "customers/{$type}/{$filename}";

        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return Storage::disk('local')->response($path, null, [
            'X-Robots-Tag' => 'noindex, nofollow',
        ]);
    }

    /**
     * Serve protected evaluation media files.
     *
     * @param string $type
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function serveEvaluationMedia($type, $filename)
    {
        $path = "evaluations/{$type}/{$filename}";

        \Illuminate\Support\Facades\Log::info("Serving Media Request (Public)", [
            'path' => $path,
            'exists' => Storage::disk('local')->exists($path),
            'user_id' => auth()->id() ?? 'guest',
            'full_path' => Storage::disk('local')->path($path)
        ]);

        if (!Storage::disk('local')->exists($path)) {
            \Illuminate\Support\Facades\Log::warning("Media Not Found: $path");
            abort(404);
        }

        return Storage::disk('local')->response($path, null, [
            'X-Robots-Tag' => 'noindex, nofollow',
        ]);
    }

    /**
     * Serve protected customer visit media files.
     */
    public function serveCustomerVisitMedia($type, $filename)
    {
        $path = "customer-visits/{$type}/{$filename}";

        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return Storage::disk('local')->response($path, null, [
            'X-Robots-Tag' => 'noindex, nofollow',
        ]);
    }
}
