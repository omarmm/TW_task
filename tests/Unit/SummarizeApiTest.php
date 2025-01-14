<?php

namespace Tests\Unit;

use App\Services\SummarizeService;
use PHPUnit\Framework\TestCase;

class SummarizeApiTest extends TestCase
{
   /** @test */
    public function it_correctly_summarizes_data_entries()
    {
        $data = [
            ['id' => 1, 'value' => 10],
            ['id' => 2, 'value' => 20],
            ['id' => 3, 'value' => 30],
        ];

        $service = new SummarizeService();
        $result = $service->summarize($data);

        $this->assertEquals(3, $result['total_entries']);
        $this->assertEquals(60, $result['sum']);
        $this->assertEquals(20, $result['average']);
    }

    /** @test */
    public function it_handles_an_empty_data_array()
    {
        $data = [];

        $service = new SummarizeService();
        $result = $service->summarize($data);

        $this->assertEquals(0, $result['total_entries']);
        $this->assertEquals(0, $result['sum']);
        $this->assertEquals(0, $result['average']);
    }

    /** @test */
    public function it_handles_high_precision_values()
    {
        $data = [
            ['id' => 1, 'value' => 10.123],
            ['id' => 2, 'value' => 20.456],
            ['id' => 3, 'value' => 30.789],
        ];

        $service = new SummarizeService();
        $result = $service->summarize($data);

        $this->assertEquals(3, $result['total_entries']);
        $this->assertEquals(61.368, $result['sum']);
        $this->assertEquals(20.456, $result['average']);
    }
}
