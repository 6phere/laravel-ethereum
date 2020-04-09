<?php
namespace Jcsofts\LaravelEthereum;

use Illuminate\Config\Repository;
use Illuminate\Support\ServiceProvider;
use Jcsofts\LaravelEthereum\Lib\Ethereum;

/**
 * Created by PhpStorm.
 * User: lee
 * Date: 11/12/2017
 * Time: 1:38 PM
 */
class EthereumServiceProvider extends ServiceProvider
{
    protected $defer = true;
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $dist = __DIR__.'/../config/ethereum.php';
        if (function_exists('config_path')) {
            // Publishes config File.
            $this->publishes([
                $dist => config_path('ethereum.php'),
            ]);
        }
        $this->mergeConfigFrom($dist, 'ethereum');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Ethereum::class, function ($app) {
            return $this->createInstance(collect(config('ethereum')));
        });
    }

    public function provides()
    {
        return [Ethereum::class];
    }

    protected function createInstance($config)
    {
        // Check for ethereum config file.
        if (! $this->hasConfigSection($config)) {
            $this->raiseRunTimeException('Missing ethereum configuration section.');
        }
        // Check for username.
        if ($this->configHasNo($config, 'host')) {
            $this->raiseRunTimeException('Missing ethereum configuration: "host".');
        }
        // check the password
        if ($this->configHasNo($config, 'port')) {
            $this->raiseRunTimeException('Missing ethereum configuration: "port".');
        }


        return new Ethereum($config->get('host'), $config->get('port'));

    }

    /**
     * Checks if has global ethereum configuration section.
     *
     * @return bool
     */
    protected function hasConfigSection($config)
    {
        return $config->count() > 0;
    }

    /**
     * Checks if Nexmo config does not
     * have a value for the given key.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function configHasNo($config, $key)
    {
        return ! $this->configHas($config, $key);
    }

    /**
     * Checks if ethereum config has value for the
     * given key.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function configHas($config, $key)
    {
        return
            $config->has($key) &&
            ! is_null($config->get($key)) &&
            ! empty($config->get($key));
    }

    /**
     * Raises Runtime exception.
     *
     * @param string $message
     *
     * @throws \RuntimeException
     */
    protected function raiseRunTimeException($message)
    {
        throw new \RuntimeException($message);
    }
}