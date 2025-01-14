# Summarize API - Laravel Project

## Overview
This project is a Laravel-based API that processes and summarizes large datasets efficiently. It calculates the total number of entries, their sum, and the average value. Designed to handle records with memory-efficient chunk-based processing, this API also includes robust validation and thorough test coverage.

---

## Features
- **Efficient Summarization**: Processes large datasets using chunking to optimize memory usage.
- **Validation**: Strict validation of the incoming payload to ensure correctness.
- **Testing**: Comprehensive unit and feature tests for the service and API.
- **Asynchronous Processing**: Supports queue-based background processing for scalability (optional).

---

## Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/omarmm/TW_task.git
   cd TW_task
   composer install

2. **Run the tests**
   ```bash
   php artisan test

## please note that I have used /**@test */ annotations , if it will cause warnings it could be replaced with #[Test] I just afraid that for the old versions #[Test] may make the test not working

3. **API Request Example**
  ```bash
php artisan serve

curl -X POST http://localhost:8000/api/summarize \
-H "Content-Type: application/json" \
-H "Accept: application/json" \
-d '{
  "data": [
    { "id": 1, "value": 10 },
    { "id": 2, "value": 20 },
    { "id": 3, "value": 30 }
  ]
}'
