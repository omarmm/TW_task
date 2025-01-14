<?php
namespace App\Services;

class SummarizeService
{
    public function summarize(array $dataset, int $chunkSize = 1000): array
    {
        $totalEntries = 0;
        $sum = 0;

        foreach (array_chunk($dataset, $chunkSize) as $chunk) {
            $chunkCount = count($chunk);
            $chunkSum = array_sum(array_column($chunk, 'value'));

            $totalEntries += $chunkCount;
            $sum += $chunkSum;
        }

        $average = $totalEntries > 0 ? $sum / $totalEntries : 0;

        return [
            'total_entries' => $totalEntries,
            'sum' => round($sum,5),
            'average' => round($average,5),
        ];
    }
}
