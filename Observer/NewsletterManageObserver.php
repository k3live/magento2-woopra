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

class NewsletterManageObserver implements ObserverInterface
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
        if ($this->dataHelper->getNewsletterSubscribed() != null) {
            $event = $observer->getEvent();
            $model = $event->getSubscriber();
            $subscriberEmail = $model->getData('subscriber_email');
            if ($model->getIsStatusChanged() == 1 && $model->getData('subscriber_status') == 1) {
                $this->customerSession->setData('woopra_subscriber_changed', 1);
                $this->customerSession->setData(
                    'woopra_subscriber_status',
                    $this->dataHelper->getNewsletterSubscribed()
                );
                $this->customerSession->setData(
                    'woopra_subscriber_email',
                    $this->escapeHtml($subscriberEmail)
                );
            } elseif ($model->getIsStatusChanged() == 1 && $model->getData('subscriber_status') == 3) {
                $this->customerSession->setData('woopra_subscriber_changed', 1);
                $this->customerSession->setData(
                    'woopra_subscriber_status',
                    $this->dataHelper->getNewsletterUnsubscribed()
                );
                $this->customerSession->setData(
                    'woopra_subscriber_email',
                    $this->escapeHtml($subscriberEmail)
                );
            }
        }
    }
}
