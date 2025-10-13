<?php

namespace App\Exports;

use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketHour;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TicketExport implements FromCollection, WithHeadings
{
    protected array $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function headings(): array
    {
        return [
            '#',
            'project',
            'ticket',
            'owner',
            'responsible',
            'status',
            'type',
            'priority',
            'ticket Date',
            'content'
        ];
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        $collection = collect();

        // $hours = TicketHour::where('user_id', auth()->user()->id)
        //     ->whereBetween('created_at', [$this->params['start_date'], $this->params['end_date']])
        //     ->get();

        $hours = Ticket::where('owner_id', $this->params['user_id'] ? $this->params['user_id'] : auth()->user()->id)
            ->whereBetween('created_at', [
                $this->params['start_date'] . ' 00:00:00',
                $this->params['end_date'] . ' 23:59:59',
            ])
            ->get();



        foreach ($hours as $item) {
            $collection->push([
                '#' => $item->code,
                'project' => $item->project->name,
                'ticket' => $item->name,
                'owner' => $item?->owner?->name,
                'responsible' => $item->responsible?->name,
                'status' => $item?->status?->name,
                'type' => $item?->type?->name,
                "priority" => $item?->priority?->name,
                'ticket Date' => $item->created_at->format(__('Y-m-d g:i A')),
                // 'content' => $item->content,
            ]);
        }

        return $collection;
    }
}
