<?php

namespace App;

use App\DependencyInjection\CompilerPass\DoctrinePass;
use App\DependencyInjection\CompilerPass\OAuthPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new OAuthPass());
        $container->addCompilerPass(new DoctrinePass());
    }
}
