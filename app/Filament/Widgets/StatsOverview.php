<?php

namespace App\Filament\Widgets;

use App\Models\Result;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Support\Colors\Color;


class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;
    protected function getStats(): array
    {

        $result = Result::query()->whereIn('status', ['validated', 'unvalidated'])->get();
        return [
            Stat::make('Total URL', $result->count())
                ->description('Jumlah url terdaftar')
                ->extraAttributes([
                    'style' => '        
            box-shadow: 0 -4px 6px -2px rgba(0, 0, 255, 0.6);
            border-radius:12px;',
                    'class' => '[&_.fi-wi-stats-overview-stat-label]:text-white',
                ]),

            Stat::make('Valid', $result->where('status', 'validated')->count())
                ->description('Jumlah berita hoaxs terverifikasi')
                ->extraAttributes([
                    'style' => '
            color:white;
            box-shadow: 0 -4px 6px -2px rgba(0, 128, 0, 0.4);            
                border-radius:12px;',
                ]),
            Stat::make('Tidak Valid', $result->where('status', 'unvalidated')->count())
                ->description('Jumlah tidak valid ')
                ->extraAttributes([
                    'style' => '
                color:white;
            box-shadow: 0 -4px 6px -2px rgba(255, 0, 0, 0.4);
                border-radius:12px;',
                ]),
        ];
    }
}
