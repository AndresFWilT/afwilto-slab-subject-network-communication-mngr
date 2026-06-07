<?php

namespace App\Adapters\In\Http\Resources;

use App\Domain\Huffman\HuffmanNode;
use App\Domain\Huffman\HuffmanResult;
use Illuminate\Http\Resources\Json\JsonResource;

class HuffmanResultResource extends JsonResource
{
    public function __construct(private readonly HuffmanResult $result)
    {
        parent::__construct($result);
    }

    public function toArray($request): array
    {
        $result = $this->result;
        $freqEntries = [];
        foreach ($result->frequencies->entries() as $symbol => $freq) {
            $freqEntries[] = ['symbol' => $symbol, 'frequency' => $freq];
        }

        $codeEntries = [];
        foreach ($result->codeTable->entries() as $symbol => $code) {
            $codeEntries[] = ['symbol' => $symbol, 'code' => $code, 'bitLength' => strlen($code)];
        }

        return [
            'frequencyTable' => $freqEntries,
            'tree'           => $this->serializeNode($result->tree),
            'codeTable'      => $codeEntries,
            'encoding'       => [
                'encodedBitString'   => $result->encodedBitString,
                'originalBitCount'   => $result->stats->originalBitCount,
                'compressedBitCount' => $result->stats->compressedBitCount,
                'compressionRatio'   => $result->stats->compressionRatio,
                'savingsPercent'     => $result->stats->savingsPercent,
            ],
        ];
    }

    private function serializeNode(?HuffmanNode $node): ?array
    {
        if ($node === null) {
            return null;
        }
        return [
            'frequency' => $node->frequency,
            'symbol'    => $node->symbol,
            'left'      => $this->serializeNode($node->left),
            'right'     => $this->serializeNode($node->right),
        ];
    }
}
