<?php
namespace Syncitgroup\Sgform\Controller\Group;

class Form extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

    protected $customerSession;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->customerSession = $customerSession;
        return parent::__construct($context);
    }

    public function execute()
    {
        if ($this->customerSession->isLoggedIn()) {
            return $this->_pageFactory->create();
        }
        return $this->resultRedirectFactory->create()->setPath('customer/account/login');
    }
}
