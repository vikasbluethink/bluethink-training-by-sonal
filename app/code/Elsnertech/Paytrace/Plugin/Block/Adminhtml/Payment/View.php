<?php
/**
 * @author Elsner Team
 * @copyright Copyright © Elsner Technologies Pvt. Ltd (https://www.elsner.com/)
 * @package Elsnertech_Paytrace
 */

namespace Elsnertech\Paytrace\Plugin\Block\Adminhtml\Payment;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Registry;
use Magento\Backend\Block\Widget\Button\Toolbar\Interceptor;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Backend\Block\Widget\Button\ButtonList;

class View
{
    /**
     * @param UrlInterface $backendUrl
     * @param Registry $registry
     */
    public function __construct(
        UrlInterface $backendUrl,
        Registry $registry
    ) {
        $this->_backendUrl = $backendUrl;
        $this->_coreRegistry = $registry;
    }

    /**
     * Check order status in Paytrace
     *
     * @param Interceptor $subject
     * @param AbstractBlock $context
     * @param ButtonList $buttonList
     * @return $this
     */
    public function beforePushButtons(
        Interceptor $subject,
        AbstractBlock $context,
        ButtonList $buttonList
    ) {
        $this->_request = $context->getRequest();
        if ($this->_request->getFullActionName() == 'sales_order_view') {
            $transactionId = $this->getOrderTransaction();
            if ($transactionId) {
                $order = $this->_coreRegistry->registry('sales_order');
                $backendurl = $this->_backendUrl->getUrl(
                    "paytrace/transaction/statuscheck/",
                    ['transaction_id'=> $transactionId,
                    'order_id'=>$order->getId()]
                );
                $buttonList->add(
                    'paytrace_status',
                    [
                        'label' => __('Paytrace Status'),
                        'class' => 'paytrace-class',
                        'onclick' => 'setLocation(\'' . $backendurl . '\')'
                    ]
                );
            }
        }
    }

    /**
     * Get Order Transection Id
     *
     * @return string
     */
    public function getOrderTransaction()
    {
        $order = $this->_coreRegistry->registry('sales_order');
        if ($order->getId() && $order->getPayment() &&
            ($order->getPayment()->getMethod()=='paytrace' ||
                $order->getPayment()->getMethod()=='paytracevault')
        ) {
            return $order->getPayment()->getLastTransId();
        }
    }
}
