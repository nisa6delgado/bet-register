<?php

namespace App\Filament\Widgets;

use App\Models\Bet;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\Url;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    #[Url]
    public ?string $from = null;

    #[Url]
    public ?string $until = null;

    protected function getColumns(): int | array
    {
        return [
            'md' => 2,
            'xl' => 4,
        ];
    }

    protected function getStats(): array
    {
        $from = $this->from ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $until = $this->until ?? Carbon::now()->endOfMonth()->format('Y-m-d');
        
        $wagared = 0;
        $winning = 0;
        $earned = 0;
        $losses = 0;
        $lost = 0;

        $bets = Bet::query()
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $until)
            ->get();

        foreach ($bets as $bet) {
            $wagared += $bet->amount;

            if ($bet->result == 'Ganado') {
                $earned += ($bet->amount * $bet->odds) - $bet->amount;
                $winning += 1;
            }

            if ($bet->result == 'Perdido') {
                $losses += $bet->amount;
                $lost += 1;
            }
        }

        $balance = number_format($earned - $losses, 2);
        $wagared = number_format($wagared, 2);
        $earned = number_format($earned, 2);
        $losses = number_format($losses, 2);

        $total = count($bets);

        $rate = $winning / $total * 100;
        $rate = number_format($rate, 2);
        $rate = $rate . '%';

        return [
            Stat::make('Unidades apostadas', $wagared),
            Stat::make('Unidades ganadas', $earned),
            Stat::make('Unidades perdidas', $losses),
            Stat::make('Balance', $balance),

            Stat::make('Apuestas realizadas', $total),
            Stat::make('Apuestas ganadas', $winning),
            Stat::make('Apuestas perdidas', $lost),
            Stat::make('Porcentaje de acierto', $rate),
        ];
    }
}
