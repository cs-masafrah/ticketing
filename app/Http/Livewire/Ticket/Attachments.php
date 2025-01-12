<?php

namespace App\Http\Livewire\Ticket;

use App\Models\Ticket;
use Livewire\Component;
use Filament\Facades\Filament;
use Filament\Tables\Actions\Action;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class Attachments extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public Ticket $ticket;

    protected $listeners = [
        'filesUploaded'
    ];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function render()
    {
        return view('livewire.ticket.attachments');
    }

    protected function getFormModel(): Model|string|null
    {
        return $this->ticket;
    }

    protected function getFormSchema(): array
    {
        return [
            SpatieMediaLibraryFileUpload::make('attachments')
                ->label(__('Attachments'))
                ->hint(__('Important: If a file has the same name, it will be replaced'))
                ->helperText(__('Here you can attach all files needed for this ticket'))
                ->multiple()
                ->disablePreview()
        ];
    }

    public function perform(): void
    {
        $this->form->getState();
        $this->form->fill();
        $this->emit('filesUploaded');
        Filament::notify('success', __('Ticket attachments saved'));
    }

    public function filesUploaded(): void
    {
        $this->ticket->refresh();
    }

    protected function getTableQuery(): Builder
    {
        return $this->ticket->media()->getQuery();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__('Name'))
                ->sortable()
                ->searchable(),

            // TextColumn::make(name: 'human_readable_size')
            //     ->label(__('Size'))
            //     ->sortable()
            //     ->searchable(),

            TextColumn::make('mime_type')
                ->label(__('Mime type'))
                ->sortable()
                ->searchable(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('view')
            ->label(__('View'))
            ->icon('heroicon-o-eye')
            ->url(function ($record) {
                return $record->getUrl();
            })
            ->openUrlInNewTab(),

            Action::make('download')
                ->label(__('Download'))
                ->icon('heroicon-o-download')
                ->action(function ($record) {
                    // Ensure the media object exists and is valid
                    $media = $record; // If $record is directly your media model
                    $filePath = $media->getPath(); // Retrieve the file path

                    // Validate if the file exists on disk
                    if (!file_exists($filePath)) {
                        Filament::notify('danger', __('File not found on the server.'));
                        return;
                    }

                    // Return the file as a download response
                    return response()->download($filePath, $media->file_name);
                }),
            DeleteAction::make()
                ->action(function ($record) {
                    $record->delete();
                    Filament::notify('success', __('Ticket attachment deleted'));
                })
        ];
    }
}
