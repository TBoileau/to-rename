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

    public function boot(): void
    {
        parent::boot();

        date_default_timezone_set($this->getContainer()->getParameter('timezone'));
    }

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new OAuthPass());
        $container->addCompilerPass(new DoctrinePass());
    }
}
