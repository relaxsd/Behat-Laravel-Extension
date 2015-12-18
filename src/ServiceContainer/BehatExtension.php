<?php

namespace Laracasts\Behat\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class BehatExtension implements Extension {

	/**
	 * {@inheritdoc}
	 */
	public function getConfigKey()
	{
		return 'lumen';
	}

	/**
	 * {@inheritdoc}
	 */
	public function initialize(ExtensionManager $extensionManager)
	{
		if (null !== $minkExtension = $extensionManager->getExtension('mink')) {
			$minkExtension->registerDriverFactory(new LumenFactory);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container)
	{
		//
	}

	/**
	 * {@inheritdoc}
	 */
	public function configure(ArrayNodeDefinition $builder)
	{
		$builder
			->children()
				->scalarNode('bootstrap_path')
					->defaultValue('bootstrap/app.php')
				->end()
				->scalarNode('env_path')
					->defaultValue('.env.behat');
	}

	/**
	 * {@inheritdoc}
	 */
	public function load(ContainerBuilder $container, array $config)
	{
		$app = $this->loadLumen($container, $config);

		$this->loadInitializer($container, $app, $config);
	}

	/**
	 * Boot up Lumen.
	 *
	 * @param ContainerBuilder $container
	 * @param array            $config
	 * @return mixed
	 */
	private function loadLumen(ContainerBuilder $container, array $config)
	{
		$lumen = new LumenBooter($container->getParameter('paths.base'), $config['env_path']);

		$container->set('lumen.app', $app = $lumen->boot());

		return $app;
	}

	/**
	 * Load the initializer.
	 *
	 * @param ContainerBuilder    $container
	 * @param HttpKernelInterface $app
	 * @param array               $behatConfig
	 */
	private function loadInitializer(ContainerBuilder $container, $app, array $config)
	{
		$definition = new Definition('Laracasts\Behat\Context\KernelAwareInitializer', [$app, $config]);

		$definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, ['priority' => 0]);
		$definition->addTag(ContextExtension::INITIALIZER_TAG, ['priority' => 0]);

		$container->setDefinition('lumen.initializer', $definition);
	}

}