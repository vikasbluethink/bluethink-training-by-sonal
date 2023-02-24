<?php
/**
 * @author Elsner Team
 * @copyright Copyright © Elsner Technologies Pvt. Ltd (https://www.elsner.com/)
 * @package Elsnertech_Paytrace
 */

namespace Elsnertech\Paytrace\Block;

use Magento\Framework\View\Element\Template;
use Elsnertech\Paytrace\Model\Paytracevault;
use Magento\Framework\App\Http\Context;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

class SaveCard extends Template
{
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $_httpContext;

    /**
     * @var \Elsnertech\Paytrace\Model\Paytracevault
     */
    protected $_paytraceVault;

    /**
     * @param Paytracevault $paytraceVault
     * @param Context $httpContext
     * @param TemplateContext $context
     * @param array $data
     */
    public function __construct(
        Paytracevault $paytraceVault,
        Context $httpContext,
        TemplateContext $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_paytraceVault = $paytraceVault;
        $this->_httpContext = $httpContext;
    }

    /**
     * Is Customer login
     *
     * @return string
     */
    public function isLoggedIn()
    {
        return $this->_httpContext->getValue(
            \Magento\Customer\Model\Context::CONTEXT_AUTH
        );
    }

    /**
     * Get saved card detail
     *
     * @return boolean|string
     */
    public function getSaveCard()
    {
        if ($this->isLoggedIn()) {
            $saveCard = $this->_paytraceVault->getSavedCards();
            if ($saveCard) {
                return $saveCard;
            } else {
                return false;
            }
        }
    }
}
