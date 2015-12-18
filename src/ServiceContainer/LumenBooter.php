<?php

namespace Laracasts\Behat\ServiceContainer;

use RuntimeException;

class LumenBooter {

	/**
	 * The base path for the application.
	 *
	 * @var string
	 */
	private $basePath;

	/**
	 * The application's environment file.
	 *
	 * @var string
	 */
	private $environmentFile;

	/**
	 * Create a new Lumen booter instance.
	 *
	 * @param        $basePath
	 * @param string $environmentFile
	 */
	public function __construct($basePath, $environmentFile = '.env.behat')
	{
		$this->basePath        = $basePath;
		$this->environmentFile = $environmentFile;
	}

	/**
	 * Get the application's base path.
	 *
	 * @return mixed
	 */
	public function basePath()
	{
		return $this->basePath;
	}

	/**
	 * Get the application's environment file.
	 *
	 * @return string
	 */
	public function environmentFile()
	{
		return $this->environmentFile;
	}

	/**
	 * Boot the app.
	 *
	 * @return mixed
	 */
	public function boot()
	{
		$bootstrapPath = $this->basePath().'/bootstrap/app.php';

		$this->assertBootstrapFileExists($bootstrapPath);

		// In Lumen, there's no way to first create the application and then tell it 
		// to use another .env file to load while booting. It will load '.env' immediately upon creation.
		//
		// Since we cannot inject configuration items yet (no application), we'll just
		// use a global variable to store the name (idea from arisro/behat-lumen-extension).
		//
		// In order to load '.env.behat', change the Dotenv loading in /bootstrap/app.php to something like:
		//     global $dotEnv_filename;
		//     Dotenv::load(__DIR__.'/../', $dotEnv_filename ?: '.env');
		
		global $dotEnv_filename;
		$dotEnv_filename = $this->environmentFile();

		$app = require $bootstrapPath;

        // Bootstrap the (console) kernel by requesting all commands
		// (this forces the creation of an Artisan instance)
		$app->make('Illuminate\Contracts\Console\Kernel')->all();

		return $app;
	}

	/**
	 * Ensure that the provided Laravel bootstrap path exists.
	 *
	 * @param string $bootstrapPath
	 * @throws RuntimeException
	 */
	private function assertBootstrapFileExists($bootstrapPath)
	{
		if ( ! file_exists($bootstrapPath)) {
			throw new RuntimeException('Could not locate the path to the Laravel bootstrap file.');
		}
	}

}