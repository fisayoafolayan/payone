<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */
namespace Pyz\Yves\Wishlist\Communication\Controller;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\ProductOptionTransfer;
use Pyz\Yves\Wishlist\Communication\Plugin\Provider\WishlistControllerProvider;
use SprykerEngine\Yves\Application\Communication\Controller\AbstractController;
use SprykerFeature\Client\Wishlist\Service\WishlistClientInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @method WishlistClientInterface getClient()
 */
class WishlistController extends AbstractController
{

    /**
     * @return array
     */
    public function indexAction()
    {
        $wishlistClient = $this->getClient();

        return [
          'customerWishlist' => $wishlistClient->getWishlist(),
        ];
    }

    /**
     * @param string $sku
     * @param int $quantity
     * @param array $optionValueUsageIds
     *
     * @return RedirectResponse
     */
    public function addAction($sku, $quantity = 1, $optionValueUsageIds = [])
    {
        $wishlistClient = $this->getClient();

        $itemTransfer = new ItemTransfer();
        $itemTransfer->setSku($sku)->setQuantity($quantity);

        foreach ($optionValueUsageIds as $idOptionValueUsage) {
            $productOptionTransfer = new ProductOptionTransfer();
            $productOptionTransfer->setIdOptionValueUsage($idOptionValueUsage)
                ->setLocaleCode($this->getLocale());
            $itemTransfer->addProductOption($productOptionTransfer);
        }

        $wishlistClient->addItem($itemTransfer);

        return $this->redirectResponseInternal(WishlistControllerProvider::ROUTE_WISHLIST);
    }

    /**
     * @param string $sku
     * @param string $groupKey
     *
     * @return RedirectResponse
     */
    public function removeAction($sku, $groupKey)
    {
        $wishlistClient = $this->getClient();

        $itemTransfer = new ItemTransfer();
        $itemTransfer->setSku($sku)->setGroupKey($groupKey)->setQuantity(0);

        $wishlistClient->removeItem($itemTransfer);

        return $this->redirectResponseInternal(WishlistControllerProvider::ROUTE_WISHLIST);
    }

    /**
     * @param string $sku
     * @param string $groupKey
     *
     * @return RedirectResponse
     */
    public function increaseAction($sku, $groupKey)
    {
        $wishlistClient = $this->getClient();

        $itemTransfer = new ItemTransfer();
        $itemTransfer->setSku($sku)->setGroupKey($groupKey)->setQuantity(1);

        $wishlistClient->increaseItemQuantity($itemTransfer);

        return $this->redirectResponseInternal(WishlistControllerProvider::ROUTE_WISHLIST);
    }

    /**
     * @param string $sku
     * @param string $groupKey
     *
     * @return RedirectResponse
     */
    public function decreaseAction($sku, $groupKey)
    {
        $wishlistClient = $this->getClient();

        $itemTransfer = new ItemTransfer();
        $itemTransfer->setSku($sku)->setGroupKey($groupKey)->setQuantity(1);

        $wishlistClient->decreaseItemQuantity($itemTransfer);

        return $this->redirectResponseInternal(WishlistControllerProvider::ROUTE_WISHLIST);
    }

    /**
     * @param string $groupKey
     *
     * @return RedirectResponse
     */
    public function addToCartAction($groupKey)
    {
        $wishlistClient = $this->getClient();

        $wishlistItems = $wishlistClient->getWishlist();
        $wishlistItem = null;
        foreach ($wishlistItems->getItems() as $item) {
            if ($groupKey === $item->getGroupKey()) {
                $wishlistItem = clone $item;
                break;
            }
        }

        if ($wishlistItem !== null) {
            $cartClient = $this->getLocator()->cart()->client();
            $wishlistItem->setGroupKey(null);
            $cartClient->addItem($wishlistItem);
        }

        return $this->redirectResponseInternal(WishlistControllerProvider::ROUTE_WISHLIST);
    }

}
