<?php

/**
 * Woopra Module for Magento
 *
 * @package   Woopra_Analytics
 * @author    K3Live for Woopra <support@woopra.com>
 * @copyright 2017 Copyright (c) Woopra (http://www.woopra.com/)
 * @license   Open Software License (OSL 3.0)
 */

namespace Woopra\Analytics\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ENABLED = 'woopra_analytics/woopra_basic/enabled';
    const HOSTNAME = 'woopra_analytics/woopra_basic/hostname';
    const TEST = 'woopra_analytics/woopra_basic/test';
    const SUBDOMAIN = 'woopra_analytics/woopra_advanced/subdomain';
    const VISITOR_TIMEOUT = 'woopra_analytics/woopra_advanced/visitor_timeout';
    const TRACK_URL_PARAMETERS = 'woopra_analytics/woopra_advanced/track_url_parameters';
    const TRACKING_COOKIE_EXPIRATION = 'woopra_analytics/woopra_advanced/tracking_cookie_expiration';
    const TRACKING_COOKIE_NAME = 'woopra_analytics/woopra_advanced/tracking_cookie_name';
    const TRACKING_COOKIE_DOMAIN = 'woopra_analytics/woopra_advanced/tracking_cookie_domain';
    const TRACKING_COOKIE_PATH = 'woopra_analytics/woopra_advanced/tracking_cookie_path';
    const PING = 'woopra_analytics/woopra_advanced/ping';
    const PING_INTERVAL = 'woopra_analytics/woopra_advanced/ping_interval';
    const DOWNLOAD_TRACKING = 'woopra_analytics/woopra_advanced/download_tracking';
    const DOWNLOAD_TRACKING_PAUSE = 'woopra_analytics/woopra_advanced/download_tracking_pause';
    const OUTGOING_TRACKING = 'woopra_analytics/woopra_advanced/outgoing_tracking';
    const OUTGOING_TRACKING_PAUSE = 'woopra_analytics/woopra_advanced/outgoing_tracking_pause';
    const OUTGOING_IGNORE_SUBDOMAIN = 'woopra_analytics/woopra_advanced/outgoing_ignore_subdomain';
    const HIDE_CAMPAIGN = 'woopra_analytics/woopra_advanced/hide_campaign';
    const NAME = 'woopra_analytics/woopra_outputs/name';
    const EMAIL = 'woopra_analytics/woopra_outputs/email';
    const COMPANY = 'woopra_analytics/woopra_outputs/company';
    const CUSTOMER_LOCATION = 'woopra_analytics/woopra_outputs/customer_location';
    const CUSTOMER_PHONE = 'woopra_analytics/woopra_outputs/customer_phone';
    const CUSTOMER_GROUP = 'woopra_analytics/woopra_outputs/customer_group';
    const CUSTOMER_LIFETIME_SALES = 'woopra_analytics/woopra_outputs/customer_lifetime_sales';
    const CUSTOMER_NUMBER_ORDERS = 'woopra_analytics/woopra_outputs/customer_number_orders';
    const CUSTOMER_CREATE_DATE = 'woopra_analytics/woopra_outputs/customer_create_date';
    const CUSTOMER_CART_ITEMS = 'woopra_analytics/woopra_outputs/customer_cart_items';
    const CUSTOMER_CART_TOTAL = 'woopra_analytics/woopra_outputs/customer_cart_total';
    const CUSTOMER_WISHLIST_ITEMS = 'woopra_analytics/woopra_outputs/customer_wishlist_items';
    const CUSTOMER_WISHLIST_TOTAL = 'woopra_analytics/woopra_outputs/customer_wishlist_total';
    const CATALOG_SEARCH = 'woopra_analytics/woopra_events/catalog_search';
    const CHANGED_PASSWORD = 'woopra_analytics/woopra_events/changed_password';
    const CHECKOUT_BILLING_ADDRESS = 'woopra_analytics/woopra_events/checkout_billing_address';
    const CHECKOUT_SHIPPING_ADDRESS = 'woopra_analytics/woopra_events/checkout_shipping_address';
    const CHECKOUT_SHIPPING_METHOD = 'woopra_analytics/woopra_events/checkout_shipping_method';
    const CHECKOUT_PAYMENT_METHOD = 'woopra_analytics/woopra_events/checkout_payment_method';
    const CHECKOUT_REVIEW = 'woopra_analytics/woopra_events/checkout_review';
    const CHECKOUT_SUCCESS = 'woopra_analytics/woopra_events/checkout_success';
    const CMS_NO_ROUTE = 'woopra_analytics/woopra_events/cms_no_route';
    const CONTACT_FORM_SENT = 'woopra_analytics/woopra_events/contact_form_sent';
    const COUPON_ADDED = 'woopra_analytics/woopra_events/coupon_added';
    const COUPON_REMOVED = 'woopra_analytics/woopra_events/coupon_removed';
    const CUSTOMER_CREATE_ACCOUNT = 'woopra_analytics/woopra_events/customer_create_account';
    const CUSTOMER_CREATE_ACCOUNT_SUCCESS = 'woopra_analytics/woopra_events/customer_create_account_success';
    const CUSTOMER_LOGIN = 'woopra_analytics/woopra_events/customer_login';
    const CUSTOMER_LOGOUT = 'woopra_analytics/woopra_events/customer_logout';
    const ESTIMATE_POST = 'woopra_analytics/woopra_events/estimate_post';
    const FORGOT_PASSWORD = 'woopra_analytics/woopra_events/forgot_password';
    const NEWSLETTER_SUBSCRIBED = 'woopra_analytics/woopra_events/newsletter_subscribed';
    const NEWSLETTER_UNSUBSCRIBED = 'woopra_analytics/woopra_events/newsletter_unsubscribed';
    const PRODUCT_ADDED_TO_CART = 'woopra_analytics/woopra_events/product_added_to_cart';
    const PRODUCT_REMOVED_FROM_CART = 'woopra_analytics/woopra_events/product_removed_from_cart';
    const PRODUCT_ADDED_TO_COMPARE = 'woopra_analytics/woopra_events/product_added_to_compare';
    const PRODUCT_REMOVED_FROM_COMPARE = 'woopra_analytics/woopra_events/product_removed_from_compare';
    const PRODUCT_ADDED_TO_WISHLIST = 'woopra_analytics/woopra_events/product_added_to_wishlist';
    const PRODUCT_REMOVED_FROM_WISHLIST = 'woopra_analytics/woopra_events/product_removed_from_wishlist';
    const PRODUCT_PURCHASED = 'woopra_analytics/woopra_events/product_purchased';
    const PRODUCT_REVIEW_READ = 'woopra_analytics/woopra_events/product_review_read';
    const PRODUCT_REVIEW_POSTED = 'woopra_analytics/woopra_events/product_review_posted';
    const SENDFRIEND_PRODUCT = 'woopra_analytics/woopra_events/sendfriend_product';
    
    public function __construct(\Magento\Framework\App\Helper\Context $context)
    {
        parent::__construct($context);
    }
 
    public function getEnabled()
    {
        return $this->scopeConfig->getValue(self::ENABLED);
    }
    public function getHostname()
    {
        return $this->scopeConfig->getValue(self::HOSTNAME);
    }
    public function getTest()
    {
        return $this->scopeConfig->getValue(self::TEST);
    }
    public function getSubdomain()
    {
        return $this->scopeConfig->getValue(self::SUBDOMAIN);
    }
    public function getVistorTimeout()
    {
        return $this->scopeConfig->getValue(self::VISITOR_TIMEOUT);
    }
    public function getTrackUrlParameters()
    {
        return $this->scopeConfig->getValue(self::TRACK_URL_PARAMETERS);
    }
    public function getTrackingCookieExpiration()
    {
        return $this->scopeConfig->getValue(self::TRACKING_COOKIE_EXPIRATION);
    }
    public function getTrackingCookieName()
    {
        return $this->scopeConfig->getValue(self::TRACKING_COOKIE_NAME);
    }
    public function getTrackingCookieDomain()
    {
        return $this->scopeConfig->getValue(self::TRACKING_COOKIE_DOMAIN);
    }
    public function getTrackingCookiePath()
    {
        return $this->scopeConfig->getValue(self::TRACKING_COOKIE_PATH);
    }
    public function getPing()
    {
        return $this->scopeConfig->getValue(self::PING);
    }
    public function getPingInterval()
    {
        return $this->scopeConfig->getValue(self::PING_INTERVAL);
    }
    public function getDownloadTracking()
    {
        return $this->scopeConfig->getValue(self::DOWNLOAD_TRACKING);
    }
    public function getDownloadTrackingPause()
    {
        return $this->scopeConfig->getValue(self::DOWNLOAD_TRACKING_PAUSE);
    }
    public function getOutgoingTracking()
    {
        return $this->scopeConfig->getValue(self::OUTGOING_TRACKING);
    }
    public function getOutgoingTrackingPause()
    {
        return $this->scopeConfig->getValue(self::OUTGOING_TRACKING_PAUSE);
    }
    public function getOutgoingIgnoreSubdomain()
    {
        return $this->scopeConfig->getValue(self::OUTGOING_IGNORE_SUBDOMAIN);
    }
    public function getHideCampaign()
    {
        return $this->scopeConfig->getValue(self::HIDE_CAMPAIGN);
    }
    public function getCustomerName()
    {
        return $this->scopeConfig->getValue(self::NAME);
    }
    public function getCustomerEmail()
    {
        return $this->scopeConfig->getValue(self::EMAIL);
    }
    public function getCustomerCompany()
    {
        return $this->scopeConfig->getValue(self::COMPANY);
    }
    public function getCustomerLocation()
    {
        return $this->scopeConfig->getValue(self::CUSTOMER_LOCATION);
    }
    public function getCustomerPhone()
    {
        return $this->scopeConfig->getValue(self::CUSTOMER_PHONE);
    }
    public function getCustomerGroup()
    {
        return $this->scopeConfig->getValue(self::CUSTOMER_GROUP);
    }
    public function getCustomerLifetimeSales()
    {
        return $this->scopeConfig->getValue(self::CUSTOMER_LIFETIME_SALES);
    }
    public function getCustomerNumberOrders()
    {
        return $this->scopeConfig->getValue(self::CUSTOMER_NUMBER_ORDERS);
    }
    public function getCustomerCreateDate()
    {
        return $this->scopeConfig->getValue(self::CUSTOMER_CREATE_DATE);
    }
    public function getCustomerCartItems()
    {
        return $this->scopeConfig->getValue(self::CUSTOMER_CART_ITEMS);
    }
    public function getCustomerCartTotal()
    {
        return $this->scopeConfig->getValue(self::CUSTOMER_CART_TOTAL);
    }
    public function getCustomerWishlistItems()
    {
        return $this->scopeConfig->getValue(self::CUSTOMER_WISHLIST_ITEMS);
    }
    public function getCustomerWishlistTotal()
    {
        return $this->scopeConfig->getValue(self::CUSTOMER_WISHLIST_TOTAL);
    }
    public function getCatalogSearch()
    {
        return $this->scopeConfig->getValue(self::CATALOG_SEARCH);
    }
    public function getChangedPassword()
    {
        return $this->scopeConfig->getValue(self::CHANGED_PASSWORD);
    }
    public function getCheckoutBillingAddress()
    {
        return $this->scopeConfig->getValue(self::CHECKOUT_BILLING_ADDRESS);
    }
    public function getCheckoutShippingAddress()
    {
        return $this->scopeConfig->getValue(self::CHECKOUT_SHIPPING_ADDRESS);
    }
    public function getCheckoutShippingMethod()
    {
        return $this->scopeConfig->getValue(self::CHECKOUT_SHIPPING_METHOD);
    }
    public function getCheckoutPaymentMethod()
    {
        return $this->scopeConfig->getValue(self::CHECKOUT_PAYMENT_METHOD);
    }
    public function getCheckoutReview()
    {
        return $this->scopeConfig->getValue(self::CHECKOUT_REVIEW);
    }
    public function getCheckoutSuccess()
    {
        return $this->scopeConfig->getValue(self::CHECKOUT_SUCCESS);
    }
    public function getCmsNoRoute()
    {
        return $this->scopeConfig->getValue(self::CMS_NO_ROUTE);
    }
    public function getContactFormSent()
    {
        return $this->scopeConfig->getValue(self::CONTACT_FORM_SENT);
    }
    public function getCouponCodeAdded()
    {
        return $this->scopeConfig->getValue(self::COUPON_ADDED);
    }
    public function getCouponCodeRemoved()
    {
        return $this->scopeConfig->getValue(self::COUPON_REMOVED);
    }
    public function getCustomerCreateAccount()
    {
        return $this->scopeConfig->getValue(self::CUSTOMER_CREATE_ACCOUNT);
    }
    public function getCustomerCreateAccountSuccess()
    {
        return $this->scopeConfig->getValue(self::CUSTOMER_CREATE_ACCOUNT_SUCCESS);
    }
    public function getCustomerLogin()
    {
        return $this->scopeConfig->getValue(self::CUSTOMER_LOGIN);
    }
    public function getCustomerLogout()
    {
        return $this->scopeConfig->getValue(self::CUSTOMER_LOGOUT);
    }
    public function getEstimatePost()
    {
        return $this->scopeConfig->getValue(self::ESTIMATE_POST);
    }
    public function getForgotPassword()
    {
        return $this->scopeConfig->getValue(self::FORGOT_PASSWORD);
    }
    public function getNewsletterSubscribed()
    {
        return $this->scopeConfig->getValue(self::NEWSLETTER_SUBSCRIBED);
    }
    public function getNewsletterUnsubscribed()
    {
        return $this->scopeConfig->getValue(self::NEWSLETTER_UNSUBSCRIBED);
    }
    public function getProductAddedToCart()
    {
        return $this->scopeConfig->getValue(self::PRODUCT_ADDED_TO_CART);
    }
    public function getProductRemovedFromCart()
    {
        return $this->scopeConfig->getValue(self::PRODUCT_REMOVED_FROM_CART);
    }
    public function getProductAddedToCompare()
    {
        return $this->scopeConfig->getValue(self::PRODUCT_ADDED_TO_COMPARE);
    }
    public function getProductRemovedFromCompare()
    {
        return $this->scopeConfig->getValue(self::PRODUCT_REMOVED_FROM_COMPARE);
    }
    public function getProductAddedToWishlist()
    {
        return $this->scopeConfig->getValue(self::PRODUCT_ADDED_TO_WISHLIST);
    }
    public function getProductRemovedFromWishlist()
    {
        return $this->scopeConfig->getValue(self::PRODUCT_REMOVED_FROM_WISHLIST);
    }
    public function getProductPurchased()
    {
        return $this->scopeConfig->getValue(self::PRODUCT_PURCHASED);
    }
    public function getProductReviewRead()
    {
        return $this->scopeConfig->getValue(self::PRODUCT_REVIEW_READ);
    }
    public function getProductReviewPosted()
    {
        return $this->scopeConfig->getValue(self::PRODUCT_REVIEW_POSTED);
    }
    public function getProductEmailToFriend()
    {
        return $this->scopeConfig->getValue(self::SENDFRIEND_PRODUCT);
    }
}
