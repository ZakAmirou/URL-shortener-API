<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use App\Jobs\ProcessUrl;
use App\Models\Url;

class ShortenUrlController extends Controller
{
    public function shorten(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid URL'
            ], 400);
        }

        $url = $request->input('url');

        // Check if the URL is already in the cache
        $key = 'shorturl:' . md5($url);
        if (Redis::exists($key)) {
            return response()->json([
                'short_url' => Redis::get($key)
            ]);
        }

        // Generate a new short URL and add it to the cache
        $shortUrl = Str::random(6);
        Redis::set($key, $shortUrl);
        Redis::set('url:' . $shortUrl, $url);

        // Create a new URL record in the database
        $newUrl = Url::create([
            'original_url' => $url,
            'short_url' => $shortUrl
        ]);

        // Queue the URL for processing
        ProcessUrl::dispatch($shortUrl);

        return response()->json([
            'short_url' => $shortUrl
        ]);
    }

    public function redirect($shortUrl)
    {
        // Retrieve the full URL from the database
        $url = Url::where('short_url', $shortUrl)->first();
    
        // Increment clicks
        $url->increment('clicks');
    
        // Return a JSON response with the original URL
        return response()->json([
            'original_url' => $url->original_url,
        ]);
    }
}
