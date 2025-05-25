<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class ProcessPodcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $podcastData;
    
    /**
     * Número de intentos permitidos
     */
    public $tries = 3;
    
    /**
     * Tiempo de espera en segundos antes de timeout
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     *
     * @param array $podcastData
     * @return void
     */
    public function __construct($podcastData)
    {
        $this->podcastData = $podcastData;
        // Especificar la cola de RabbitMQ para este job
        $this->onQueue('podcasts');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Log::info('🎧 Iniciando procesamiento de podcast via RabbitMQ', [
            'data' => $this->podcastData,
            'attempt' => $this->attempts(),
            'queue' => 'podcasts'
        ]);

        try {
            // Simulación de procesamiento del podcast
            $this->processPodcastData();
            
            Log::info('✅ Podcast procesado exitosamente via RabbitMQ', [
                'data' => $this->podcastData,
                'processing_time' => '10 segundos'
            ]);
            
        } catch (Exception $e) {
            Log::error('❌ Error procesando podcast via RabbitMQ', [
                'data' => $this->podcastData,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);
            
            // Si no es el último intento, re-lanzar la excepción para retry
            if ($this->attempts() < $this->tries) {
                throw $e;
            }
        }
    }
    
    /**
     * Simular procesamiento del podcast
     */
    private function processPodcastData(): void
    {
        Log::info('📥 Descargando podcast...', ['url' => $this->podcastData['url']]);
        sleep(3);
        
        Log::info('🔄 Procesando audio...', ['title' => $this->podcastData['title']]);
        sleep(4);
        
        Log::info('💾 Guardando metadatos...', ['data' => $this->podcastData]);
        sleep(3);
        
        if (rand(1, 100) <= 5) {
            throw new Exception('Error simulado en el procesamiento');
        }
    }

    /**
     * Handle a job failure.
     *
     * @param Exception $exception
     * @return void
     */
    public function failed(Exception $exception): void
    {
        Log::error('💥 Job falló definitivamente después de todos los intentos', [
            'data' => $this->podcastData,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
        
        // Aquí podrías enviar una notificación, email, etc.
    }
}
