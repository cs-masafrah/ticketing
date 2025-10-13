<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Ticket;
use App\Models\Project;
use App\Models\TicketHour;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

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
            // 'content'
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

        $hours = Ticket::query()
            ->where('owner_id', $this->params['user_id'] ?? auth()->user()->id)
            ->when(
                !empty($this->params['project_id']),
                fn($query) =>
                $query->where('project_id', $this->params['project_id'])
            )
            ->whereBetween('created_at', [
                Carbon::parse($this->params['start_date'])->startOfDay(),
                Carbon::parse($this->params['end_date'])->endOfDay(),
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
