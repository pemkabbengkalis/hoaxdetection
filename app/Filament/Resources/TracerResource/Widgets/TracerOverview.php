<?php

namespace App\Filament\Resources\TracerResource\Widgets;

use App\Models\Tracer;
use Doctrine\DBAL\Types\Type;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TracerOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(' Tracer', Tracer::count())
                ->icon('heroicon-o-globe-alt')
                ->description('Jumlah keseluruhan tracer terdaftar')
                ->color('white')
                ->extraAttributes([
                    'style' => '
            --fi-stats-card-color: #ffffff;       
            background-color:rgba(66, 133, 244);
            color:white;
            box-shadow:0 8px 15px rgba(176,196,222);
            border-radius:12px;',
                    'class' => '[&_.fi-wi-stats-overview-stat-label]:text-white',
                ]),

            Stat::make(' Nama / Domain', Tracer::distinct()->count('name'))
                ->icon('heroicon-o-square-3-stack-3d')
                ->description('Jumlah keseluruhan nama/domain terdaftar ')
                ->color('white')
                ->extraAttributes([
                    'style' => '
            background-color:rgba(52, 168, 83);
            color:white;
            box-shadow:0 8px 15px rgba(176,196,222);
            border-radius:12px;',
                ]),

            Stat::make(' Type Platform', Tracer::distinct()->count('type_platform'))
                ->icon('heroicon-o-squares-plus')
                ->description('Jumlah keseluruhan type platform terdaftar ')
                ->color('white')
                ->extraAttributes([
                    'style' => '
            background-color:rgba(234, 67, 53);
            color:white;
            box-shadow:0 8px 15px rgba(176,196,222);
            border-radius:12px;',
                ]),
        ];
    }
}
