<?php

/**
 * Woopra Module for Magento
 *
 * @package   Woopra_Analytics
 * @author    K3Live for Woopra <support@woopra.com>
 * @copyright 2017 Copyright (c) Woopra (http://www.woopra.com/)
 * @license   Open Software License (OSL 3.0)
 */

namespace Woopra\Analytics\Observer;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\ObserverInterface;
use Woopra\Analytics\Helper\Data;

class CartRemoveObserver implements ObserverInterface
{
    private $customerSession;
    private $dataHelper;
    private $request;

    public function __construct(
        Data $dataHelper,
        Http $request,
        Session $customerSession
    ) {
        $this->customerSession = $customerSession;
        $this->dataHelper = $dataHelper;
        $this->request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->dataHelper->getProductRemovedFromCart() != null) {
            $event = $observer->getEvent();
            if ($event) {
                $product = $event->getQuoteItem()->getProduct();
                $this->customerSession->setData('woopra_cart_wishlist_trigger', 1);
                $this->customerSession->setData(
                    'woopra_cart_wishlist_status',
                    $this->dataHelper->getProductRemovedFromCart()
                );
                $this->customerSession->setData('woopra_cart_wishlist_name', $product['name']);
                $this->customerSession->setData('woopra_cart_wishlist_sku', $product['sku']);
                $this->customerSession->setData('woopra_cart_wishlist_price', round($product['price'], 2));
            }
        }
    }
}
