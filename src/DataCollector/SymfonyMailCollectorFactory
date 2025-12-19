<?php

declare(strict_types=1);

namespace Mezzio\DebugBar\DataCollector;

use DebugBar\Bridge\Symfony\SymfonyMailCollector;
use Psr\Container\ContainerInterface;
use Symfony\Component\Mailer\Event\SentMessageEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class SymfonyMailCollectorFactory
{
    public function __invoke(ContainerInterface $container): SymfonyMailCollector
    {
        $collector = new SymfonyMailCollector();

        // Get Symfony EventDispatcher
        $dispatcher = $container->get(EventDispatcherInterface::class);

        // Register listener for sent mail events
        $dispatcher->addListener(
            SentMessageEvent::class,
            static function (SentMessageEvent $event) use ($collector): void {
                $collector->addSymfonyMessage($event->getMessage());
            }
        );

        return $collector;
    }
}
