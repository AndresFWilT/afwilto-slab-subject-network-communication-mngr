<?php

namespace App\Adapters\In\Http\Resources;

use App\Domain\ShannonFano\ShannonFanoResult;
use Illuminate\Http\Resources\Json\JsonResource;

class ShannonFanoResultResource extends JsonResource
{
    public function __construct(private readonly ShannonFanoResult $result)
    {
        parent::__construct($result);
    }

    public function toArray($request): array
    {
        $result = $this->result;

        $entries = array_map(fn($e) => [
            'symbol'         => $e->symbol,
            'frequency'      => $e->frequency,
            'probability'    => $e->probability,
            'entropy'        => $e->entropy,
            'messageEntropy' => $e->messageEntropy,
            'codeBitLength'  => $e->codeBitLength,
            'messageBits'    => $e->messageBits,
            'code'           => $result->codeTable->codeFor($e->symbol),
        ], $result->entries);

        $codeEntries = [];
        foreach ($result->codeTable->entries() as $symbol => $code) {
            $codeEntries[] = ['symbol' => $symbol, 'code' => $code, 'bitLength' => strlen($code)];
        }

        return [
            'entries'  => $entries,
            'totals'   => [
                'frequency'      => $result->totals->frequency,
                'probability'    => $result->totals->probability,
                'entropy'        => $result->totals->entropy,
                'messageEntropy' => $result->totals->messageEntropy,
                'codeBits'       => $result->totals->codeBits,
                'messageBits'    => $result->totals->messageBits,
            ],
            'codeTable' => $codeEntries,
            'encoding'  => [
                'encodedBitString'   => $result->encodedBitString,
                'originalBitCount'   => $result->stats->originalBitCount,
                'compressedBitCount' => $result->stats->compressedBitCount,
                'compressionRatio'   => $result->stats->compressionRatio,
                'savingsPercent'     => $result->stats->savingsPercent,
            ],
        ];
    }
}
