<?php

declare(strict_types=1);

namespace Forkrefactor\Apixception\Core\Transformer;

abstract class ExceptionTransformer
{
    abstract public function transform(\Throwable $exception): array;

    final public function __construct()
    {
        // Disabled constructor
    }
}
