<?php

declare(strict_types=1);

namespace Forkrefactor\Apixception\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ApixceptionExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config'),
        );
        $loader->load('apixception.yml');
        $config = $this->processConfiguration(new Configuration(), $configs);

        $service = $container->getDefinition('PcComponentes\Apixception\Core\ApixceptionDispatcher');

        foreach ($config as $line) {
            $service->addMethodCall(
                'add',
                [
                    $line['exception'],
                    $line['http_code'],
                    $line['transformer'],
                ],
            );
        }
    }
}
