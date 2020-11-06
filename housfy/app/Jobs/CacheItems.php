<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class CacheItems implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const CACHE_MINUTES = 2;

    protected $item;
    protected $cacheKey;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($item, $cacheKey)
    {
        $this->item = $item;
        $this->cacheKey = $cacheKey;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Cache::put($this->cacheKey, $this->item, Carbon::now()->add(self::CACHE_MINUTES.' minutes'));
    }

}
