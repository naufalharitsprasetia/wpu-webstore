<?php

namespace App\Livewire;

use App\Contract\CartServiceInterface;
use App\Data\CartItemData;
use App\Data\ProductData;
use Livewire\Component;

class AddToCart extends Component
{
    public int $quantity;
    public string $sku;
    public float $price;
    public int $stock;
    public int $weigth;
    public string $label = 'Add to Cart';

    public function mount(ProductData $product, CartServiceInterface $cart)
    {
        $this->sku = $product->sku;
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->weigth = $product->weigth;
        $this->quantity = $cart->getItemBySku($product->sku)->quantity ?? 1;
    }

    protected function rules(): array
    {
        return ['quantity' => ['required', 'integer', 'min:1', "max:{$this->stock}"]];
    }

    public function addToCart(CartServiceInterface $cart)
    {
        $this->validate();
        $cart->addOrUpdate(new CartItemData(
            sku: $this->sku,
            price: $this->price,
            quantity: $this->quantity,
            weigth: $this->weigth
        ));
    }
    public function render()
    {
        return view('livewire.add-to-cart');
    }
}