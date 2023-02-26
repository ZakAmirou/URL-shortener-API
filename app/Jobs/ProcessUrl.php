<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;

class ProcessUrl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $shortUrl;

    /**
     * Create a new job instance.
     *
     * @param  string  $shortUrl
     * @return void
     */
    public function __construct($shortUrl)
    {
        $this->shortUrl = $shortUrl;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $url = Redis::get('url:' . $this->shortUrl);
        
        $response = Http::get($url);
        
        Redis::set('html:' . $this->shortUrl, $response->body());
    }
}
