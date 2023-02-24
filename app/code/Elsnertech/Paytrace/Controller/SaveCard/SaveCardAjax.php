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
use Elsnertech\Paytrace\Model\Paytracevault;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;

class SaveCardAjax extends AbstractAccount
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    /**
     * @param Context $context
     * @param Paytracevault $paytraceVault
     * @param HttpContext $httpContext
     * @param Repository $assetRepo
     * @param RequestInterface $request
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        Paytracevault $paytraceVault,
        HttpContext $httpContext,
        Repository $assetRepo,
        RequestInterface $request,
        PageFactory $pageFactory
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_paytraceVault = $paytraceVault;
        $this->_httpContext = $httpContext;
        $this->assetRepo = $assetRepo;
        $this->request = $request;
        return parent::__construct($context);
    }

    /**
     * Get saved card data
     *
     * @return json
     */
    public function execute()
    {
        $resultPage = $this->_pageFactory->create();
        $saveCard = $this->getSaveCard();
        $result['html'] = $this->createHtml($saveCard);
        $result['saveCard'] = $saveCard;
        $resultJson = $this->resultFactory->create(
            ResultFactory::TYPE_JSON
        );
        $resultJson->setData($result);
        return $resultJson;
    }

    /**
     * Create Html
     *
     * @param SavedCards $savedCards
     * @return string
     */
    public function createHtml($savedCards)
    {
        $params = ['_secure' => $this->request->isSecure()];
        
        $html = '';
        if (!empty($savedCards)) {
            foreach ($savedCards as $savedCard) {
                $html .= '<div class="row payment-card">
                          <div class="card-inner">
                          <div class="col card-image">';
                          $imagePath = $savedCard['card_image'];

                $html .='<img src="'.$this->assetRepo->getUrlWithParams(
                    'Magento_Payment::images/cc/'.$imagePath,
                    $params
                ).
                '" width="46" height="30" class="payment-icon">';
                $html .= '</div>';
                $html .='<div class="col card-number">
                            '.$savedCard['last4'].'
                            </div>';
                $html .='<div class="col action-delete">
                        <form class="form" id="remove-card-'.
                        $savedCard['entity_id'].
                        '" action="" method="post">
                    <input name="customer_id" value="'.
                    $savedCard['paytrace_customer_id'].
                    '" type="hidden"/>
                    <input name="entity_id" value="'.
                    $savedCard['entity_id'].
                    '" type="hidden"/>
                    <button type="button" class="action delete" 
                    onclick="getRemoveAction(this)">
                        <span>'.__("Remove").'</span>
                    </button>
                </form>
            </div></div></div>';
            }
        } else {
            $html .='<div class="row">
                        <div class="col not-found">
                            '. __("You don't have any cards yet, please add card details.").'
                        </div>
                    </div>';
        }
        return $html;
    }

    /**
     * Is login
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
     * Get save card
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
