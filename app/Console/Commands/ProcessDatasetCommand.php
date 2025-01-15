<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use App\Jobs\SummarizeChunkJob;
use JsonMachine\Items;
use JsonMachine\JsonDecoder\ExtJsonDecoder;

class ProcessDatasetCommand extends Command
{
    protected $signature = 'dataset:process {dataset? : The path to the JSON dataset file}';
    protected $description = 'Process a large dataset, handling single JSON objects with a "data" key using streaming';

    public function handle()
    {
        ini_set('max_execution_time', 0); // Prevent script timeout
        $datasetPath = $this->argument('dataset') ?? 'payload.json';

        if (!file_exists($datasetPath)) {
            $this->error('File does not exist.');
            return Command::FAILURE;
        }

        $this->info("File found: $datasetPath");
        $this->info("Starting dataset processing...");

        try {
            return $this->processJsonWithStreaming($datasetPath);
        } catch (\Throwable $e) {
            $this->error("Unexpected error: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    private function processJsonWithStreaming(string $filePath): int
    {
        $this->info("Processing JSON object with 'data' key using streaming...");
        $chunk = [];
        $chunkSize = 10000;

        // Stream the "data" key as associative arrays
        $data = Items::fromFile($filePath, [
            'pointer' => '/data',
            'decoder' => new ExtJsonDecoder(true) // Convert stdClass to associative arrays
        ]);

        foreach ($data as $record) {
            if (!isset($record['id'], $record['value'])) {
                $this->error("Invalid structure. Skipping record: " . json_encode($record));
                continue;
            }

            $chunk[] = $record;

            // Dispatch a job when chunk size is reached
            if (count($chunk) >= $chunkSize) {
                $this->dispatchChunk($chunk);
                $chunk = [];
            }
        }

        // Dispatch remaining chunk
        if (!empty($chunk)) {
            $this->dispatchChunk($chunk);
        }

        $this->info("Dataset processing started. Monitor the queue for progress.");
        return Command::SUCCESS;
    }

    private function dispatchChunk(array $chunk): void
    {
        SummarizeChunkJob::dispatch($chunk)->delay(now()->addSeconds(5));

        $this->info("Dispatched a job with " . count($chunk) . " records with a 5-second delay.");
    }
}
