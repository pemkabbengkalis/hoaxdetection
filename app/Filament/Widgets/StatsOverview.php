<?php

namespace App\Filament\Widgets;

use App\Models\Result;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;
    protected function getStats(): array
    {

        $result = Result::query()->whereIn('status', ['validated', 'unvalidated'])->get();
              return [
            Stat::make('Total URL', $result->count()),
            Stat::make('Valid', $result->where('status','validated')->count()),
            Stat::make('Tidak Valid', $result->where('status','unvalidated')->count()),
        ];
    }
}
