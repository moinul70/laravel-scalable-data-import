<?php

namespace App\Http\Controllers;


use App\Jobs\SyncExternalDataJob;
use Illuminate\Support\Facades\Cache;

class SyncController extends Controller
{
    public function __construct() {}


    public function triggerSync()
    {
        // Use job for large data datasets
        try {
            SyncExternalDataJob::dispatch();
            return response()->json(['status' => 'processing']);
        } catch (\RuntimeException $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Service Unavailable: ' . $e->getMessage()
            ], 503); // Service Unavailable status
        }
    }

    // Poll sync status
    public function syncStatus()
    {
        return response()->json([
            'status' => Cache::get('sync_status', 'idle')
        ]);
    }
}
