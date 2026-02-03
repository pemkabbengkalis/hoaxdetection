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
            Stat::make('Total URL', $result->count())
                ->description('Jumlah url terdaftar')
                ->color('white')
                ->extraAttributes([
                    'style' => '
            --fi-stats-card-color: #ffffff;       
            background-color:rgba(66, 133, 244);
            color:white;
            box-shadow:0 3px 8px rgba(176,196,222);
            border-radius:12px;',
                    'class' => '!bg-blue-600 !text-danger',


                ]),
            Stat::make('Valid', $result->where('status', 'validated')->count())
                ->description('Jumlah berita hoaxs terverifikasi')
                ->color('white')
                ->extraAttributes([
                    'style' => '
            --fi-stats-card-color: #ffffff;       
            background-color:rgba(52, 168, 83);
            color:white;
            box-shadow:0 3px 8px rgba(176,196,222);
            border-radius:12px;',
                    'class' => '!bg-blue-600 !text-danger',

                ]),
            Stat::make('Tidak Valid', $result->where('status', 'unvalidated')->count())
                ->description('Jumlah tidak valid ')
                ->color('white')
                ->extraAttributes([
                    'style' => '
            --fi-stats-card-color: #ffffff;       
            background-color:rgba(234, 67, 53);
            color:white;
            box-shadow:0 3px 8px rgba(176,196,222);
            border-radius:12px;',
                    'class' => '!bg-blue-600 !text-danger',

                ]),
        ];
    }
}
