<?php

namespace Webkul\Product\Listeners;

use Webkul\Product\Repositories\ProductRepository;
use Webkul\Product\Repositories\ProductBundleOptionProductRepository;
use Webkul\Product\Repositories\ProductGroupedProductRepository;
use Webkul\Product\Helpers\Indexers\{Inventory, Price, ElasticSearch, Flat};
use Webkul\Checkout\Repositories\{CartItemRepository ,CartRepository};

class Product
{
    protected $indexers = [
        'inventory' => Inventory::class,
        'price'     => Price::class,
        'elastic'   => ElasticSearch::class,
        'flat'      => Flat::class,
    ];

    /**
     * Create a new listener instance.
     *
     * @param  \Webkul\Product\Repositories\ProductRepository  $productRepository
     * @param  \Webkul\Product\Repositories\ProductBundleOptionProductRepository  $productBundleOptionProductRepository
     * @param  \Webkul\Product\Repositories\ProductGroupedProductRepository  $productGroupedProductRepository
     * @return void
     */
    public function __construct(
        protected ProductRepository $productRepository,
        protected ProductBundleOptionProductRepository $productBundleOptionProductRepository,
        protected ProductGroupedProductRepository $productGroupedProductRepository,
        protected CartItemRepository $cartItemsRepository,
        protected CartRepository $cartRepository
    )
    {
    }

    /**
     * Update or create product indices
     *
     * @param  \Webkul\Product\Contracts\Product  $product
     * @return void
     */
    public function afterCreate($product)
    {
        app($this->indexers['flat'])->refresh($product);
    }

    /**
     * Update or create product indices
     *
     * @param  \Webkul\Product\Contracts\Product  $product
     * @return void
     */
    public function afterUpdate($product)
    {
        $products = $this->getAllRelatedProducts($product);

        app($this->indexers['flat'])->refresh($product);

        app($this->indexers['inventory'])->reindexRows($products);

        app($this->indexers['price'])->reindexRows($products);

        if (core()->getConfigData('catalog.products.storefront.search_mode') == 'elastic') {
            app($this->indexers['elastic'])->reindexRows($products);
        }

        $this->updateCartItems($product);
    }

    /**
     * Delete product indices
     *
     * @param  integer  $productId
     * @return void
     */
    public function beforeDelete($productId)
    {
        if (core()->getConfigData('catalog.products.storefront.search_mode') != 'elastic') {
            return;
        }

        $product = $this->productRepository->find($productId);

        app($this->indexers['elastic'])->reindexRow($product);
    }

    /**
     * Returns parents bundle products associated with simple product
     *
     * @param  \Webkul\Product\Contracts\Product  $product
     * @return array
     */
    public function getAllRelatedProducts($product)
    {
        $products = [$product];

        if ($product->type == 'simple') {
            if ($product->parent_id) {
                $products[] = $product->parent;
            }

            $products = array_merge(
                $products,
                $this->getParentBundleProducts($product),
                $this->getParentGroupProducts($product)
            );
        } elseif ($product->type == 'configurable') {
            $products = [];

            foreach ($product->variants as $variant) {
                $products[] = $variant;
            }

            $products[] = $product;
        }

        return $products;
    }

    /**
     * Returns parents bundle products associated with simple product
     *
     * @param  \Webkul\Product\Contracts\Product  $product
     * @return array
     */
    public function getParentBundleProducts($product)
    {
        $bundleOptionProducts = $this->productBundleOptionProductRepository->findWhere([
            'product_id' => $product->id,
        ]);

        $products = [];

        foreach ($bundleOptionProducts as $bundleOptionProduct) {
            $products[] = $bundleOptionProduct->bundle_option->product;
        }

        return $products;
    }

    /**
     * Returns parents group products associated with simple product
     *
     * @param  \Webkul\Product\Contracts\Product  $product
     * @return array
     */
    public function getParentGroupProducts($product)
    {
        $groupedOptionProducts = $this->productGroupedProductRepository->findWhere([
            'associated_product_id' => $product->id,
        ]);

        $products = [];

        foreach ($groupedOptionProducts as $groupedOptionProduct) {
            $products[] = $groupedOptionProduct->product;
        }

        return $products;
    }

    /**
     * Update Cart items
     *
     * @return void
     */
    public function updateCartItems($product)
    {
        $cartItems = $this->cartItemsRepository->findWhere(['product_id' => $product->id]);

        $cartItems->each(function ($cartItem) use ($product) {
            $cart = $this->cartRepository->find($cartItem->cart_id);

            if ($customer = $cart->customer) {
                $groupIdFromCart = $customer->group->id;

                $priceIndexPrice = $product->price_indices->where('customer_group_id', $groupIdFromCart)->first();
            } else {
                $priceIndexPrice = $product->price_indices->where('customer_group_id', 1)->first();
            }

            if ($cartItem->parent_id) {
                $this->cartItemsRepository->update([
                    'price'      => $priceIndexPrice->min_price,
                    'base_price' => $priceIndexPrice->min_price,
                    'total'      => $priceIndexPrice->min_price,
                    'base_total' => $priceIndexPrice->min_price,
                ], $cartItem->parent_id);
            }
            
            $cartItem->update([
                'price'      => $priceIndexPrice->min_price,
                'base_price' => $priceIndexPrice->min_price,
                'total'      => $priceIndexPrice->min_price,
                'base_total' => $priceIndexPrice->min_price,
            ]);

            $productId = $cartItem->parent_id ? $product->id : null;

            $cartTotal = $this->cartItemsRepository->getSumOfColumns($cartItem->cart_id, $productId);
            
            $this->cartRepository->update([
                'sub_total'        => $cartTotal->sub_total,
                'base_sub_total'   => $cartTotal->base_sub_total,
                'base_grand_total' => $cartTotal->base_grand_total,
                'grand_total'      => $cartTotal->grand_total,
            ], $cartItem->cart_id);
        });
    }
}
