<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Services\SummarizeService;
use Illuminate\Support\Facades\Cache;

class SummarizeChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected array $chunk;

    public function __construct(array $chunk)
    {
        $this->chunk = $chunk;
    }

    public function handle(SummarizeService $service)
    {
        // Process the chunk
        $result = $service->summarize($this->chunk);

        // Store the result in cache for later aggregation
        $cacheKey = 'chunk_result_' . $this->batchId;
        Cache::put($cacheKey, $result, 3600); // Cache expires in 1 hour

        // Print the result to the queue worker console
        $this->printResultToConsole($result);
    }

    private function printResultToConsole(array $result): void
    {
        $totalEntries = $result['total_entries'];
        $sum = $result['sum'];
        $average = $result['average'];

        echo PHP_EOL . "Processed Chunk:" . PHP_EOL;
        echo " - Total Entries: $totalEntries" . PHP_EOL;
        echo " - Sum: $sum" . PHP_EOL;
        echo " - Average: $average" . PHP_EOL;
    }
}
