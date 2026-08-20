<?php

namespace App\Filament\Widgets;

use App\Models\Bet;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use stdClass;

class BetsTable extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Bet::query())
            ->heading('Apuestas')
            ->columns([
                TextColumn::make('#')->state(
                    static function (HasTable $livewire, stdClass $rowLoop): string {
                        return (string) (
                            $rowLoop->iteration + ($livewire->getTableRecordsPerPage() * ($livewire->getTablePage() - 1))
                        );
                    }
                ),

                TextColumn::make('created_at')
                    ->label('Fecha y hora')
                    ->datetime('d/m/Y h:i A')
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Descripción')
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => nl2br(e($state)))
                    ->html(),

                TextColumn::make('amount')
                    ->label('Apostado')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return number_format($state, 2);
                    }),

                TextColumn::make('odds')
                    ->label('Cuota')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('revenue')
                    ->label('Ganancia')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        if ($record->result == 'Ganado') {
                            return number_format($record->amount * $record->odds, 2);
                        }

                        return 0.00;
                    }),

                BadgeColumn::make('result')
                    ->label('Resultado')
                    ->colors([
                        'success' => 'Ganado',
                        'danger' => 'Perdido',
                        'info' => 'Nulo',
                        'primary' => 'Abierto',
                    ]),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Editar')
                    ->button()
                    ->icon('heroicon-o-pencil-square')
                    ->modalWidth(Width::Medium)
                    ->modalSubmitActionLabel('Guardar cambios')
                    ->fillForm(fn ($record): array => [
                        'description' => $record->description,
                        'amount' => $record->amount,
                        'odds' => $record->odds,
                        'result' => $record->result,
                    ])
                    ->form([
                        Textarea::make('description')
                            ->label('Descripción')
                            ->required(),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('amount')
                                    ->label('Monto')
                                    ->required(),

                                TextInput::make('odds')
                                    ->label('Cuota')
                                    ->required(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('result')
                                    ->label('Resultado')
                                    ->options([
                                        'Abierto' => 'Abierto',
                                        'Ganado' => 'Ganado',
                                        'Perdido' => 'Perdido',
                                    ])
                            ])
                    ])
                    ->action(function ($record, array $data): void {
                        $record->update($data);

                        Notification::make()
                            ->title('Apuesta editada exitosamente')
                            ->success()
                            ->send();

                        redirect('/');
                    }),

                Action::make('delete')
                    ->label('Eliminar')
                    ->button()
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->delete();

                        Notification::make()
                            ->title('Apuesta eliminada exitosamente')
                            ->success()
                            ->send();

                        redirect('/');
                    }),
            ])
            ->defaultSort('id', 'desc');
    }
}
