<?php

namespace Bluethink\AdminGrid\Controller\Adminhtml\Index;

class NewProfile extends \Magento\Backend\App\Action
{
    protected $_pageFactory;

    protected $scopeConfig;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if (!$this->scopeConfig->getValue('bluethink/admingrid/enable', $scopeType)) {
            return $this;
        }
        return $this->_pageFactory->create();
    }
}
