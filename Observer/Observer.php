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

use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Session\Proxy as CheckoutSession;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Escaper;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Woopra\Analytics\Helper\Data;

class Observer implements ObserverInterface
{
    private $checkoutSession;
    private $customerSession;
    private $dataHelper;
    private $escaper;
    private $order;
    private $productRepository;
    private $request;

    public function __construct(
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession,
        Data $dataHelper,
        Escaper $escaper,
        Order $order,
        ProductRepository $productRepository,
        Http $request
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->dataHelper = $dataHelper;
        $this->escaper = $escaper;
        $this->order = $order;
        $this->productRepository = $productRepository;
        $this->request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->request->getFullActionName() == 'customer_account_logoutSuccess' &&
            $this->dataHelper->getCustomerLogout() != null) {
            $this->customerSession->setData('woopra_login_logout_trigger', 1);
            $this->customerSession->setData(
                'woopra_login_logout_status',
                $this->dataHelper->getCustomerLogout()
            );
        }

        if ($this->request->getFullActionName() == 'contacts_index_post' &&
            $this->dataHelper->getContactFormSent() != null) {
            $request = $this->request->getParams();
            if ($request) {
                $this->customerSession->setData('woopra_contacts_index_post', 1);
                $this->customerSession->setData(
                    'woopra_contacts_name',
                    $this->escaper->escapeHtml($request['name'])
                );
                $this->customerSession->setData(
                    'woopra_contacts_email',
                    $this->escaper->escapeHtml($request['email'])
                );
                $this->customerSession->setData(
                    'woopra_contacts_telephone',
                    $this->escaper->escapeHtml($request['telephone'])
                );
                $this->customerSession->setData(
                    'woopra_contacts_comment',
                    $this->escaper->escapeHtml($request['comment'])
                );
            }
        }

        if ($this->request->getFullActionName() == 'wishlist_index_remove' &&
            $this->dataHelper->getProductRemovedFromWishlist() != null) {
            $request = $this->request->getParams();
            if ($request) {
                $this->customerSession->setData('woopra_cart_wishlist_trigger', 1);
                $this->customerSession->setData(
                    'woopra_cart_wishlist_status',
                    $this->dataHelper->getProductRemovedFromWishlist()
                );
            }
        }

        if ($this->request->getFullActionName() == 'checkout_onepage_index' &&
            $this->dataHelper->getCheckoutBillingAddress() != null) {
            $this->customerSession->setData('woopra_checkout_trigger', 1);
        }

        if ($this->request->getFullActionName() == 'checkout_onepage_savePayment' &&
            $this->dataHelper->getCheckoutPaymentMethod() != null) {
            $request = $this->request->getParams();
            $this->customerSession->setData('woopra_checkout_trigger', 1);
            $this->customerSession->setData(
                'woopra_checkout_payment_method',
                $request['payment']['method']
            );
            if ($request['payment']['method'] == 'ccsave') {
                $this->customerSession->setData(
                    'woopra_checkout_payment_cc_type',
                    $request['payment']['cc_type']
                );
            }
        }

        if (($this->request->getFullActionName() == 'checkout_onepage_success' ||
            $this->request->getFullActionName() == 'checkout_multishipping_success')
            && $this->dataHelper->getCheckoutSuccess() != null) {
            $lastOrderId = $this->checkoutSession->getLastRealOrderId();
            if ($lastOrderId) {
                $order = $this->order->loadByIncrementId($lastOrderId);
                $cost = 0;
                $this->customerSession->setData('woopra_checkout_success_trigger', 1);
                $this->customerSession->setData(
                    'woopra_checkout_success_coupon_code',
                    $order->getCouponCode()
                );
                $this->customerSession->setData(
                    'woopra_checkout_success_discount_amount',
                    round($order->getDiscountAmount(), 2)
                );
                $this->customerSession->setData('woopra_checkout_success_order_id', $lastOrderId);
                $this->customerSession->setData(
                    'woopra_checkout_success_order_subtotal',
                    round($order->getSubtotal(), 2)
                );
                $this->customerSession->setData(
                    'woopra_checkout_success_order_total',
                    round($order->getGrandTotal(), 2)
                );
                $this->customerSession->setData(
                    'woopra_checkout_success_order_weight',
                    round($order->getWeight(), 2)
                );
                $this->customerSession->setData(
                    'woopra_checkout_success_shipping_amount',
                    round($order->getShippingAmount(), 2)
                );
                $this->customerSession->setData(
                    'woopra_checkout_success_shipping_description',
                    $order->getShippingDescription()
                );
                $this->customerSession->setData(
                    'woopra_checkout_success_total_items_ordered',
                    round($order->getTotalQtyOrdered(), 0)
                );
                $items = $order->getAllVisibleItems();
                foreach ($items as $item) {
                    $cost = $cost + round($this->productRepository->getById($item->getProductId())->getData('cost'), 2);
                }
                $profit = $order->getSubtotal() - $cost;
                if ($cost != 0) {
                    $this->customerSession->setData('woopra_checkout_success_profit', round($profit, 2));
                }
            }
        }

        if ($this->request->getFullActionName() === 'catalogsearch_result_index' &&
            $this->dataHelper->getCatalogSearch() != null) {
            $request = $this->request->getParams();
            if ($request) {
                $this->customerSession->setData('woopra_search_trigger', 1);
                $this->customerSession->setData(
                    'woopra_search_keywords',
                    $this->escaper->escapeHtml($request['q'])
                );
            }
        }

        if ($this->request->getFullActionName() === 'catalogsearch_advanced_result' &&
            $this->dataHelper->getCatalogSearch() != null) {
            $request = $this->request->getParams();
            $subtotal = '';
            $searchKeywords = '';
            if ($request) {
                foreach ($request as $key => $answer) {
                    if (is_array($answer) == true) {
                        foreach ($answer as $subkey => $subanswer) {
                            $subtotal = $subtotal." ".$subkey.":".$subanswer;
                            $answer = $subtotal;
                        }
                        $subtotal = '';
                    }
                    $searchKeywords = $searchKeywords." | ".$key.": ".$answer;
                }
                $this->customerSession->setData('woopra_search_trigger', 1);
                $this->customerSession->setData(
                    'woopra_search_keywords',
                    $this->escaper->escapeHtml($searchKeywords)
                );
            }
        }

        if ($this->request->getFullActionName() === 'review_product_list' &&
            $this->dataHelper->getProductReviewRead() != null) {
            $request = $this->request->getParams();
            if ($request) {
                $product = $this->productRepository->getById($request['id'])->getData();
                $this->customerSession->setData('woopra_cart_wishlist_trigger', 1);
                $this->customerSession->setData(
                    'woopra_cart_wishlist_status',
                    $this->dataHelper->getProductReviewRead()
                );
                $this->customerSession->setData('woopra_cart_wishlist_name', $product['name']);
                $this->customerSession->setData('woopra_cart_wishlist_sku', $product['sku']);
                $this->customerSession->setData('woopra_cart_wishlist_price', round($product['price'], 2));
            }
        }

        if ($this->request->getFullActionName() === 'review_product_post' &&
            $this->dataHelper->getProductReviewPosted() != null) {
            $request = $this->request->getParams();
            if ($request) {
                $product = $this->productRepository->getById($request['id'])->getData();
                $this->customerSession->setData('woopra_cart_wishlist_trigger', 1);
                $this->customerSession->setData(
                    'woopra_cart_wishlist_status',
                    $this->dataHelper->getProductReviewPosted()
                );
                $this->customerSession->setData('woopra_cart_wishlist_name', $product['name']);
                $this->customerSession->setData('woopra_cart_wishlist_sku', $product['sku']);
                $this->customerSession->setData('woopra_cart_wishlist_price', round($product['price'], 2));
                $this->customerSession->setData('woopra_product_review_trigger', 1);
                $this->customerSession->setData(
                    'woopra_product_review_nickname',
                    $this->escaper->escapeHtml($request['nickname'])
                );
                $this->customerSession->setData(
                    'woopra_product_review_title',
                    $this->escaper->escapeHtml($request['title'])
                );
                $this->customerSession->setData(
                    'woopra_product_review_detail',
                    $this->escaper->escapeHtml($request['detail'])
                );
            }
        }

        if ($this->request->getFullActionName() === 'customer_account_forgotpasswordpost' &&
            $this->dataHelper->getForgotPassword() != null) {
            $request = $this->request->getParams();
                $this->customerSession->setData('woopra_forgot_password_trigger', 1);
            if ($request) {
                $this->customerSession->setData(
                    'woopra_forgot_password_email',
                    $this->escaper->escapeHtml($request['email'])
                );
            }
        }

        if ($this->request->getFullActionName() === 'customer_account_editPost' &&
            $this->dataHelper->getChangedPassword() != null) {
            $request = $this->request->getParams();
            if (array_key_exists('change_password', $request) && $request['change_password'] == 1 && $request['current_password'] != $request['password']) {
                $this->customerSession->setData('woopra_password_changed_trigger', 1);
            }
        }

        if ($this->request->getFullActionName() === 'checkout_cart_couponPost' &&
            $this->dataHelper->getCouponCodeAdded() != null) {
            $request = $this->request->getParams();
            if ($request) {
                $this->customerSession->setData('woopra_coupon_code_trigger', 1);
                if ($request['remove'] == 1) {
                    $this->customerSession->setData(
                        'woopra_coupon_code_status',
                        $this->dataHelper->getCouponCodeRemoved()
                    );
                } else {
                    $this->customerSession->setData(
                        'woopra_coupon_code_status',
                        $this->dataHelper->getCouponCodeAdded()
                    );
                }
                $this->customerSession->setData(
                    'woopra_coupon_code',
                    $this->escaper->escapeHtml($request['coupon_code'])
                );
            }
        }

        if ($this->request->getFullActionName() === 'customer_account_create' &&
            $this->dataHelper->getCustomerCreateAccount() != null) {
            $this->customerSession->setData('woopra_create_account_trigger', 1);
        }

        if ($this->request->getFullActionName() === 'customer_account_createpost' &&
            $this->dataHelper->getCustomerCreateAccount() != null) {
            $request = $this->request->getParams();
            $this->customerSession->setData('woopra_create_account_success_trigger', 1);
            if (isset($request['is_subscribed'])) {
                if ($request['is_subscribed'] == 1) {
                    $this->customerSession->setData('woopra_subscriber_changed', 1);
                    $this->customerSession->setData(
                        'woopra_subscriber_status',
                        $this->dataHelper->getNewsletterSubscribed()
                    );
                    $this->customerSession->setData(
                        'woopra_subscriber_email',
                        $this->escaper->escapeHtml($request['email'])
                    );
                }
            }
        }

        if ($this->request->getFullActionName() === 'checkout_cart_estimatePost' &&
            $this->dataHelper->getEstimatePost() != null) {
            $request = $this->request->getParams();
            if ($request) {
                $this->customerSession->setData('woopra_estimate_post_trigger', 1);
                $this->customerSession->setData('woopra_estimate_post_country', $request['country_id']);
                $this->customerSession->setData(
                    'woopra_estimate_post_zip',
                    $this->escaper->escapeHtml($request['estimate_postcode'])
                );
            }
        }

        if ($this->request->getFullActionName() === 'cms_noroute_index' &&
            $this->dataHelper->getCmsNoRoute() != null) {
            $request = $this->request->getOriginalPathInfo();
            $this->customerSession->setData('woopra_cms_noroute_trigger', 1);
            if ($request) {
                $this->customerSession->setData('woopra_cms_noroute_path', $request);
            }
        }

        if ($this->request->getFullActionName() === 'sendfriend_product_sendmail' &&
            $this->dataHelper->getProductEmailToFriend() != null) {
            $request = $this->request->getParams();
            if ($request) {
                $product = $this->productRepository->getById($request['id'])->getData();
                $this->customerSession->setData('woopra_sendfriend_product_name', $product['name']);
                $this->customerSession->setData('woopra_sendfriend_product_sku', $product['sku']);
                $this->customerSession->setData(
                    'woopra_sendfriend_product_price',
                    round($product['price'], 2)
                );
                $this->customerSession->setData('woopra_sendfriend_product_trigger', 1);
            }
        }
    }
}
