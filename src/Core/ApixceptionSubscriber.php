<?php

declare(strict_types=1);

namespace Forkrefactor\Apixception\Core;

use Forkrefactor\Apixception\Core\Transformer\ExceptionTransformer;

final class ApixceptionSubscriber
{
    private string $exception;
    private int $httpCode;
    private ExceptionTransformer $transformer;

    public function __construct(string $exception, int $httpCode, ExceptionTransformer $transformer)
    {
        $this->exception = $exception;
        $this->httpCode = $httpCode;
        $this->transformer = $transformer;
    }

    public function exception(): string
    {
        return $this->exception;
    }

    public function httpCode(): int
    {
        return $this->httpCode;
    }

    public function transform(\Throwable $exception): array
    {
        return $this->transformer->transform($exception);
    }
}
