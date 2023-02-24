<?php
/**
 * @author Elsner Team
 * @copyright Copyright © Elsner Technologies Pvt. Ltd (https://www.elsner.com/)
 * @package Elsnertech_Paytrace
 */

namespace Elsnertech\Paytrace\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Payment\Helper\Data as PaymentHelper;
use Elsnertech\Paytrace\Model\Api\Api;

class PaytraceConfigProvider implements ConfigProviderInterface
{
    /**
     * @var string[]
     */
    protected $_paytreApi;

    /**
     * @var string[]
     */
    protected $_methodCode = Paytrace::PAYMENT_METHOD_APYTRACE_CODE;

    /**
     * @param Api $paytreApi
     */
    public function __construct(
        Api $paytreApi
    ) {
        $this->_paytreApi = $paytreApi;
    }

    /**
     * Get Config function to return cofig data to payment renderer.
     *
     * @return []
     */
    public function getConfig()
    {
        /**
         * $config array to pass config data to payment renderer component.
         *
         * @var array
         */
        $config = [
            'payment' => [
                'paytrace' => [
                    'paytrace_vault' => $this->getVaultEnable(),
                    'captcha_enable' => $this->getCaptchaEnable()
                ],
            ],
        ];

        return $config;
    }

    /**
     * Get Vault Enable .
     *
     * @return boolean
     */
    public function getVaultEnable()
    {
        
        if ($this->_paytreApi->getCustomerSession()->isLoggedIn() && $this->_paytreApi->getPaytraceVaultEnable()) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Is Captcha enable .
     *
     * @return boolean
     */
    public function getCaptchaEnable()
    {
        
        $value = $this->_paytreApi->getPaytraceCaptchaEnable();
        return ($value)?$value:0;
    }

    /**
     * GetSavedCards function to get customers cards json data.
     *
     * @return json
     */
    public function getSavedCards()
    {
        $cardsArray = [];
        $cards = $this->_paytreApi->getSavedCards();
        if ($cards) {
            foreach ($cards as $card) {
                    array_push(
                        $cardsArray,
                        [
                            'exp_month' => $card->getCcMonth(),
                            'exp_year' => $card->getCcYear(),
                            'paytrace_customer_id' => $card->getPaytraceCustomerId(),
                            'last4' => '****'.$card->getCcNumber(),
                        ]
                    );
            }
        }

        return json_encode($cardsArray, true);
    }
}
