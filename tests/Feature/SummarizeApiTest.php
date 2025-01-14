<?php

namespace Tests\Feature;
use Tests\TestCase;

class SummarizeApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
 /** @test */
 public function it_returns_summary_of_data_entries()
 {
     $payload = [
         'data' => [
             ['id' => 1, 'value' => 10],
             ['id' => 2, 'value' => 20],
             ['id' => 3, 'value' => 30],
         ]
     ];

     $response = $this->postJson('/api/summarize', $payload);

     $response->assertStatus(200);
     $response->assertJson([
         'total_entries' => 3,
         'sum' => 60,
         'average' => 20,
     ]);
 }

 /** @test */
 public function it_validates_when_payload_is_empty()
 {
     $response = $this->postJson('/api/summarize', []);

     $response->assertStatus(422);
     $response->assertJson([
         'error' => "The 'data' field must be an array."
     ]);
 }

 /** @test */
 public function it_validates_when_data_field_is_an_empty_array()
 {
     $payload = ['data' => []];

     $response = $this->postJson('/api/summarize', $payload);

     $response->assertStatus(422);
     $response->assertJson([
         'error' => "The 'data' array must contain at least one entry."
     ]);
 }

 /** @test */
 public function it_validates_each_entry_has_a_numeric_value_field()
 {
     $payload = [
         'data' => [
             ['id' => 1],
             ['id' => 2, 'value' => 'non-numeric']
         ]
     ];

     $response = $this->postJson('/api/summarize', $payload);

     $response->assertStatus(422);
     $response->assertJson([
         'error' => "Each entry in the 'data' array must have a numeric 'value' field."
     ]);
 }

 /** @test */
 public function it_correctly_summarizes_a_large_payload()
 {
     $largeDataset = array_map(function ($i) {
         return ['id' => $i, 'value' => $i % 100];
     }, range(1, 1000000));

     $payload = ['data' => $largeDataset];

     $totalEntries = count($largeDataset);
     $sum = array_reduce($largeDataset, function ($carry, $item) {
         return $carry + $item['value'];
     }, 0);
     $average = $sum / $totalEntries;

     $response = $this->postJson('/api/summarize', $payload);

     $response->assertStatus(200);
     $response->assertJson([
         'total_entries' => $totalEntries,
         'sum' => $sum,
         'average' => $average,
     ]);
 }

 /** @test */
 public function it_handles_high_precision_decimal_values_correctly()
 {
     $payload = [
         'data' => [
             ['id' => 1, 'value' => 10.12345],
             ['id' => 2, 'value' => 20.56789],
             ['id' => 3, 'value' => 30.98765]
         ]
     ];

     $response = $this->postJson('/api/summarize', $payload);

     $response->assertStatus(200);
     $response->assertJson([
         'total_entries' => 3,
         'sum' => 61.67899,
         'average' => 20.55966
     ]);
 }

 /** @test */
 public function it_handles_malformed_json_payload()
 {
     $response = $this->post('/api/summarize', '{data: [invalid JSON]', ['Content-Type' => 'application/json']);

     $response->assertStatus(400); // Bad Request
     $response->assertJson([
         'error' => 'Invalid JSON payload.'
     ]);
 }

 /** @test */
 public function it_handles_extremely_large_payloads()
 {
     $largeDataset = array_map(function ($i) {
         return ['id' => $i, 'value' => rand(1, 100)];
     }, range(1, 5000000)); // 5 million entries

     $payload = ['data' => $largeDataset];

     $response = $this->postJson('/api/summarize', $payload);

     $response->assertStatus(200);
     $response->assertJsonStructure([
         'total_entries',
         'sum',
         'average',
     ]);
 }
}
