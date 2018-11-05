<?php

namespace Aixue\Tools;

use Aixue\Tools\Facades\ToolsFacade;
use Aixue\Tools\Services\RabbitMQ\MqHandler;
use Aixue\Tools\Services\Guzzle\GuzzleHandler;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Bootstrap the application events.
     * @return void
     */
    public function boot()
    {
        $this->publishRabbitMqConfigFile();
    }
    
    /**
     * Register the service provider.
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFile();
    
        $this->app->bind(
            MqHandler::class,
            function () {
                return (new MqHandler())->setConfigMap(config('aixue-mq'));
            }
        );
        $this->app->singleton(
            GuzzleHandler::class,
            function () {
                return new GuzzleHandler();
            }
        );
        
        $this->app->bind(
            Tools::class,
            function ($app) {
                return new Tools(
                    $app->make(MqHandler::class),
                    $app->make(GuzzleHandler::class)
                );
            }
        );
        
        AliasLoader::getInstance(['Tools' => ToolsFacade::class]);
    }
    
    public function publishRabbitMqConfigFile()
    {
        $configPath = __DIR__ . '/Services/RabbitMQ/Config/aixue-mq.php';
        
        $publishPath = function_exists('config_path')
                           ? config_path('aixue-mq.php')
                           : base_path('config/aixue-mq.php');
        
        $this->publishes([$configPath => $publishPath], 'mq-config');
    }
    
    public function mergeConfigFile()
    {
        $this->mergeConfigFrom(__DIR__ . '/Services/RabbitMQ/Config/aixue-mq.php', 'aixue-mq');
    }
}
