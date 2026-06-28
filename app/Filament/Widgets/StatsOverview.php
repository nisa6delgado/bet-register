<?php

namespace App\Filament\Widgets;

use App\Models\Bet;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $wagared = 0;

        $bets = Bet::get();

        foreach ($bets as $bet) {
            $wagared += $bet->amount;
        }

        $wagared = number_format($wagared, 2, ',', '.');

        return [
            Stat::make('Dinero apostado', $wagared),
        ];
    }
}
