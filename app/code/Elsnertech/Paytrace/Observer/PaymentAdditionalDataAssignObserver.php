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

class PaymentAdditionalDataAssignObserver extends AbstractDataAssignObserver
{

    /**
     * This should card set
     */
    public const MY_FIELD_NAME_INDEX = 'paytrace_vault';

    /**
     * This should captcha sting
     */
    public const MY_FIELD_CAPTCHA_INDEX = 'captcha_string';

    /**
     * Construct method
     *
     * @param Data $helper
     * @param Config $paytraceConfig
     * @param Json $jsonSerializer
     * @param ActionFlag $actionFlag
     * @param State $state
     */
    public function __construct(
        Data $helper,
        Config $paytraceConfig,
        Json $jsonSerializer,
        ActionFlag $actionFlag,
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
        
            $data = $this->readDataArgument($observer);
            $additionalData = $data->getData(
                PaymentInterface::KEY_ADDITIONAL_DATA
            );
        if (!is_array($additionalData) ||
                !isset($additionalData[self::MY_FIELD_NAME_INDEX])
            ) {
            return; // or throw exception depending on your logic
        }

            $paymentInfo = $this->readPaymentModelArgument($observer);
            $paymentInfo->setAdditionalInformation(
                self::MY_FIELD_NAME_INDEX,
                $additionalData[self::MY_FIELD_NAME_INDEX]
            );
        if ($this->state->getAreaCode() == 'webapi_rest') {
            if (isset($additionalData[self::MY_FIELD_CAPTCHA_INDEX]) &&
               $additionalData[self::MY_FIELD_CAPTCHA_INDEX] == 'no-string'
            ) {
                return;
            }
            if ($this->paytraceConfig->getPaytraceVaultCaptchaEnable()) {

                $paymentInfo->setAdditionalInformation(
                    self::MY_FIELD_CAPTCHA_INDEX,
                    $additionalData[self::MY_FIELD_CAPTCHA_INDEX]
                );
                $this->checkValidation($additionalData);
            }
        }
    }

    /**
     * Check captcha validation
     *
     * @param array $additionalData
     * @return boolean
     */
    public function checkValidation($additionalData)
    {
        $formId = 'co-payment-form';
        $captcha = $this->captchaHelper->getCaptcha($formId);
        
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
