<?php

declare(strict_types=1);

namespace BEAR\Hello;

use BEAR\Hello\Module\App;
use BEAR\Resource\ResourceObject;
use BEAR\Sunday\Extension\Application\AppInterface;
use BEAR\Sunday\Extension\Router\RouterInterface;
use Throwable;

use function assert;
use function immutable_cache_add;
use function immutable_cache_fetch;

/**
 * @psalm-import-type Globals from RouterInterface
 * @psalm-import-type Server from RouterInterface
 */
final class ImmutableBootstrap
{
    /**
     * @psalm-param Globals $globals
     * @psalm-param Server  $server
     * @phpstan-param array<string, mixed> $globals
     * @phpstan-param array<string, mixed> $server
     *
     * @return 0|1
     */
    public function __invoke(string $context, array $globals, array $server): int
    {
        $app = immutable_cache_fetch($context);
        if (! $app instanceof AppInterface) {
            $app = Injector::getInstance($context)->getInstance(AppInterface::class);
            immutable_cache_add($context, $app);
        }

        assert($app instanceof App);
        if ($app->httpCache->isNotModified($server)) {
            $app->httpCache->transfer();

            return 0;
        }

        $request = $app->router->match($globals, $server);
        try {
            $response = $app->resource->{$request->method}->uri($request->path)($request->query);
            assert($response instanceof ResourceObject);
            $response->transfer($app->responder, $server);

            return 0;
        } catch (Throwable $e) {
            $app->throwableHandler->handle($e, $request)->transfer();

            return 1;
        }
    }
}
