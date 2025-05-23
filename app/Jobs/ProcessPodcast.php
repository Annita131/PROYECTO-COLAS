<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; 

class ProcessPodcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $podcastData;

    /**
     * Create a new job instance.
     *
     * @param array $podcastData
     * @return void
     */
    public function __construct($podcastData)
    {
        $this->podcastData = $podcastData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Log::info('Procesando podcast:', ['data' => $this->podcastData]);

        sleep(10); // Espera 10 segundos

        Log::info('Podcast procesado:', ['data' => $this->podcastData]);
    }
}
