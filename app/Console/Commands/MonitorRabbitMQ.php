<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ProcessPodcast;

class MonitorRabbitMQ extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitmq:monitor {--test : Enviar un podcast de prueba}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitorea el estado de las colas RabbitMQ y permite enviar trabajos de prueba';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🐰 Monitor de RabbitMQ - Sistema de Colas de Podcasts');
        $this->newLine();

        if ($this->option('test')) {
            $this->sendTestPodcast();
            return;
        }

        $this->showSystemInfo();
        $this->showQueueConfiguration();
        $this->showUsageInstructions();
    }

    /**
     * Mostrar información del sistema
     */
    private function showSystemInfo(): void
    {
        $this->info('📊 Información del Sistema:');
        $this->line('- Driver de Cola: ' . config('queue.default'));
        $this->line('- Host RabbitMQ: ' . config('queue.connections.rabbitmq.hosts.0.host', 'No configurado'));
        $this->line('- Puerto: ' . config('queue.connections.rabbitmq.hosts.0.port', 'No configurado'));
        $this->line('- Usuario: ' . config('queue.connections.rabbitmq.hosts.0.user', 'No configurado'));
        $this->line('- VHost: ' . config('queue.connections.rabbitmq.hosts.0.vhost', 'No configurado'));
        $this->line('- Cola por defecto: ' . config('queue.connections.rabbitmq.queue', 'No configurado'));
        $this->newLine();
    }

    /**
     * Mostrar configuración de colas
     */
    private function showQueueConfiguration(): void
    {
        $this->info('⚙️  Configuración de Colas:');
        
        if (config('queue.default') === 'rabbitmq') {
            $this->line('<fg=green>✅ RabbitMQ está configurado como driver por defecto</fg=green>');
        } else {
            $this->line('<fg=yellow>⚠️  RabbitMQ NO es el driver por defecto (actual: ' . config('queue.default') . ')</fg=yellow>');
            $this->line('<fg=yellow>   Cambia QUEUE_CONNECTION=rabbitmq en tu archivo .env</fg=yellow>');
        }
        $this->newLine();
    }

    /**
     * Mostrar instrucciones de uso
     */
    private function showUsageInstructions(): void
    {
        $this->info('🚀 Instrucciones de Uso:');
        $this->newLine();
        
        $this->line('<fg=cyan>1. Instalar RabbitMQ:</fg=cyan>');
        $this->line('   - Windows: Descargar desde https://www.rabbitmq.com/install-windows.html');
        $this->line('   - Docker: docker run -d --name rabbitmq -p 5672:5672 -p 15672:15672 rabbitmq:3-management');
        $this->newLine();
        
        $this->line('<fg=cyan>2. Iniciar el procesador de colas:</fg=cyan>');
        $this->line('   php artisan queue:work rabbitmq --queue=podcasts');
        $this->newLine();
        
        $this->line('<fg=cyan>3. Enviar un podcast de prueba:</fg=cyan>');
        $this->line('   php artisan rabbitmq:monitor --test');
        $this->newLine();
        
        $this->line('<fg=cyan>4. Monitorear RabbitMQ (interfaz web):</fg=cyan>');
        $this->line('   http://localhost:15672 (guest/guest)');
        $this->newLine();
        
        $this->line('<fg=cyan>5. Enviar via API:</fg=cyan>');
        $this->line('   POST /api/podcasts');
        $this->line('   {"title": "Mi Podcast", "url": "https://ejemplo.com/podcast.mp3"}');
        $this->newLine();
    }

    /**
     * Enviar un podcast de prueba
     */
    private function sendTestPodcast(): void
    {
        $this->info('🧪 Enviando podcast de prueba a RabbitMQ...');
        
        $testData = [
            'title' => 'Podcast de Prueba - ' . now()->format('Y-m-d H:i:s'),
            'url' => 'https://ejemplo.com/podcast-test-' . uniqid() . '.mp3'
        ];

        try {
            ProcessPodcast::dispatch($testData);
            
            $this->info('<fg=green>✅ Podcast de prueba enviado exitosamente!</fg=green>');
            $this->line('📋 Datos enviados:');
            $this->line('   Título: ' . $testData['title']);
            $this->line('   URL: ' . $testData['url']);
            $this->newLine();
            $this->line('<fg=yellow>💡 Asegúrate de que el worker esté ejecutándose:</fg=yellow>');
            $this->line('   php artisan queue:work rabbitmq --queue=podcasts');
            
        } catch (\Exception $e) {
            $this->error('❌ Error enviando podcast de prueba: ' . $e->getMessage());
            $this->newLine();
            $this->line('<fg=yellow>Verifica que:</fg=yellow>');
            $this->line('1. RabbitMQ esté instalado y ejecutándose');
            $this->line('2. Las configuraciones en .env sean correctas');
            $this->line('3. QUEUE_CONNECTION=rabbitmq en .env');
        }
    }
}
