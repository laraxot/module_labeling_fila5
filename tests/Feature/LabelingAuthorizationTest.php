<?php

declare(strict_types=1);

use Filament\Actions\Action;
use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Modules\Labeling\Database\Seeders\LabelingPermissionSeeder;
use Modules\Labeling\Filament\Actions\PrintLabelHeaderAction;
use Modules\Labeling\Filament\Actions\PrintLabelTableAction;
use Modules\Labeling\Filament\Resources\IdentifierResource\Tables\IdentifiersTable;
use Modules\Labeling\Filament\Resources\LabelPrintResource\Tables\LabelPrintsTable;
use Modules\Labeling\Models\Identifier;
use Modules\Labeling\Models\LabelPrint;
use Modules\Labeling\Models\Policies\IdentifierPolicy;
use Modules\Labeling\Models\Policies\LabelPrintPolicy;
use Modules\Labeling\Models\Policies\LabelTemplatePolicy;
use Modules\Labeling\Models\Policies\PrinterPolicy;
use Modules\Labeling\Tests\Fixtures\LabelableItem;
use Modules\Labeling\Tests\TestCase;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Models\Policies\Concerns\DeniesUndeclaredFilamentAbilities;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/**
 * Utente autenticato fittizio: le abilita' consentite le decide un Gate::before del test,
 * cosi' il test non dipende da tabelle e permessi del modulo User.
 *
 * @param  list<string>  $abilities
 */
function labelingActingWith(array $abilities): void
{
    Auth::setUser(new LabelingAuthorizableUser(['id' => 1]));
    Gate::before(static fn (mixed $_user, string $ability): bool => in_array($ability, $abilities, true));
}

/** GenericUser che implementa Authorizable, come il Gate::before di spatie/permission richiede. */
class LabelingAuthorizableUser extends GenericUser implements AuthorizableContract
{
    use Authorizable;
}

function labelingRevokeAction(): Action
{
    $action = (new IdentifiersTable)->getTableActions()['revoke'];
    Assert::assertInstanceOf(Action::class, $action);

    return $action;
}

/** @return list<class-string<IdentifierPolicy|LabelPrintPolicy|LabelTemplatePolicy|PrinterPolicy>> */
function labelingPolicies(): array
{
    return [IdentifierPolicy::class, LabelPrintPolicy::class, LabelTemplatePolicy::class, PrinterPolicy::class];
}

describe('Labeling policies', function (): void {
    test('every policy denies the Filament abilities it does not declare', function (): void {
        $user = Mockery::mock(UserContract::class);
        Assert::assertInstanceOf(UserContract::class, $user);

        foreach (labelingPolicies() as $class) {
            Assert::assertContains(DeniesUndeclaredFilamentAbilities::class, class_uses($class), $class);

            $policy = new $class;
            Assert::assertFalse($policy->deleteAny($user), $class);
            Assert::assertFalse($policy->forceDeleteAny($user), $class);
            Assert::assertFalse($policy->reorder($user), $class);
        }
    });

    test('the permission seeder creates every permission checked by the policies', function (): void {
        $used = [];
        foreach (labelingPolicies() as $class) {
            $file = (new ReflectionClass($class))->getFileName();
            Assert::assertIsString($file);
            preg_match_all("/hasPermissionTo\\('([^']+)'\\)/", (string) file_get_contents($file), $matches);
            $used = [...$used, ...$matches[1]];
        }

        $used = array_values(array_unique($used));
        sort($used);
        $seeded = LabelingPermissionSeeder::PERMISSIONS;
        sort($seeded);

        Assert::assertSame($used, $seeded);
    });
});

describe('Labeling actions require an ability', function (): void {
    test('without abilities print, reprint, download and revoke are unauthorized', function (): void {
        labelingActingWith([]);
        $print = new LabelPrint;

        Assert::assertFalse(PrintLabelTableAction::make()->record(new LabelableItem(['code' => 'A']))->isAuthorized());
        Assert::assertFalse(PrintLabelHeaderAction::make()->record(new LabelableItem(['code' => 'A']))->isAuthorized());
        Assert::assertFalse(LabelPrintsTable::reprintAction()->record($print)->isAuthorized());
        Assert::assertFalse(LabelPrintsTable::downloadAction()->record($print)->isAuthorized());
        Assert::assertFalse(labelingRevokeAction()->record(new Identifier)->isAuthorized());
    });

    test('each action is authorized by its own ability', function (): void {
        labelingActingWith(['create', 'view', 'update']);
        $print = new LabelPrint;

        Assert::assertTrue(PrintLabelTableAction::make()->record(new LabelableItem(['code' => 'A']))->isAuthorized());
        Assert::assertTrue(PrintLabelHeaderAction::make()->record(new LabelableItem(['code' => 'A']))->isAuthorized());
        Assert::assertTrue(LabelPrintsTable::reprintAction()->record($print)->isAuthorized());
        Assert::assertTrue(LabelPrintsTable::downloadAction()->record($print)->isAuthorized());
        Assert::assertTrue(labelingRevokeAction()->record(new Identifier)->isAuthorized());
    });

    test('print and reprint are rate limited', function (): void {
        config(['labeling.print_rate_limit' => 7]);

        Assert::assertSame(7, PrintLabelTableAction::make()->getRateLimit());
        Assert::assertSame(7, LabelPrintsTable::reprintAction()->getRateLimit());
    });
});
