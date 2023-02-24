<?php
/**
 * @author Elsner Team
 * @copyright Copyright © Elsner Technologies Pvt. Ltd (https://www.elsner.com/)
 * @package Elsnertech_Paytrace
 */

namespace Elsnertech\Paytrace\Controller\SaveCard;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Request\Http;
use Elsnertech\Paytrace\Model\Customers;
use Magento\Framework\Message\ManagerInterface;
use Elsnertech\Paytrace\Model\Api\Api;

class DeleteCard extends AbstractAccount
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Elsnertech\Paytrace\Model\Customers
     */
    protected $_paytraceCollection;

    /**
     * @var \Elsnertech\Paytrace\Model\Api\Api
     */
    protected $_paytraceApi;
    
    /**
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param Http $request
     * @param Customers $paytraceCollection
     * @param ManagerInterface $messageManager
     * @param Api $paytraceApi
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Http $request,
        Customers $paytraceCollection,
        ManagerInterface $messageManager,
        Api $paytraceApi
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_request = $request;
        $this->_paytraceCollection = $paytraceCollection;
        $this->_messageManager = $messageManager;
        $this->_paytraceApi = $paytraceApi;
        return parent::__construct($context);
    }

    /**
     * Deleted Saved card
     *
     * @return json
     */
    public function execute()
    {

        $customerId = $this->_request->getParam('customer_id');
        $entityId = $this->_request->getParam('entity_id');
        $customerId = $this->_paytraceApi->decryptText($customerId);
        try {
            $deleteCustomer = $this->_paytraceApi->deleteCustomerProfile(
                $customerId
            );
            if ($deleteCustomer['success'] == true &&
                $deleteCustomer['response_code'] == 162 &&
                $deleteCustomer['customer_id'] == $customerId
            ) {
                $removeCard = $this->_paytraceCollection;
                $remove = $removeCard->setEntityId($entityId);
                $removeSavedCard = $remove->delete();
                $result['message'] = "You successfully removed card.";
                $result['error'] = false ;
            } else {
                $result['error'] = true ;
                $result['message'] = "Something went wrong please try again.";
            }
        } catch (\Exception $e) {
            $result['error'] = true ;
            $result['message'] = __(
                'Error appeared while check paytrace status %1',
                $e->getMessage()
            );
        }

        $resultJson = $this->resultFactory->create(
            ResultFactory::TYPE_JSON
        );
        $resultJson->setData($result);
        return $resultJson;
    }
}
