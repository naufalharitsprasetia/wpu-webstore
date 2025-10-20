<?php

namespace App\Livewire;

use App\Data\ProductCollectionData;
use App\Models\Tag;
use App\Models\Product;
use Livewire\Component;
use App\Data\ProductData;

class ProductCatalog extends Component
{
    public function render()
    {
        $collection_result = Tag::query()->withType('collection')->withCount('products')->get();
        $result = Product::paginate(1);


        $products = ProductData::collect($result);
        $collections = ProductCollectionData::collect($collection_result);

        return view('livewire.product-catalog', compact('products', 'collections'));
    }
}