<?php

namespace App\Console\Commands;

use App\Jobs\ProcessPodcast;
use App\Models\Podcast;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class Enqueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:enqueue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /*
        $podcast = Podcast::create();
        ProcessPodcast::dispatch($podcast);

        $this->output->writeln('Enqueued Podcast successfully: '.$podcast->id);
        */

        $podcasts = Podcast::all();

        $batch = [];
        foreach ($podcasts as $podcast) {
            $batch[] = ProcessPodcast::dispatch($podcast);
            $this->output->writeln('Enqueued Podcast successfully: '.$podcast->id);
        }

        Bus::batch($batch);
    }
}
