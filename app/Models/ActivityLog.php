<?php

namespace App\Models;

use Spatie\Activitylog\Models\Activity as SpatieActivity;
use Illuminate\Support\Facades\Schema;

class ActivityLog extends SpatieActivity
{
    public function getTable()
    {
        if (! empty($this->table)) {
            return $this->table;
        }

        $configured = config('activitylog.table_name');
        if ($configured && Schema::hasTable($configured)) {
            return $this->table = $configured;
        }

        if (Schema::hasTable('activity_log')) {
            return $this->table = 'activity_log';
        }

        if (Schema::hasTable('log_tbl')) {
            return $this->table = 'log_tbl';
        }

        if (Schema::hasTable('activity_logs')) {
            return $this->table = 'activity_logs';
        }

        return $this->table = ($configured ?: 'activity_log');
    }

    public function getCauserNameAttribute(): ?string
    {
        try {
            if ($this->causer && isset($this->causer->name)) {
                return $this->causer->name;
            }
        } catch (\Throwable $e) {
            // Causer relation failed or class missing
        }

        if ($this->causer_id) {
            try {
                $user = User::find($this->causer_id);
                if ($user) {
                    return $user->name;
                }
            } catch (\Throwable $e) {
                // User query failed
            }
        }

        return null;
    }
}
