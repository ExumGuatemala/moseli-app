<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables on the default connection that should support soft deletes.
     *
     * @var array<int, string>
     */
    private array $tables = [
        'users',
        'password_resets',
        'failed_jobs',
        'personal_access_tokens',
        'departamentos',
        'municipios',
        'clients',
        'products',
        'product_colors',
        'product_types',
        'order_states',
        'orders',
        'orders_products',
        'media',
        'payments',
        'permissions',
        'roles',
        'model_has_permissions',
        'model_has_roles',
        'role_has_permissions',
        'branches',
        'institutions',
        'type_product_features',
        'product_features',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            $this->addDeletedAtColumn($tableName);
        }

        $activityLogConnection = config('activitylog.database_connection');
        $activityLogTable = config('activitylog.table_name', 'activity_log');

        if (is_string($activityLogTable) && $activityLogTable !== '') {
            $this->addDeletedAtColumn($activityLogTable, $activityLogConnection);
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            $this->dropDeletedAtColumn($tableName);
        }

        $activityLogConnection = config('activitylog.database_connection');
        $activityLogTable = config('activitylog.table_name', 'activity_log');

        if (is_string($activityLogTable) && $activityLogTable !== '') {
            $this->dropDeletedAtColumn($activityLogTable, $activityLogConnection);
        }
    }

    private function addDeletedAtColumn(string $tableName, ?string $connection = null): void
    {
        $schema = $connection ? Schema::connection($connection) : Schema::getFacadeRoot();

        if (! $schema->hasTable($tableName) || $schema->hasColumn($tableName, 'deleted_at')) {
            return;
        }

        $schema->table($tableName, function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    private function dropDeletedAtColumn(string $tableName, ?string $connection = null): void
    {
        $schema = $connection ? Schema::connection($connection) : Schema::getFacadeRoot();

        if (! $schema->hasTable($tableName) || ! $schema->hasColumn($tableName, 'deleted_at')) {
            return;
        }

        $schema->table($tableName, function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
