<?php

namespace App\Adapters\In\Http\Controllers;

use App\Adapters\In\Http\Requests\EncodeTextRequest;
use App\Adapters\In\Http\Resources\ShannonFanoResultResource;
use App\Application\ShannonFano\Ports\EncodeWithShannonFanoPort;
use Illuminate\Routing\Controller;

class ShannonFanoController extends Controller
{
    private readonly EncodeWithShannonFanoPort $useCase;

    public function __construct(EncodeWithShannonFanoPort $useCase)
    {
        $this->useCase = $useCase;
    }

    public function encode(EncodeTextRequest $request): ShannonFanoResultResource
    {
        $result = $this->useCase->execute($request->validated('text'));
        return new ShannonFanoResultResource($result);
    }
}
