<?php

namespace Pamil\CartCommand\Infrastructure;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

final class CartCommandKernel extends Kernel
{
    use MicroKernelTrait;

    private const VAR_DIR = __DIR__ . '/../../var';
    private const CONFIG_DIR = __DIR__ . '/Resources/config';

    /** {@inheritdoc} */
    public function getCacheDir(): string
    {
        return self::VAR_DIR . '/cache/' . $this->environment;
    }

    /** {@inheritdoc} */
    public function getLogDir(): string
    {
        return self::VAR_DIR . '/logs';
    }

    /** {@inheritdoc} */
    public function registerBundles(): iterable
    {
        /** @var array $contents */
        $contents = require self::CONFIG_DIR . '/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    /** {@inheritdoc} */
    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $loader->load(self::CONFIG_DIR . '/packages/*.yaml', 'glob');
        if (is_dir(self::CONFIG_DIR . '/packages/' . $this->environment)) {
            $loader->load(self::CONFIG_DIR . '/packages/'.$this->environment.'/**/*.yaml', 'glob');
        }

        $loader->load(self::CONFIG_DIR . '/container.xml');
    }

    /** {@inheritdoc} */
    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $routes->import(self::CONFIG_DIR . '/routing.xml');
    }
}
