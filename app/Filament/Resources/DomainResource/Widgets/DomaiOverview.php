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

            Stat::make(
                ' Extensions',
                Domain::distinct()->count('extension')

            )
                ->icon('heroicon-o-square-3-stack-3d')
                ->description('Jumlah ekstensi domain terdaftar ')
                ->color('white')
                ->extraAttributes([
                    'style' => '
            background-color:rgba(52, 168, 83);
            color:white;
            box-shadow:0 3px 8px rgba(176,196,222);
            border-radius:12px;',
                ]),



            Stat::make(
                ' Extensions',
                Domain::distinct()->count('type')

            )
                ->icon('heroicon-o-square-3-stack-3d')
                ->description('Jumlah ekstensi domain terdaftar ')
                ->color('white')
                ->extraAttributes([
                    'style' => '
            background-color:rgba(234, 67, 53);
            color:white;
            box-shadow:0 3px 8px rgba(176,196,222);
            border-radius:12px;',
                ]),



        ];
    }
}
