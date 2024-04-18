<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ProcessPodcast implements ShouldQueue/*, ShouldBeUnique*/
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //dump($this, func_get_args());
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (getenv('HARD_FAILURE')) {
            throw new \RuntimeException('Error for the test');
        }

        Cache::set('foo', [1, 2, 3], 3);
        sleep(4);
        dump(Cache::get('foo'));
        Cache::flush();


        dump('Handled message');
    }
}
