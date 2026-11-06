<?php

declare(strict_types=1);

namespace Modules\Labeling\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Models\Permission;
use Modules\User\Models\Role;

/**
 * Permessi usati dalle policy di Labeling (`{modello}.{abilita}`, guard `web`) e ruolo del pannello
 * `labeling::admin` (il nome coincide con l'id del pannello, `BaseUser::canAccessPanel`) con tutti i
 * permessi del modulo. Idempotente: si puo' rieseguire. Senza questi permessi `hasPermissionTo()` lancia
 * `PermissionDoesNotExist` e ogni utente non super-admin riceve un errore 500 (audit di sicurezza M4).
 */
class LabelingPermissionSeeder extends Seeder
{
    public const string GUARD = 'web';

    public const string ADMIN_ROLE = 'labeling::admin';

    /** Allineato alle chiamate `hasPermissionTo()` di app/Models/Policies (test LabelingPermissionSeederTest). */
    public const array PERMISSIONS = [
        'label_template.viewAny', 'label_template.view', 'label_template.create', 'label_template.update', 'label_template.delete',
        'printer.viewAny', 'printer.view', 'printer.create', 'printer.update', 'printer.delete',
        'identifier.viewAny', 'identifier.view', 'identifier.create', 'identifier.update', 'identifier.delete',
        'label_print.viewAny', 'label_print.view', 'label_print.create', 'label_print.delete',
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $name) {
            Permission::findOrCreate($name, self::GUARD);
        }

        Role::findOrCreate(self::ADMIN_ROLE, self::GUARD)->givePermissionTo(self::PERMISSIONS);
    }
}
