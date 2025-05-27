<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaginatedResource extends JsonResource
{

    public static function format($paginator, $resourceClass)
    {
        return [
            'data' => $resourceClass::collection($paginator)->resolve(),
            'links' => $paginator->toArray()['links'],
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];
    }
}
