<?php

namespace App\Filament\Resources\DomainResource\Widgets;

use App\Models\Domain;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
// use Symfony\Component\HttpFoundation\File\Exception\ExtensionFileException;

class DomaiOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(' Domains', Domain::count())
                ->icon('heroicon-o-globe-alt')
                ->description('Jumlah keseluruhan domain yang terdaftar')
                ->color('info'),

            Stat::make(
                ' Extensions',
                Domain::distinct()->count('extension')

            )
                ->icon('heroicon-o-square-3-stack-3d')
                ->description('Jumlah ekstensi domain terdaftar ')
                ->color('info'),
        ];
    }
}
