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
        'message' => "The data field is required.",
        'errors' => [
            'data' => ["The data field is required."]
        ]
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
        'message' => "The data.0.value field is required. (and 1 more error)",
        'errors' => [
            'data.0.value' => [
                "The data.0.value field is required."
            ],
            'data.1.value' => [
                "The data.1.value field must be a number."
            ]
        ]
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
 public function it_handles_extremely_large_payloads()
 {
     $largeDataset = array_map(function ($i) {
         return ['id' => $i, 'value' => rand(1, 100)];
     }, range(1, 10000)); // 10000 entries

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
