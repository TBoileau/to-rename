<?php

declare(strict_types=1);

namespace App\DependencyInjection\CompilerPass;

use App\OAuth\ClientInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OAuthPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $taggedServiceIds = $container->findTaggedServiceIds('app.oauth.client');

        /**
         * @var class-string<ClientInterface> $id
         */
        foreach ($taggedServiceIds as $id => $tags) {
            $container->registerAliasForArgument($id, ClientInterface::class, sprintf('%s.client', $id::getName()));
        }
    }
}
