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
            //Stat::make(' Tracer', Tracer::count())

            Stat::make('Tracer', cache()->remember('tracer_count', 60, fn() => Tracer::count()))
                ->icon('heroicon-o-globe-alt')
                ->description('Jumlah keseluruhan tracer terdaftar')
                ->color('info'),

            //Stat::make(' Nama / Domain', Tracer::distinct()->count('name'))
            Stat::make('Tracer', cache()->remember('tracer_count', 60, fn() => Tracer::count('name')))
                ->icon('heroicon-o-square-3-stack-3d')
                ->description('Jumlah keseluruhan nama/domain terdaftar ')
                ->color('info'),

            // Stat::make(' Type Platform', Tracer::distinct()->count('type_platform'))
            Stat::make('Tracer', cache()->remember('tracer_count', 60, fn() => Tracer::count('type_platform')))
                ->icon('heroicon-o-squares-plus')
                ->description('Jumlah keseluruhan type platform terdaftar ')
                ->color('info'),
        ];
    }
}
