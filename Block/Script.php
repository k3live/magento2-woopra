<?php

/**
 * Woopra Module for Magento
 *
 * @package   Woopra_Analytics
 * @author    K3Live for Woopra <support@woopra.com>
 * @copyright 2017 Copyright (c) Woopra (http://www.woopra.com/)
 * @license   Open Software License (OSL 3.0)
 */

namespace Woopra\Analytics\Block;

use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Cart;
use Magento\Customer\Model\Group as CustomerGroup;
use Magento\Checkout\Model\Session\Proxy as CheckoutSession;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Sale\Collection as SaleCollection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Woopra\Analytics\Helper\Data;

class Script extends \Magento\Framework\View\Element\Template
{
    private $checkoutCart;
    public $checkoutSession;
    private $context;
    private $customerGroup;
    private $customerSession;
    private $dataHelper;
    public $escaper;
    public $order;
    public $productRepository;
    private $registry;
    private $saleCollection;
    private $salesOrderCollection;
    private $wishlistProvider;

    public function __construct(
        Context $context,
        Cart $checkoutCart,
        CheckoutSession $checkoutSession,
        CollectionFactory $salesOrderCollection,
        CustomerGroup $customerGroup,
        CustomerSession $customerSession,
        Data $dataHelper,
        Order $order,
        ProductRepository $productRepository,
        Registry $registry,
        SaleCollection $saleCollection,
        WishlistProviderInterface $wishlistProvider,
        array $data = []
    ) {
        $this->cart = $checkoutCart;
        $this->checkoutSession = $checkoutSession;
        $this->customerGroup = $customerGroup;
        $this->customerSession = $customerSession;
        $this->dataHelper = $dataHelper;
        $this->escaper = $context->getEscaper();
        $this->order = $order;
        $this->productRepository = $productRepository;
        $this->registry = $registry;
        $this->saleCollection = $saleCollection;
        $this->salesOrderCollection = $salesOrderCollection;
        $this->wishlistProvider = $wishlistProvider;

        parent::__construct($context, $data);
    }
    
    public function getSetting($key = null)
    {
        static $data;
        if (empty($data)) {
            $data = [
                'enabled' => $this->dataHelper->getEnabled(),
                'test' => $this->dataHelper->getTest(),
                'visitor_timeout' => $this->dataHelper->getVistorTimeout(),
                'track_url_parameters' => $this->dataHelper->getTrackUrlParameters(),
                'hostname' => $this->dataHelper->getHostname(),
                'subdomain' => $this->dataHelper->getSubdomain(),
                'tracking_cookie_expiration' => $this->dataHelper->getTrackingCookieExpiration(),
                'tracking_cookie_name' => $this->dataHelper->getTrackingCookieName(),
                'tracking_cookie_domain' => $this->dataHelper->getTrackingCookieDomain(),
                'tracking_cookie_path' => $this->dataHelper->getTrackingCookiePath(),
                'ping' => $this->dataHelper->getPing(),
                'ping_interval' => $this->dataHelper->getPingInterval(),
                'download_tracking' => $this->dataHelper->getDownloadTracking(),
                'download_tracking_pause' => $this->dataHelper->getDownloadTrackingPause(),
                'outgoing_tracking' => $this->dataHelper->getOutgoingTracking(),
                'outgoing_tracking_pause' => $this->dataHelper->getOutgoingTrackingPause(),
                'outgoing_ignore_subdomain' => $this->dataHelper->getOutgoingIgnoreSubdomain(),
                'hide_campaign' => $this->dataHelper->getHideCampaign(),
                'customer_name_output' => $this->dataHelper->getCustomerName(),
                'customer_email_output' => $this->dataHelper->getCustomerEmail(),
                'customer_company_output' => $this->dataHelper->getCustomerCompany(),
                'customer_location_output' => $this->dataHelper->getCustomerLocation(),
                'customer_phone_output' => $this->dataHelper->getCustomerPhone(),
                'customer_group_output' => $this->dataHelper->getCustomerGroup(),
                'customer_lifetime_sales_output' => $this->dataHelper->getCustomerLifetimeSales(),
                'customer_number_orders_output' => $this->dataHelper->getCustomerNumberOrders(),
                'customer_create_date_output' => $this->dataHelper->getCustomerCreateDate(),
                'customer_cart_items_output' => $this->dataHelper->getCustomerCartItems(),
                'customer_cart_total_output' => $this->dataHelper->getCustomerCartTotal(),
                'customer_wishlist_items_output' => $this->dataHelper->getCustomerWishlistItems(),
                'customer_wishlist_total_output' => $this->dataHelper->getCustomerWishlistTotal()
            ];

            $customer = $this->customerSession->getCustomer();
            if (!empty($customer)) {
                if ($customer->getName() != ' ' && $this->dataHelper->getCustomerName() != null) {
                    $data['customer_name'] = $this->_escaper->escapeQuote($customer->getName());
                }
                if ($this->dataHelper->getCustomerEmail() != null) {
                    $data['customer_email'] = $customer->getEmail();
                }

                $address = $customer->getDefaultBillingAddress();
                if (!empty($address)) {
                    $address = $customer->getDefaultShippingAddress();
                }
                if (!empty($address)) {
                    if ($this->dataHelper->getCustomerCompany() != null) {
                        $data['customer_company'] = $this->_escaper->escapeQuote($address->getCompany());
                    }
                    if ($this->dataHelper->getCustomerLocation() != null) {
                        $data['customer_location'] =
                            $this->_escaper->escapeQuote($address->getCity()) . ', ' .
                            $this->_escaper->escapeQuote($address->getRegion()) .
                            ' (' . $address->getCountryId() . ')';
                    }
                    if ($this->dataHelper->getCustomerPhone() != null) {
                        $data['customer_phone'] = $this->_escaper->escapeQuote($address->getTelephone());
                    }
                }

                if ($this->dataHelper->getCustomerGroup() != null) {
                    $groupId = $this->customerSession->getCustomerGroupId();
                    $this->customerGroup->load($groupId);
                    $group = $this->customerGroup->getCode();
                    $data['customer_group'] = $this->_escaper->escapeQuote($group);
                }

                if ($this->dataHelper->getCustomerCreateDate() != null) {
                    $date = $customer->getCreatedAtTimestamp();
                    if ($date != 0) {
                        $data['customer_create_date'] = $this->_escaper->escapeQuote(date('m-d-Y H:i', $date));
                    }
                }

                $customerTotals = $this->saleCollection
                    ->setOrderStateFilter(\Magento\Sales\Model\Order::STATE_CANCELED, true)
                    ->setCustomerIdFilter($customer->getId())
                    ->load()
                    ->getTotals();
                if ($this->dataHelper->getCustomerLifetimeSales() != null && $group != 'NOT LOGGED IN') {
                    $data['customer_lifetime_sales'] = round($customerTotals->getLifetime(), 2);
                }
                if ($this->dataHelper->getCustomerNumberOrders() != null && $group != 'NOT LOGGED IN') {
                    $data['customer_number_orders'] = $customerTotals->getNumOrders();
                }

                $wishListCollection = $this->wishlistProvider->getWishlist()->getItemCollection();
                $wishlistItems = 0;
                $wishListTotal = 0;
                foreach ($wishListCollection as $item) {
                    $product = $item->getProduct();
                    $wishlistItems = $wishlistItems ++;
                    $wishListTotal = $wishListTotal + $product->getPrice();
                }
                if ($this->dataHelper->getCustomerWishlistItems() != null) {
                    $data['customer_wishlist_items'] = $wishlistItems;
                }
                if ($this->dataHelper->getCustomerWishlistTotal() != null) {
                    $data['customer_wishlist_total'] = $wishListTotal;
                }
            }

            if ($this->dataHelper->getCustomerCartItems() != null) {
                $data['customer_cart_items'] = $this->cart->getQuote()->getItemsCount();
            }
            if ($this->dataHelper->getCustomerCartTotal() != null) {
                $data['customer_cart_total'] = round($this->cart->getQuote()->getGrandTotal(), 2);
            }
            $currentCategory = $this->registry->registry('current_category');
            if (!empty($currentCategory)) {
                $data['category'] = $this->_escaper->escapeQuote($currentCategory->getName());
            }
            $currentProduct = $this->registry->registry('currentProduct');
            if (!empty($currentProduct)) {
                $data['product_sku'] = $this->_escaper->escapeQuote($currentProduct->getSku());
                $data['product_price'] = strip_tags($currentProduct->getPrice());
            }

            if ($this->dataHelper->getNewsletterSubscribed() != null) {
                $data['woopra_subscriber_changed'] = $this->customerSession
                    ->getData('woopra_subscriber_changed', true);
                $data['woopra_subscriber_status'] = $this->customerSession
                    ->getData('woopra_subscriber_status', true);
                $data['woopra_subscriber_email'] = $this->customerSession
                    ->getData('woopra_subscriber_email', true);
            }

            if ($this->dataHelper->getContactFormSent() != null) {
                $data['woopra_contacts_index_post'] = $this->customerSession
                    ->getData('woopra_contacts_index_post', true);
                $data['woopra_contacts_name'] = $this->customerSession
                    ->getData('woopra_contacts_name', true);
                $data['woopra_contacts_email'] = $this->customerSession
                    ->getData('woopra_contacts_email', true);
                $data['woopra_contacts_telephone'] = $this->customerSession
                    ->getData('woopra_contacts_telephone', true);
                $data['woopra_contacts_comment'] = $this->customerSession
                    ->getData('woopra_contacts_comment', true);
            }

            $data['woopra_cart_wishlist_trigger'] = $this->customerSession
                ->getData('woopra_cart_wishlist_trigger', true);
            $data['woopra_cart_wishlist_status'] = $this->customerSession
                ->getData('woopra_cart_wishlist_status', true);
            $data['woopra_cart_wishlist_name'] = $this->customerSession
                ->getData('woopra_cart_wishlist_name', true);
            $data['woopra_cart_wishlist_sku'] = $this->customerSession
                ->getData('woopra_cart_wishlist_sku', true);
            $data['woopra_cart_wishlist_price'] = $this->customerSession
                ->getData('woopra_cart_wishlist_price', true);

            if ($this->dataHelper->getCatalogSearch() != null) {
                $data['woopra_search_name'] = $this->dataHelper->getCatalogSearch();
                $data['woopra_search_trigger'] = $this->customerSession
                    ->getData('woopra_search_trigger', true);
                $data['woopra_search_keywords'] = $this->customerSession
                    ->getData('woopra_search_keywords', true);
            }

            if ($this->dataHelper->getCustomerCreateAccount() != null) {
                $data['woopra_create_account_trigger'] = $this->customerSession
                    ->getData('woopra_create_account_trigger', true);
            }

            if ($this->dataHelper->getCustomerCreateAccountSuccess() != null) {
                $data['woopra_create_account_success_trigger'] = $this->customerSession
                    ->getData('woopra_create_account_success_trigger', true);
            }

            if ($this->dataHelper->getCheckoutBillingAddress() != null) {
                $data['woopra_checkout_trigger'] = $this->customerSession
                    ->getData('woopra_checkout_trigger', true);
            }

            if ($this->dataHelper->getCheckoutSuccess() != null) {
                $data['woopra_checkout_payment_method'] = $this->customerSession
                    ->getData('woopra_checkout_payment_method', true);
                $data['woopra_checkout_payment_cc_type'] = $this->customerSession
                    ->getData('woopra_checkout_payment_cc_type', true);
                $data['woopra_checkout_success_trigger'] = $this->customerSession
                    ->getData('woopra_checkout_success_trigger', true);
                $data['woopra_checkout_success_coupon_code'] = $this->customerSession
                    ->getData('woopra_checkout_success_coupon_code', true);
                $data['woopra_checkout_success_discount_amount'] = $this->customerSession
                    ->getData('woopra_checkout_success_discount_amount', true);
                $data['woopra_checkout_success_order_id'] = $this->customerSession
                    ->getData('woopra_checkout_success_order_id', true);
                $data['woopra_checkout_success_order_subtotal'] = $this->customerSession
                    ->getData('woopra_checkout_success_order_subtotal', true);
                $data['woopra_checkout_success_order_total'] = $this->customerSession
                    ->getData('woopra_checkout_success_order_total', true);
                $data['woopra_checkout_success_order_weight'] = $this->customerSession
                    ->getData('woopra_checkout_success_order_weight', true);
                $data['woopra_checkout_success_shipping_amount'] = $this->customerSession
                    ->getData('woopra_checkout_success_shipping_amount', true);
                $data['woopra_checkout_success_shipping_description'] = $this->customerSession
                    ->getData('woopra_checkout_success_shipping_description', true);
                $data['woopra_checkout_success_total_items_ordered'] = $this->customerSession
                    ->getData('woopra_checkout_success_total_items_ordered', true);
                $data['woopra_checkout_success_profit'] = $this->customerSession
                    ->getData('woopra_checkout_success_profit', true);
                $data['woopra_checkout_success_guest_trigger'] = $this->customerSession
                    ->getData('woopra_checkout_success_guest_trigger', true);
                $data['woopra_checkout_success_guest_name'] = $this->customerSession
                    ->getData('woopra_checkout_success_guest_name', true);
                $data['woopra_checkout_success_guest_email'] = $this->customerSession
                    ->getData('woopra_checkout_success_guest_email', true);
            }

            if ($this->dataHelper->getCmsNoRoute() != null) {
                $data['woopra_cms_noroute_trigger'] = $this->customerSession
                    ->getData('woopra_cms_noroute_trigger', true);
                $data['woopra_cms_noroute_path'] = $this->customerSession
                    ->getData('woopra_cms_noroute_path', true);
                $data['woopra_cms_noroute_url'] = $this->_storeManager->getStore()->getCurrentUrl();
            }

            if ($this->dataHelper->getCouponCodeAdded() != null) {
                $data['woopra_coupon_code_trigger'] = $this->customerSession
                    ->getData('woopra_coupon_code_trigger', true);
                $data['woopra_coupon_code_status'] = $this->customerSession
                    ->getData('woopra_coupon_code_status', true);
                $data['woopra_coupon_code'] = $this->customerSession->getData('woopra_coupon_code', true);
                $data['woopra_coupon_code_validity'] = $this->customerSession
                    ->getData('woopra_coupon_code_validity', true);
                $data['woopra_coupon_code_active'] = $this->customerSession
                    ->getData('woopra_coupon_code_active', true);
                $data['woopra_coupon_code_name'] = $this->customerSession
                    ->getData('woopra_coupon_code_name', true);
            }

            if ($this->dataHelper->getCustomerLogin() != null) {
                $data['woopra_login_logout_trigger'] = $this->customerSession
                    ->getData('woopra_login_logout_trigger', true);
                $data['woopra_login_logout_status'] = $this->customerSession
                    ->getData('woopra_login_logout_status', true);
            }

            if ($this->dataHelper->getForgotPassword() != null) {
                $data['woopra_forgot_password_trigger'] = $this->customerSession
                    ->getData('woopra_forgot_password_trigger', true);
                $data['woopra_forgot_password_email'] = $this->customerSession
                    ->getData('woopra_forgot_password_email', true);
            }

            if ($this->dataHelper->getChangedPassword() != null) {
                $data['woopra_password_changed_trigger'] = $this->customerSession
                ->getData('woopra_password_changed_trigger', true);
            }

            if ($this->dataHelper->getProductReviewRead() != null && $this->customerSession
                    ->getData('woopra_product_review_trigger') == 1) {
                $data['woopra_cart_wishlist_status'] = 'product_review_posted';
                $data['woopra_product_review_trigger'] = $this->customerSession
                    ->getData('woopra_product_review_trigger', true);
                $data['woopra_product_review_nickname'] = $this->customerSession
                    ->getData('woopra_product_review_nickname', true);
                $data['woopra_product_review_title'] = $this->customerSession
                    ->getData('woopra_product_review_title', true);
                $data['woopra_product_review_detail'] = $this->customerSession
                    ->getData('woopra_product_review_detail', true);
            }

            if ($this->dataHelper->getEstimatePost() != null && $this->customerSession
                    ->getData('woopra_estimate_post_trigger') == 1) {
                $data['woopra_estimate_post_trigger'] = $this->customerSession
                    ->getData('woopra_estimate_post_trigger', true);
                $data['woopra_estimate_post_country'] = $this->customerSession
                    ->getData('woopra_estimate_post_country', true);
                $data['woopra_estimate_post_state'] = $this->customerSession
                    ->getData('woopra_estimate_post_state', true);
                $data['woopra_estimate_post_zip'] = $this->customerSession
                    ->getData('woopra_estimate_post_zip', true);
            }

            if ($this->dataHelper->getProductEmailToFriend() != null) {
                $data['woopra_sendfriend_product_trigger'] = $this->customerSession
                    ->getData('woopra_sendfriend_product_trigger', true);
                $data['woopra_sendfriend_product_name'] = $this->customerSession
                    ->getData('woopra_sendfriend_product_name', true);
                $data['woopra_sendfriend_product_sku'] = $this->customerSession
                    ->getData('woopra_sendfriend_product_sku', true);
                $data['woopra_sendfriend_product_price'] = $this->customerSession
                    ->getData('woopra_sendfriend_product_price', true);
            }
        }

        if (isset($data[$key])) {
            return $data[$key];
        } else {
            return null;
        }
    }

    public function getCheckoutDetails()
    {
        if ($this->dataHelper->getProductPurchased() != null &&
            $this->getSetting('woopra_checkout_success_trigger') == 1) {
            $orderIds = $this->checkoutSession->getLastRealOrderId();
            if (empty($orderIds)) {
                return;
            }

            $collection = $this->salesOrderCollection->create();
            $collection->addFieldToFilter('entity_id', ['in' => $orderIds]);
            $result = [];

            foreach ($collection as $order) {
                if ($order->getIsVirtual()) {
                    $address = $order->getBillingAddress();
                } else {
                    $address = $order->getShippingAddress();
                }

                foreach ($order->getAllVisibleItems() as $item) {
                    $result[] = sprintf(
                        "    woopra.track({
        name: '".$this->escaper->escapeJsQuote($this->dataHelper->getProductPurchased())."',
        product_sku: '%s',
        product_name: '%s',
        product_price: '%s',
        product_qty: '%s'
    });",
                        $this->escapeJsQuote($item->getSku()),
                        $this->escapeJsQuote($item->getName()),
                        round($item->getBasePrice(), 2),
                        round($item->getQtyOrdered(), 2)
                    );
                }
            }
            return implode("\n", $result);
        }
    }
}
