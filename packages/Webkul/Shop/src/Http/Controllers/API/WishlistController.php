<?php

namespace Webkul\Shop\Http\Controllers\API;

use Cart;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Customer\Repositories\WishlistRepository;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Shop\Http\Resources\WishlistResource;

class WishlistController extends APIController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected WishlistRepository $wishlistRepository,
        protected ProductRepository $productRepository
    ) {
    }

    /**
     * Displays the listing resources if the customer has items on the wishlist.
     */
    public function index(): JsonResource
    {
        if (! core()->getConfigData('general.content.shop.wishlist_option')) {
            abort(404);
        }

        $this->removeInactiveItems();

        $items = $this->wishlistRepository
            ->where([
                'channel_id'  => core()->getCurrentChannel()->id,
                'customer_id' => auth()->guard('customer')->user()->id,
            ])
            ->get();

        return WishlistResource::collection($items);
    }

    /**
     * Function to add items to the wishlist.
     */
    public function store(): JsonResource
    {
        $product = $this->productRepository->with('parent')->find(request()->input('product_id'));

        if (! $product) {
            return new JsonResource([
                'message' => trans('customer::app.product-removed'),
            ]);
        }

        $data = [
            'channel_id'  => core()->getCurrentChannel()->id,
            'product_id'  => $product->id,
            'customer_id' => auth()->guard('customer')->user()->id,
        ];

        if (
            $product->parent
            && $product->parent->type !== 'configurable'
        ) {
            $product = $product->parent;
            $data['product_id'] = $product->id;
        }

        if (! $this->wishlistRepository->findOneWhere($data)) {
            $this->wishlistRepository->create($data);

            return new JsonResource([
                'message' => trans('customer::app.wishlist.success'),
            ]);
        }

        $this->wishlistRepository->findOneWhere([
            'product_id' => $data['product_id'],
        ])->delete();

        return new JsonResource([
            'message' => trans('customer::app.wishlist.removed'),
        ]);
    }

    /**
     * Move the wishlist item to the cart.
     *
     * @param  int  $id
     */
    public function moveToCart($id): JsonResource
    {
        $wishlistItem = $this->wishlistRepository->findOneWhere([
            'id'          => $id,
            'customer_id' => auth()->guard('customer')->user()->id,
        ]);

        if (! $wishlistItem) {
            abort(404);
        }

        try {
            $result = Cart::moveToCart($wishlistItem);

            if ($result) {
                return new JsonResource([
                    'data' => [
                        'wishlist' => WishlistResource::collection($this->wishlistRepository->get()), 
                        'cart'     => ['items_count'  => Cart::getCart()->items_count],
                    ],

                    'message'  => trans('shop::app.components.products.item-add-to-cart'),
                ]);
            }

            return new JsonResource([
                'redirect' => true,
                'data'     => route('shop.productOrCategory.index', $wishlistItem->product->url_key),
                'message'  => trans('shop::app.checkout.cart.integrity.missing_options'),
            ]);

        } catch (\Exception $exception) {
            return new JsonResource([
                'redirect' => true,
                'data'     => route('shop.productOrCategory.index', $wishlistItem->product->url_key),
                'message'  => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Function to remove items to the wishlist.
     *
     * @param  int  $id
     */
    public function destroy($id): JsonResource
    {
        $this->wishlistRepository->delete($id);

        return new JsonResource([
            'data'    => WishlistResource::collection($this->wishlistRepository->get()),
            'message' => trans('shop::app.customers.account.wishlist.removed'),
        ]);
    }

    /**
     * Method for removing all items from the wishlist.
     */
    public function destroyAll(): JsonResource
    {
        $success = $this->wishlistRepository->deleteWhere([
            'customer_id'  => auth()->guard('customer')->user()->id,
        ]);

        if (! $success) {
            return new JsonResource([
                'message'  => trans('shop::app.customers.account.wishlist.remove-fail'),
            ]);
        }

        return new JsonResource([
            'message'  => trans('shop::app.customers.account.wishlist.removed'),
        ]);
    }

    /**
     * Removing inactive wishlist items.
     *
     * @return int
     */
    protected function removeInactiveItems()
    {
        $customer = auth()->guard('customer')->user();

        $customer->load(['wishlist_items.product']);

        $inactiveItemIds = $customer->wishlist_items
            ->filter(fn ($item) => ! $item->product->status)
            ->pluck('product_id')
            ->toArray();

        return $customer->wishlist_items()
            ->whereIn('product_id', $inactiveItemIds)
            ->delete();
    }
}
