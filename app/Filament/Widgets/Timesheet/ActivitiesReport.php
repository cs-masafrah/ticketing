<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Timesheet;

use App\Models\TicketHour;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\BarChartWidget;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ActivitiesReport extends BarChartWidget
{
    protected int|string|array $columnSpan = [
        'sm' => 1,
        'md' => 6,
        'lg' => 3
    ];

    public ?string $filter = '2025';

    protected function getHeading(): string
    {
        return __('Logged time by activity');
    }

    protected function getFilters(): ?array
    {
        return [
            2024 => 2024,
            2025 => 2025,
            2026 => 2026,
            2027 => 2027,
            2028 => 2028,
            2029 => 2029,
            2030 => 2030,
            2031 => 2031,
            2032 => 2032,
            2033 => 2033,
            2034 => 2034,
            2035 => 2035,
            2036 => 2036,
            2037 => 2037,
            2038 => 2038,
            2039 => 2039,
            2040 => 2040,
            2041 => 2041,
            2042 => 2042,
            2043 => 2043,
            2044 => 2044,
            2045 => 2045,
        ];
    }

    protected function getData(): array
    {
        $collection = $this->filter(auth()->user(), [
            'year' => $this->filter
        ]);

        $datasets = $this->getDatasets($collection);

        return [
            'datasets' => [
                [
                    'label' => __('Total time logged'),
                    'data' => $datasets['sets'],
                    'backgroundColor' => [
                        'rgba(54, 162, 235, .6)'
                    ],
                    'borderColor' => [
                        'rgba(54, 162, 235, .8)'
                    ],
                ],
            ],
            'labels' => $datasets['labels'],
        ];
    }

    protected function getDatasets(Collection $collection): array
    {
        $datasets = [
            'sets' => [],
            'labels' => []
        ];

        foreach ($collection as $item) {
            $datasets['sets'][] = $item->value;
            $datasets['labels'][] = $item->activity?->name ?? __('No activity');
        }

        return $datasets;
    }

    protected function filter(User $user, array $params): Collection
    {
        return TicketHour::with('activity')
            ->select([
                'activity_id',
                DB::raw('SUM(value) as value'),
            ])
            ->whereRaw(
                DB::raw("YEAR(created_at)=" . (is_null($params['year']) ? Carbon::now()->format('Y') : $params['year']))
            )
            ->where('user_id', $user->id)
            ->groupBy('activity_id')
            ->get();
    }
}
