<?php

declare(strict_types=1);

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Labeling\Actions\BuildLabelUrlAction;
use Modules\Labeling\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

describe('BuildLabelUrlAction', function (): void {
    test('uses APP_URL and ignores the Host of the current request', function (): void {
        config(['app.url' => 'https://impianto.example/']);
        request()->headers->set('Host', 'attacker.example');
        app('url')->useOrigin('https://attacker.example');

        $url = app(BuildLabelUrlAction::class)->execute('SU 1/2');

        Assert::assertSame('https://impianto.example/l/SU%201%2F2', $url);
    });
});

describe('route labeling.resolve', function (): void {
    test('is throttled', function (): void {
        $route = Route::getRoutes()->getByName('labeling.resolve');

        Assert::assertNotNull($route);
        Assert::assertNotEmpty(array_filter(
            $route->gatherMiddleware(),
            static fn (mixed $middleware): bool => is_string($middleware) && str_starts_with($middleware, 'throttle:'),
        ));
    });

    test('answers 429 after the configured number of requests from the same client', function (): void {
        $limit = max(1, config()->integer('labeling.resolve_rate_limit', 30));

        $kernel = app(Kernel::class);
        $status = static fn (string $code): int => $kernel->handle(Request::create('/l/'.$code))->getStatusCode();

        for ($i = 0; $i < $limit; $i++) {
            Assert::assertSame(404, $status('UNKNOWN-'.$i));
        }

        Assert::assertSame(429, $status('UNKNOWN-LAST'));
    });
});
