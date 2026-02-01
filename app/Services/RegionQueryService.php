<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\RegionData;
use App\Models\Region;
use Spatie\LaravelData\DataCollection;

class RegionQueryService
{

    public function searchRegionByName(string $keyword, int $limit = 5): DataCollection
    {
        $region = Region::where('type', 'village')->where(function ($query) use ($keyword) {
            $query->where('name', 'LIKE', "%$keyword%")
                ->orWhere('postal_code', 'LIKE', "%$keyword%")
                ->orWhereHas('parent', function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', "%$keyword%");
                })
                ->orWhereHas('parent.parent', function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', "%$keyword%");
                })
                ->orWhereHas('parent.parent.parent', function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', "%$keyword%");
                });
        })->with(['parent', 'parent.parent', 'parent.parent.parent'])
            ->limit($limit)
            ->get();

        return new DataCollection(RegionData::class, $region);
    }

    public function searchRegionByCode(string $code): RegionData
    {
        return RegionData::fromModel(Region::where('code', $code)->first());
    }
}
