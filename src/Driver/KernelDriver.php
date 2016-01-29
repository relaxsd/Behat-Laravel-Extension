<?php

namespace Laracasts\Behat\Driver;

use Behat\Mink\Driver\BrowserKitDriver;
use Laracasts\Behat\Context\HttpKernelProxy;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class KernelDriver extends BrowserKitDriver
{

    /**
     * Create a new KernelDriver.
     *
     * @param HttpKernelInterface $app
     * @param string|null         $baseUrl
     */
    public function __construct($app, $baseUrl = null)
    {
        // Fix for Lumen 5.2 (
        if (! ($app instanceof HttpKernelInterface)) {
            $app = new HttpKernelProxy($app);
        }
        
        parent::__construct(new Client($app), $baseUrl);
    }

    /**
     * Refresh the driver.
     *
     * @param HttpKernelInterface $app
     * @return KernelDriver
     */
    public function reboot($app)
    {
        return $this->__construct($app);
    }

}
