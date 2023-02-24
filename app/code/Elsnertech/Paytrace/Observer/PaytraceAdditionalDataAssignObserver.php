<?php
/**
 * @author Elsner Team
 * @copyright Copyright © Elsner Technologies Pvt. Ltd (https://www.elsner.com/)
 * @package Elsnertech_Paytrace
 */

namespace Elsnertech\Paytrace\Observer;

use Magento\Framework\Event\Observer;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\ActionFlag ;
use Elsnertech\Paytrace\Model\Api\Config;
use Magento\Captcha\Helper\Data;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;

class PaytraceAdditionalDataAssignObserver extends AbstractDataAssignObserver
{
    /**
     * This should card set
     */
    public const MY_FIELD_NAME_INDEX = 'is_saved';

    /**
     * This should captcha sting
     */
    public const MY_FIELD_CAPTCHA_INDEX = 'captcha_string';

    /**
     * @param Data $helper
     * @param Json $jsonSerializer
     * @param ActionFlag $actionFlag
     * @param Config $paytraceConfig
     * @param State $state
     */
    public function __construct(
        Data $helper,
        Json $jsonSerializer,
        ActionFlag $actionFlag,
        Config $paytraceConfig,
        State $state
    ) {
        $this->captchaHelper = $helper;
        $this->paytraceConfig = $paytraceConfig;
        $this->jsonSerializer = $jsonSerializer;
        $this->actionFlag = $actionFlag;
        $this->state = $state;
    }
    
    /**
     * Data assign event for paytrace
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->state->getAreaCode() == 'webapi_rest') {
            $data = $this->readDataArgument($observer);
            $additionalData = $data->getData(
                PaymentInterface::KEY_ADDITIONAL_DATA
            );
            if (isset($additionalData[self::MY_FIELD_CAPTCHA_INDEX]) &&
                 $additionalData[self::MY_FIELD_CAPTCHA_INDEX] == 'no-string') {
                return;
            }
            if ($this->paytraceConfig->getPaytraceCaptchaEnable()) {
                $this->checkValidation($additionalData, $observer);
            }
        }
    }

    /**
     * Check captcha validation
     *
     * @param AdditionalData $additionalData
     * @param Observer $observer
     * @return boolean
     */
    public function checkValidation($additionalData, $observer)
    {
        $formId = 'co-payment-form';
        $captcha = $this->captchaHelper->getCaptcha($formId);
        if (isset($additionalData[self::MY_FIELD_CAPTCHA_INDEX])) {
            $word = $additionalData[self::MY_FIELD_CAPTCHA_INDEX];
        
            if ($captcha->isCorrect($word)) {
                return;
            }

            $data = $this->jsonSerializer->serialize([
                'success' => false,
                'error' => true,
                'error_messages' => __('Incorrect CAPTCHA.')
            ]);
            
            throw new LocalizedException(__('Incorrect CAPTCHA.'));
        }
    }
}
