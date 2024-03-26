<?php

declare(strict_types=1);

use BEAR\Hello\ImmutableBootstrap;

require dirname(__DIR__) . '/autoload.php';
exit((new ImmutableBootstrap())(PHP_SAPI === 'cli-server' ? 'app' : 'prod-app', $GLOBALS, $_SERVER));
