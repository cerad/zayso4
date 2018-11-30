<?php

namespace App;

use App\Core\ActionInterface;
use App\Core\TransformerLocator;
use App\Project\ProjectLocator;
use App\Project\ProjectInterface;
use App\Project\ProjectTemplateInterface;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel implements CompilerPassInterface
{
    use MicroKernelTrait;

    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function getCacheDir()
    {
        return $this->getProjectDir().'/var/cache/'.$this->environment;
    }

    public function getLogDir()
    {
        return $this->getProjectDir().'/var/log';
    }

    public function registerBundles()
    {
        /** @noinspection PhpIncludeInspection */
        $contents = require $this->getProjectDir().'/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $container->addResource(new FileResource($this->getProjectDir().'/config/bundles.php'));
        // Feel free to remove the "container.autowiring.strict_mode" parameter
        // if you are using symfony/dependency-injection 4.0+ as it's the default behavior
        $container->setParameter('container.autowiring.strict_mode', true);
        $container->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir().'/config';

        $loader->load($confDir.'/{packages}/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{packages}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}_'.$this->environment.self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $confDir = $this->getProjectDir().'/config';

        $routes->import($confDir.'/{routes}/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}'.self::CONFIG_EXTS, '/', 'glob');
    }
    protected function build(ContainerBuilder $container)
    {
        $container->registerForAutoconfiguration(ActionInterface::class)
            ->addTag('controller.service_arguments');
        $container->registerForAutoconfiguration(ProjectInterface::class)
            ->addTag('project.base');
        $container->registerForAutoconfiguration(ProjectTemplateInterface::class)
            ->addTag('project.template');
        $container->registerForAutoconfiguration(DataTransformerInterface::class)
            ->addTag('project.transformer');
    }
    public function process(ContainerBuilder $container)
    {
        // Trial and error a wonderful thing
        $projectLocator = $container->getDefinition(ProjectLocator::class);
        $projectLocatorIds = [];
        foreach ($container->findTaggedServiceIds('project.base') as $id => $tags) {
            $projectLocatorIds[$id] = new Reference($id);
        }
        foreach ($container->findTaggedServiceIds('project.template') as $id => $tags) {
            $projectLocatorIds[$id] = new Reference($id);
        }
        $projectLocator->setArguments([$projectLocatorIds]);

        $transformerLocatorIds = [];
        foreach ($container->findTaggedServiceIds('project.transformer') as $id => $tags) {
            $transformerLocatorIds[$id] = new Reference($id);
        }
        $transformerLocator = $container->getDefinition(TransformerLocator::class);
        $transformerLocator->setArguments([$transformerLocatorIds]);
        //dump(array_keys($transformerLocatorIds));
    }
}
