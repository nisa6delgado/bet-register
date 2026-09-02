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
            'md' => 1,
            'xl' => 20,
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

        if ($total && $winning) {
            $rate = $winning / $total * 100;
            $rate = number_format($rate, 2);
            $rate = $rate . '%';
            
        } else {
            $rate = '0%';
        }

        $average = $bets->avg('odds');
        $average = number_format($average, 2);

        return [
            Stat::make('Unidades apostadas', $wagared)
                ->columnSpan(5),

            Stat::make('Unidades ganadas', $earned)
                ->columnSpan(5),

            Stat::make('Unidades perdidas', $losses)  
                ->columnSpan(5),

            Stat::make('Balance', $balance)
                ->columnSpan(5),

            Stat::make('Apuestas realizadas', $total) 
                ->columnSpan(4),

            Stat::make('Apuestas ganadas', $winning)  
                ->columnSpan(4),

            Stat::make('Apuestas perdidas', $lost)
                ->columnSpan(4),

            Stat::make('Porcentaje de acierto', $rate)
                ->columnSpan(4),

            Stat::make('Cuota promedio', $average)
                ->columnSpan(4),
        ];
    }
}
