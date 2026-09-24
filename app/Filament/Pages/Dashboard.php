<?php

namespace App\Filament\Pages;

use App\Models\Bet;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\Width;
use Livewire\Attributes\Url;

class Dashboard extends BaseDashboard
{
    #[Url]
    public ?string $from = null;

    #[Url]
    public ?string $until = null;

    #[Url]
    public ?string $result = 'Todos';

    #[Url]
    public ?string $tags = null;

    protected function getHeaderActions(): array
    {
        $bets = Bet::get();

        $tags = [];

        foreach ($bets as $bet) {
            if ($bet->tags) {
                foreach ($bet->tags as $tag) {
                    $tags[] = $tag;
                }
            }
        }

        $tags = array_unique($tags);
        $tags = array_combine($tags, $tags);
        asort($tags);
        
        $selectedTags = explode(',', $this->tags);

        return [
            Action::make('filter')
                ->label('Filtros')
                ->icon('heroicon-o-funnel')
                ->modalWidth(Width::Large)
                ->modalSubmitActionLabel('Filtrar')
                ->fillForm(fn () => [
                    'from' => $this->from ?? Carbon::now()->startOfMonth()->format('Y-m-d'),
                    'until' => $this->until ?? Carbon::now()->endOfMonth()->format('Y-m-d'),
                    'result' => $this->result,
                    'tags' => $selectedTags,
                ])
                ->form([
                    Grid::make(2)
                        ->schema([
                            DatePicker::make('from')
                                ->label('Desde')
                                ->date()
                                ->required(),

                            DatePicker::make('until')
                                ->label('Hasta')
                                ->required(),
                        ]),

                    Grid::make(2)
                        ->schema([
                            Select::make('result')
                                ->label('Resultado')
                                ->options([
                                    'Todos' => 'Todos',
                                    'Abierto' => 'Abierto',
                                    'Ganado' => 'Ganado',
                                    'Perdido' => 'Perdido',
                                ]),

                            Select::make('tags')
                                ->label('Etiquetas')
                                ->options($tags)
                                ->multiple(),
                        ]),
                ])
                ->action(function ($data) {
                    $data['tags'] = implode(',', $data['tags']);
                    $params = http_build_query($data);

                    return redirect(
                        '?' . $params
                    );
                }),

            Action::make('create')
                ->label('Registrar apuesta')
                ->icon('heroicon-o-plus')
                ->modalWidth(Width::Large)
                ->modalSubmitActionLabel('Registar apuesta')
                ->form([
                    Textarea::make('description')
                        ->label('Descripción')
                        ->rows(4)
                        ->required(),

                    Grid::make(2)
                        ->schema([
                            TextInput::make('amount')
                                ->label('Monto')
                                ->numeric()
                                ->minValue(0.01)
                                ->required(),

                            TextInput::make('odds')
                                ->label('Cuota')
                                ->numeric()
                                ->minValue(1.01)
                                ->required(),
                        ]),

                    TagsInput::make('tags')
                        ->label('Etiquetas')
                        ->required(),
                ])
                ->action(function ($data) {
                    Bet::create([
                        'description' => $data['description'],
                        'amount' => $data['amount'],
                        'odds' => $data['odds'],
                        'result' => 'Abierto',
                        'tags' => $data['tags'],
                    ]);

                    Notification::make()
                        ->title('Apuesta creada exitosamente')
                        ->success()
                        ->send();

                    return redirect('/');
                }),
        ];
    }
}
