<?php

namespace App\Adapters\In\Http\Controllers;

use App\Adapters\In\Http\Requests\EncodeTextRequest;
use App\Adapters\In\Http\Resources\HuffmanResultResource;
use App\Application\Huffman\Ports\EncodeWithHuffmanPort;
use Illuminate\Routing\Controller;

class HuffmanController extends Controller
{
    private readonly EncodeWithHuffmanPort $useCase;

    public function __construct(EncodeWithHuffmanPort $useCase)
    {
        $this->useCase = $useCase;
    }

    public function encode(EncodeTextRequest $request): HuffmanResultResource
    {
        $result = $this->useCase->execute($request->validated('text'));
        return new HuffmanResultResource($result);
    }
}
