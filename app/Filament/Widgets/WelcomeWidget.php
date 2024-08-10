<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WelcomeWidget extends BaseWidget
{

    protected static ?int $sort = -3;

    protected static bool $isLazy = false;

    /**
     * @var view-string
     */
    protected static string $view = 'filament-panels::widgets.account-widget';

    protected function getStats(): array
    {
        return [
            //
        ];
    }
}
