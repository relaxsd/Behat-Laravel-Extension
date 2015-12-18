<?php

namespace Laracasts\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Laracasts\Behat\ServiceContainer\LumenBooter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class KernelAwareInitializer implements EventSubscriberInterface, ContextInitializer
{

    /**
     * The app kernel.
     *
     * @var HttpKernelInterface
     */
    private $kernel;

    /**
     * The Behat context.
     *
     * @var Context
     */
    private $context;

    /**
     * Construct the initializer.
     *
     * @param HttpKernelInterface $kernel
     */
    public function __construct(HttpKernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ScenarioTested::AFTER => ['rebootKernel', - 15]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function initializeContext(Context $context)
    {
        $this->context = $context;

        $this->setAppOnContext($this->kernel);
    }

    /**
     * Set the app kernel to the feature context.
     */
    private function setAppOnContext()
    {
        if ($this->context instanceof KernelAwareContext) {
            $this->context->setApp($this->kernel);
        }
    }

    /**
     * After each scenario, reboot the kernel.
     */
    public function rebootKernel()
    {
        $this->kernel->flush();

        // The Lumen application has no environmentFile() method like L5.
        // Instead, get it from the global variable.
        // Note that for '.env.behat' to work, you need to modify your app.php file,
        // else '.env' will be used. See LumenBooter.

        global $dotEnv_filename;
        $environmentFile = $dotEnv_filename ?: '.env.behat';

        $lumen = new LumenBooter($this->kernel->basePath(), $environmentFile);

        $this->context->getSession('lumen')->getDriver()->reboot($this->kernel = $lumen->boot());

        $this->setAppOnContext();
    }

}