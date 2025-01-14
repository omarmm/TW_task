<?php

namespace App\Http\Controllers;

use App\Http\Requests\SummarizeRequest;
use App\Services\SummarizeService;
use Illuminate\Http\Request;

class SummarizeController extends Controller
{
    protected $service;

    public function __construct(SummarizeService $service)
    {
        $this->service = $service;
    }

    public function summarize(SummarizeRequest $request)
    {
        $validatedData = $request->validated();

        return response()->json(
            $this->service->summarize($validatedData['data'])
        );
    }
}
