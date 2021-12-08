<?php

declare(strict_types=1);

namespace Forkrefactor\Apixception\Core;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;


class ApixceptionDispatcher implements EventSubscriberInterface
{
    private array $subscribers;

    public function __construct()
    {
        $this->subscribers = [];
    }

    public function add(string $exception, int $httpCode, string $transformerClass): void
    {
        $this->guardIfIsClassOrInterface($exception);
        $this->guardIfClassExists($transformerClass);
        $this->subscribers[] = new ApixceptionSubscriber(
            $exception,
            $httpCode,
            (new \ReflectionClass($transformerClass))->newInstanceWithoutConstructor(),
        );
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        foreach ($this->subscribers as $subscriber) {
            if (false === \is_a($exception, $subscriber->exception(), true)) {
                continue;
            }

            $event->allowCustomResponseCode();
            $event->setResponse(
                new JsonResponse(
                    $subscriber->transform($exception),
                    $subscriber->httpCode(),
                ),
            );

            return;
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException'],
        ];
    }

    private function guardIfIsClassOrInterface(string $class): void
    {
        if (false === $this->classExists($class) && false === interface_exists($class)) {
            throw new \InvalidArgumentException(
                \sprintf('%s should be a class or an interface', $class),
            );
        }
    }

    private function guardIfClassExists(string $class): void
    {
        if (false === $this->classExists($class)) {
            throw new \InvalidArgumentException(
                \sprintf('%s should be a class', $class),
            );
        }
    }

    private function classExists(string $class): bool
    {
        return \class_exists($class);
    }
}
