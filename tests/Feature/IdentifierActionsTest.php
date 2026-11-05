<?php

declare(strict_types=1);

use Modules\Labeling\Actions\AssignIdentifierAction;
use Modules\Labeling\Actions\ResolveIdentifiableAction;
use Modules\Labeling\Actions\RevokeIdentifierAction;
use Modules\Labeling\Enums\IdentifierTypeEnum;
use Modules\Labeling\Exceptions\IdentifierAlreadyAssignedException;
use Modules\Labeling\Models\Identifier;
use Modules\Labeling\Tests\Fixtures\LabelableItem;
use Modules\Labeling\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

describe('AssignIdentifierAction', function (): void {
    test('uses the label code as QR value when none is given', function (): void {
        $item = LabelableItem::createWith('SU-T-0001');

        $identifier = app(AssignIdentifierAction::class)->execute($item, IdentifierTypeEnum::QR);

        Assert::assertSame('SU-T-0001', $identifier->value);
        Assert::assertSame('qr:SU-T-0001', $identifier->active_value);
        Assert::assertTrue($identifier->is_active);
        Assert::assertSame($identifier->id, $item->activeIdentifier(IdentifierTypeEnum::QR)?->id);
    });

    test('revokes the previous active identifier of the same type', function (): void {
        $item = LabelableItem::createWith('SU-T-0002');
        $first = app(AssignIdentifierAction::class)->execute($item, IdentifierTypeEnum::RFID_UHF, 'e2801160600002aa');
        $second = app(AssignIdentifierAction::class)->execute($item, IdentifierTypeEnum::RFID_UHF, 'E2801160600002BB');

        $first->refresh();
        Assert::assertFalse($first->is_active);
        Assert::assertNull($first->active_value);
        Assert::assertNotNull($first->revoked_at);
        Assert::assertSame('E2801160600002BB', $second->value);
        Assert::assertSame(1, $item->identifiers()->where('is_active', true)->count());
    });

    test('stores RFID values as compact uppercase hex', function (): void {
        $identifier = app(AssignIdentifierAction::class)->execute(LabelableItem::createWith('SU-T-0003'), IdentifierTypeEnum::NFC, '04:a2:1b:3c:4d:5e:80');

        Assert::assertSame('04A21B3C4D5E80', $identifier->value);
    });

    test('is idempotent for the same model and value', function (): void {
        $item = LabelableItem::createWith('SU-T-0004');
        $first = app(AssignIdentifierAction::class)->execute($item, IdentifierTypeEnum::QR);
        $again = app(AssignIdentifierAction::class)->execute($item, IdentifierTypeEnum::QR);

        Assert::assertSame($first->id, $again->id);
    });

    test('refuses a value already active on another model', function (): void {
        app(AssignIdentifierAction::class)->execute(LabelableItem::createWith('SU-T-0005'), IdentifierTypeEnum::QR, 'SHARED-1');

        app(AssignIdentifierAction::class)->execute(LabelableItem::createWith('SU-T-0006'), IdentifierTypeEnum::QR, 'SHARED-1');
    })->throws(IdentifierAlreadyAssignedException::class);

    test('allows reusing a revoked value', function (): void {
        $old = app(AssignIdentifierAction::class)->execute(LabelableItem::createWith('SU-T-0007'), IdentifierTypeEnum::QR, 'REUSED-1');
        app(RevokeIdentifierAction::class)->execute($old);

        $new = app(AssignIdentifierAction::class)->execute(LabelableItem::createWith('SU-T-0008'), IdentifierTypeEnum::QR, 'REUSED-1');

        Assert::assertTrue($new->is_active);
        Assert::assertSame(2, Identifier::query()->where('value', 'REUSED-1')->count());
    });

    test('requires the read value for RFID tags', function (): void {
        app(AssignIdentifierAction::class)->execute(LabelableItem::createWith('SU-T-0009'), IdentifierTypeEnum::RFID_UHF);
    })->throws(InvalidArgumentException::class);
});

describe('ResolveIdentifiableAction', function (): void {
    test('resolves the model from the raw code, the QR url and a wedge reading', function (string $scanned): void {
        $item = LabelableItem::createWith('SU-R-0001');
        app(AssignIdentifierAction::class)->execute($item, IdentifierTypeEnum::QR);

        $resolved = app(ResolveIdentifiableAction::class)->execute($scanned);

        Assert::assertInstanceOf(LabelableItem::class, $resolved);
        Assert::assertSame($item->id, $resolved->getKey());
    })->with([
        'code' => ['SU-R-0001'],
        'url' => ['https://roosko.example.it/l/SU-R-0001'],
        'wedge' => ["SU-R-0001\r\n"],
    ]);

    test('resolves RFID tags regardless of case and separators', function (): void {
        $item = LabelableItem::createWith('SU-R-0002');
        app(AssignIdentifierAction::class)->execute($item, IdentifierTypeEnum::RFID_UHF, 'E28011606000020A1B2C3D4E');

        $resolved = app(ResolveIdentifiableAction::class)->execute('e2-80-11-60-60-00-02-0a-1b-2c-3d-4e');

        Assert::assertSame($item->id, $resolved?->getKey());
    });

    test('ignores revoked identifiers and unknown codes', function (): void {
        $identifier = app(AssignIdentifierAction::class)->execute(LabelableItem::createWith('SU-R-0003'), IdentifierTypeEnum::QR);
        app(RevokeIdentifierAction::class)->execute($identifier);

        Assert::assertNull(app(ResolveIdentifiableAction::class)->execute('SU-R-0003'));
        Assert::assertNull(app(ResolveIdentifiableAction::class)->execute('NOPE-404'));
        Assert::assertNull(app(ResolveIdentifiableAction::class)->execute('   '));
    });
});
