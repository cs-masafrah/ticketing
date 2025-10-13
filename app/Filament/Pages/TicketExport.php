<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TicketExport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $slug = 'ticket-export';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.ticket-export';

    protected static function getNavigationGroup(): ?string
    {
        return __('Tickets');
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Card::make()->schema([
                Grid::make()
                    ->columns(2)
                    ->schema([

                        DatePicker::make('start_date')
                            ->required()
                            ->reactive()
                            ->label('Star date'),
                        DatePicker::make('end_date')
                            ->required()
                            ->reactive()
                            ->label('End date'),
                        Select::make('user_id')
                            ->label('User')
                            ->reactive()
                            ->options(User::all()->pluck('name', 'id')) // get users dynamically
                            ->searchable(),
                    ])
            ])
        ];
    }

    public function create(): BinaryFileResponse
    {
        $data = $this->form->getState();

        return Excel::download(
            new \App\Exports\TicketExport($data),
            'time_' . time() . '.xlsx', // <-- change extension to xlsx
            \Maatwebsite\Excel\Excel::XLSX, // <-- change type to XLSX
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'] // optional
        );
    }
}
