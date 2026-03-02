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

            Stat::make('Hoax', $result->where('status', 'validated')->where('category', 'hoax')->count())
                ->description('Jumlah berita hoaxs terverifikasi')
                ->extraAttributes([
                    'style' => '
            color:white;
            box-shadow: 0 -4px 6px -2px rgba(0, 128, 0, 0.4);            
                border-radius:12px;',
                ]),

            Stat::make('Fakta', $result->where('status', 'validated')->where('category', 'fakta')->count())
                ->description('Jumlah berita fakta terverifikasi')
                ->extraAttributes([
                    'style' => '
            color:white;
            box-shadow: 0 -4px 6px -2px rgba(245, 158, 11, 0.4);
                border-radius:12px;',
                ]),

            Stat::make('Data Butuh Verifikasi', $result->where('status', 'unvalidated')->count())
                ->description('Jumlah Data Butuh Verifikasi ')
                ->extraAttributes([
                    'style' => '
                color:white;
            box-shadow: 0 -4px 6px -2px rgba(255, 0, 0, 0.4);
                border-radius:12px;',
                ]),
        ];
    }
}
