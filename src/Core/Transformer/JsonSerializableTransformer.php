<?php

declare(strict_types=1);

namespace Forkrefactor\Apixception\Core\Transformer;

final class JsonSerializableTransformer extends ExceptionTransformer
{
    public function transform(\Throwable $exception): array
    {
        if (false === $exception instanceof \JsonSerializable) {
            throw new \InvalidArgumentException(
                \sprintf('%s needs to be %s', self::class, \JsonSerializable::class),
            );
        }

        return [
            'message' => $exception->getMessage(),
            'data' => $exception,
        ];
    }
}
