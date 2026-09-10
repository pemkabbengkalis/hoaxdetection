<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = config('activitylog.database_connection', config('database.default'));
        $tableName = config('activitylog.table_name', 'activity_log');

        Schema::connection($connection)->table($tableName, function (Blueprint $table) use ($connection, $tableName) {
            if (!Schema::connection($connection)->hasColumn($tableName, 'event')) {
                $table->string('event')->nullable()->after('subject_type');
            }
            if (!Schema::connection($connection)->hasColumn($tableName, 'batch_uuid')) {
                $table->uuid('batch_uuid')->nullable()->after('properties');
            }
        });
    }

    public function down(): void
    {
        $connection = config('activitylog.database_connection', config('database.default'));
        $tableName = config('activitylog.table_name', 'activity_log');

        Schema::connection($connection)->table($tableName, function (Blueprint $table) use ($connection, $tableName) {
            if (Schema::connection($connection)->hasColumn($tableName, 'batch_uuid')) {
                $table->dropColumn('batch_uuid');
            }
            if (Schema::connection($connection)->hasColumn($tableName, 'event')) {
                $table->dropColumn('event');
            }
        });
    }
};
