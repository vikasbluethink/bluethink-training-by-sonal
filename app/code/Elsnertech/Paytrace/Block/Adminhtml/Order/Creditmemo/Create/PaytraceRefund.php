<?php
/**
 * @author Elsner Team
 * @copyright Copyright © Elsner Technologies Pvt. Ltd (https://www.elsner.com/)
 * @package Elsnertech_Paytrace
 */

namespace Elsnertech\Paytrace\Block\Adminhtml\Order\Creditmemo\Create;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;
use Elsnertech\Paytrace\Model\Api\Api;

class PaytraceRefund extends Template
{
    
    /**
     * @param Context $context
     * @param Registry $registry
     * @param Api $api
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Api $api,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $registry;
        $this->_api = $api;
    }
    
    /**
     * Get current credit memo object
     *
     * @return object
     */
    public function getCreditmemo()
    {
        return $this->_coreRegistry->registry('current_creditmemo');
    }
    
    /**
     * Get order transection detail
     *
     * @return object
     */
    public function getOrderTransaction()
    {
        if ($this->getCreditmemo()->getInvoice()
            && ($this->getCreditmemo()
                ->getOrder()
                ->getPayment()
                ->getMethod()=='paytrace'
                || $this->getCreditmemo()
                ->getOrder()
                ->getPayment()
                ->getMethod()=='paytracevault')
        ) {
            return $this->getCreditmemo()
            ->getInvoice()
            ->getTransactionId();
        }
    }

    /**
     * Get order status detail
     *
     * @return []
     */
    public function getOrderStatus()
    {
        $transactionId = $this->_api->getValidTransectionId(
            $this->getOrderTransaction()
        );
        if ($transactionId) {
            return $data = $this->_api->getStatusByTransecion(
                $transactionId
            );
        }
        return [];
    }
}
