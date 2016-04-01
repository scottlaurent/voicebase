<?php

namespace Scottlaurent\Voicebase\ServiceProviders;

use Illuminate\Support\ServiceProvider;
use Scottlaurent\Voicebase\Voicebase;

class LaravelServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}
	
	/**
	 * Register the application services.
	 * @return Voicebase
	 * @throws \Exception
	 */
	public function register()
	{
		if (!$parameters = config('voicebase'))
		{
			throw new \Exception('Please add a voicebase.php config file to your config folder.');
		}
		$this->app->bind('voicebase', function () use ($parameters) {
			return new Voicebase($parameters);
		});
	}
	
	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['voicebase'];
	}
}