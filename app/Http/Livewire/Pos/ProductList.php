<?php

namespace App\Http\Livewire\Pos;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Product\Entities\Category;
use Modules\Product\Entities\Product;
use Modules\Product\Http\Controllers\CategoriesController;

class ProductList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'selectedCategory' => 'categoryChanged',
        'showCount'        => 'showCountChanged'
    ];

    public $categories;
    public $category_id;
    public $limit = 9;

    public function mount($categories) {
        $this->categories = $categories;
        $this->category_id = '';
    }

    public function render() {
        $products = Product::all();
        foreach ($products as $product) {
            $product->image = $product->getFirstMediaUrl('images');
            $product->product_price_old = $product->product_price;
            if($product->product_is_discount == 1) {
                if ($product->product_discount_percentage > 0) {
                    $product->product_price = $product->product_price * (100 - $product->product_discount_percentage)/100;
                } else if ($product->product_discount_amount > 0) {
                    $product->product_price = $product->product_price - $product->product_discount_amount;
                }
            }
        }

        return view('livewire.pos.product-list', [

            'products' =>$products,
            'categories' => Category::all()
//            ->paginate($this->limit)
        ]);
    }

    public function categoryChanged($category_id) {
        $this->category_id = $category_id;
        $this->resetPage();
    }

    public function showCountChanged($value) {
        $this->limit = $value;
        $this->resetPage();
    }

    public function selectProduct($product) {
        $this->emit('productSelected', $product);
    }
}
