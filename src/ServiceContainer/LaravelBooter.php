<?php

namespace Laracasts\Behat\ServiceContainer;

use RuntimeException;

class LaravelBooter
{

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
     * The application's autoload file (for Laravel 5.2).
     *
     * @var string
     */
    private $autoloadFile;
    
    /**
     * The application's bootstrap file.
     *
     * @var string
     */
    private $bootstrapFile;

    /**
     * Create a new Laravel booter instance.
     *
     * @param        $basePath
     * @param string $environmentFile
     * @param string $autoloadFile
     * @param string $bootstrapFile
     */
    public function __construct($basePath, $environmentFile = '.env.behat', $autoloadFile = 'bootstrap/autoload.php', $bootstrapFile = 'bootstrap/app.php')
    {
        $this->basePath = $basePath;
        $this->environmentFile = $environmentFile;
        $this->autoloadFile = $autoloadFile;
        $this->bootstrapFile = $bootstrapFile;
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
     * Get the applications bootstrap file.
     *
     * @return string
     */
    public function bootstrapFile()
    {
        return ltrim($this->bootstrapFile, '/');
    }

    /**
     * Get the applications autoload file.
     *
     * @return string
     */
    public function autoloadFile()
    {
        return ltrim($this->autoloadFile, '/');
    }

    /**
     * Boot the app.
     *
     * @return mixed
     */
    public function boot()
    {
        // Load the autoload.php file (since Laravel 5.2)
        $autoloadPath = $this->basePath() . '/'. $this->autoloadFile();
        $this->assertBootstrapFileExists($autoloadPath);

        require $autoloadPath;

        $bootstrapPath = $this->basePath() . '/'. $this->bootstrapFile();
        $this->assertBootstrapFileExists($bootstrapPath);

        $app = require_once $bootstrapPath;

        $app->loadEnvironmentFrom($this->environmentFile());

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        $app->make('Illuminate\Http\Request')->capture();

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
            throw new RuntimeException("Could not locate the path to the Laravel bootstrap file '{$bootstrapPath}'.");
        }
    }

}
