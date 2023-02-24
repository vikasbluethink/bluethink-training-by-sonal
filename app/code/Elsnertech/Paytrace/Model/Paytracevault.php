<?php
/**
 * @author Elsner Team
 * @copyright Copyright © Elsner Technologies Pvt. Ltd (https://www.elsner.com/)
 * @package Elsnertech_Paytrace
 */

namespace Elsnertech\Paytrace\Model;

use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\DataObject;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\Quote\Payment;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Payment\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order\Payment\TransactionFactory;
use Magento\Payment\Model\Method\Logger;
use Elsnertech\Paytrace\Model\Api\Api;
use Magento\Payment\Model\InfoInterface;

class Paytracevault extends AbstractMethod
{
    /**
     * Method code
     */
    public const PAYMENT_METHOD_APYTRACEVAULT_CODE = 'paytracevault';

    /**
     * @var boolean
     */
    protected $_canAuthorize = true;

    /**
     * @var boolean
     */
    protected $_canCapture = true;

    /**
     * @var boolean
     */
    protected $_isGateway                   = true;

    /**
     * @var boolean
     */
    protected $_canCapturePartial           = true;

    /**
     * @var boolean
     */
    protected $_canRefund                   = true;

    /**
     * @var boolean
     */
    protected $_canRefundInvoicePartial     = true;

    /**
     * @var \Elsnertech\Paytrace\Model\Api\Api
     */
    protected $_paytreApi;

    /**
     * @var String
     */
    protected $_supportedCurrencyCodes = ['USD'];

    /**
     * @var \Elsnertech\Paytrace\Block\Form\Paytracevault
     */
    protected $_formBlockType = \Elsnertech\Paytrace\Block\Form\Paytracevault::class;

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_APYTRACEVAULT_CODE;
    
    /**
     * @var boolean
     */
    protected $_isOffline = true;

    /**
     *
     * @var bool
     */
    protected $_canVoid = true;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param Data $paymentData
     * @param ScopeConfigInterface $scopeConfig
     * @param TransactionFactory $transactionFactory
     * @param Logger $logger
     * @param Api $paytreApi
     */

    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        Data $paymentData,
        ScopeConfigInterface $scopeConfig,
        TransactionFactory $transactionFactory,
        Logger $logger,
        Api $paytreApi
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger
        );
        $this->_paytreApi = $paytreApi;
        $this->_transactionFactory = $transactionFactory;
    }

    /**
     * Get Store Config value.
     *
     * @return string
     */
    public function getPayableTo()
    {
        return $this->getConfigData('payable_to');
    }

    /**
     * Get Store Config value.
     *
     * @return string
     */
    public function getMailingAddress()
    {
        return $this->getConfigData('mailing_address');
    }

    /**
     * Authorise Payment.
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     */
    public function authorize(
        InfoInterface $payment,
        $amount
    ) {
        try {
            $token = $this->_paytreApi->getAuthorizeToken();
            if (isset($token['access_token'])) {
                $saledata = $this->_paytreApi->createValtTransaction(
                    $token['access_token'],
                    $payment,
                    $amount,
                    'authorization'
                );
                if (isset($saledata["success"]) &&
                    $saledata["success"] &&
                    $saledata['response_code'] == 101 &&
                    $saledata['transaction_id']
                ) {
                    $payment->setTransactionId(
                        $saledata['transaction_id']
                    )->setIsTransactionClosed(0);
                    $payment->setTransactionAdditionalInfo(
                        \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS,
                        $saledata
                    );
                    return $this;
                } else {
                    $this->debugData([$saledata]);
                    if (isset($saledata["errors"])) {
                        $errormessage = $this->getErrorMessageFromArray(
                            $saledata["errors"]
                        );
                        throw new \Magento\Framework\Validator\Exception(
                            __($errormessage)
                        );
                    } elseif (isset($saledata["approval_message"])) {
                        throw new \Magento\Framework\Validator\Exception(
                            __("Payment Error: ".trim($saledata["approval_message"]))
                        );
                    } else {
                        throw new \Magento\Framework\Validator\Exception(
                            __("Payment error.")
                        );
                    }
                }
            } else {
                throw new \Magento\Framework\Validator\Exception(
                    __('Token not found.')
                );
            }

            throw new \Magento\Framework\Validator\Exception(
                __('Payment error.')
            );
        } catch (\Exception $e) {
            throw new \Magento\Framework\Validator\Exception(
                __($e->getMessage())
            );
        }
    }

    /**
     * Errors Arrays.
     *
     * @param ErrorsMessage $temp
     * @return $temp
     */
    public function getErrorMessageFromArray($temp)
    {
        if (is_array($temp)) {
            $extractval = reset($temp);
            if (is_array($extractval)) {
                return $this->getErrorMessageFromArray($extractval);
            } else {
                return $extractval;
            }
        } else {
            return $temp;
        }
    }

    /**
     * Check whether there are CC types set in configuration
     *
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(
        \Magento\Quote\Api\Data\CartInterface $quote = null
    ) {
        $collection = $this->_paytreApi->getSavedCardArray();
        if (!empty($collection)) {
            return true;
        }

        return false;
    }

    /**
     * Get saved value.
     *
     * @return array
     */
    public function getSavedCards()
    {
        $cardsArray = [];
        $cards = $this->_paytreApi->getSavedCardArray();
        if (!empty($cards)) {
            foreach ($cards as $card) {
                    $imageName = strtolower($card['cc_type']).'.png';
                    array_push(
                        $cardsArray,
                        [
                            'entity_id' => $card['entity_id'],
                            'exp_month' => $card['cc_month'],
                            'exp_year' => $card['cc_year'],
                            'paytrace_customer_id' => $card['paytrace_customer_id'],
                            'last4' => '****'.$card['cc_number'],
                            'card_image' => $imageName
                        ]
                    );
            }
        }

        return $cardsArray;
    }

    /**
     * Capture Payment.
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     */
    // phpcs:disable
    public function capture(
        InfoInterface $payment,
        $amount
    ) {
        try {
            $token = $this->_paytreApi->getAuthorizeToken();
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            if (isset($token['access_token'])) {
                if ($payment->getLastTransId()) {
                    $transaction = $this->_transactionFactory->create();
                    $transaction->load($payment->getLastTransId(), "txn_id");
                    if ($transaction->getTransactionId() &&
                        $transaction->getTxnType()=='authorization'
                    ) {
                        $saledata = $this->_paytreApi->createValtTransaction(
                            $token['access_token'],
                            $payment,
                            $amount,
                            'capture'
                        );
                        if (isset($saledata["success"]) &&
                            $saledata["success"] &&
                            $saledata['response_code'] == 112 &&
                            $saledata['transaction_id']
                        ) {
                            $payment->setTransactionAdditionalInfo(
                                \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS,
                                $saledata
                            );
                        
                            $order = $payment->getOrder();
                            $integratorId = $this->getConfigData('integrator_id');
                            $this->_paytreApi->sendPaytraceEmail(
                                $saledata['transaction_id'],
                                $order,
                                $integratorId,
                                $token['access_token']
                            );

                            return $this;
                        } else {
                            $this->debugData([$saledata]);
                            if (isset($saledata["errors"])) {
                                $errormessage = $this->getErrorMessageFromArray(
                                    $saledata["errors"]
                                );
                                throw new \Magento\Framework\Validator\Exception(
                                    __($errormessage)
                                );
                            } elseif (isset($saledata["approval_message"])) {
                                throw new \Magento\Framework\Validator\Exception(
                                    __("Payment Error: ".trim($saledata["approval_message"]))
                                );
                            } else {
                                throw new \Magento\Framework\Validator\Exception(
                                    __("Payment error.")
                                );
                            }
                        }
                    }
                } else {
                    $saledata = $this->_paytreApi->createValtTransaction(
                        $token['access_token'],
                        $payment,
                        $amount,
                        'sale'
                    );
                    if (isset($saledata["success"]) &&
                        $saledata["success"] &&
                        $saledata['response_code'] == 101 &&
                        $saledata['transaction_id']
                    ) {
                        $payment->setTransactionId(
                            $saledata['transaction_id']
                        )->setIsTransactionClosed(0);
                        $payment->setTransactionAdditionalInfo(
                            \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS,
                            $saledata
                        );

                        $order = $payment->getOrder();
                        $integratorId = $this->getConfigData('integrator_id');
                        $this->_paytreApi->sendPaytraceEmail(
                            $saledata['transaction_id'],
                            $order,
                            $integratorId,
                            $token['access_token']
                        );

                        return $this;
                    } else {
                        $this->debugData([$saledata]);
                        if (isset($saledata["errors"])) {
                            $errormessage = $this->getErrorMessageFromArray(
                                $saledata["errors"]
                            );
                            throw new \Magento\Framework\Validator\Exception(
                                __($errormessage)
                            );
                        } elseif (isset($saledata["approval_message"])) {
                            throw new \Magento\Framework\Validator\Exception(
                                __("Payment Error: ".trim($saledata["approval_message"]))
                            );
                        } else {
                            throw new \Magento\Framework\Validator\Exception(
                                __("Payment error.")
                            );
                        }
                    }
                }
            } else {
                throw new \Magento\Framework\Validator\Exception(
                    __('Token not found.')
                );
            }

            throw new \Magento\Framework\Validator\Exception(
                __('Payment error.')
            );
        } catch (\Exception $e) {
            throw new \Magento\Framework\Validator\Exception(
                __($e->getMessage())
            );
        }
    }

    /**
     * Refund Payment.
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     */
    public function refund(
        InfoInterface $payment,
        $amount
    ) {
        try {
            $token = $this->_paytreApi->getAuthorizeToken();
        
            if (isset($token['access_token'])) {
                $saledata = $this->_paytreApi->createRefundTransaction(
                    $token['access_token'],
                    $payment,
                    $amount
                );
                $this->debugData([$saledata]);
                if (isset($saledata["success"]) &&
                    $saledata["success"] &&
                    $saledata['response_code'] == 106 &&
                    $saledata['transaction_id']
                ) {
                    $payment->setTransactionId(
                        $saledata['transaction_id']
                    )->setIsTransactionClosed(0);
                    $payment->setTransactionAdditionalInfo(
                        \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS,
                        $saledata
                    );
                    return $this;
                } else {
                    if (isset($saledata["errors"])) {
                        $voidsaledata = $this->_paytreApi->createVoidRefundTransaction(
                            $token['access_token'],
                            $payment,
                            $amount
                        );
                        $this->debugData([$voidsaledata]);
                        if (isset($voidsaledata["success"]) &&
                            $voidsaledata["success"] &&
                            $voidsaledata['response_code'] == 109 &&
                            $voidsaledata['transaction_id']
                        ) {
                            $payment->setTransactionId(
                                $voidsaledata['transaction_id']
                            )->setIsTransactionClosed(0);
                            $payment->setTransactionAdditionalInfo(
                                \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS,
                                $voidsaledata
                            );
                            return $this;
                        } else {
                            $this->debugData([$voidsaledata]);
                            $errormessage = $this->getErrorMessageFromArray(
                                $voidsaledata["errors"]
                            );
                            throw new \Magento\Framework\Validator\Exception(
                                __($errormessage)
                            );
                        }
                    } else {
                        throw new \Magento\Framework\Validator\Exception(
                            __('Error in refund transaction.')
                        );
                    }
                }
            } else {
                throw new \Magento\Framework\Validator\Exception(
                    __('Token not found.')
                );
            }

            throw new \Magento\Framework\Validator\Exception(
                __('Error in refund transaction.')
            );
        } catch (\Exception $e) {
            throw new \Magento\Framework\Validator\Exception(
                __($e->getMessage())
            );
        }
    }

    /**
     * Cancel paytrace payment method
     *
     * @param \Magento\Framework\DataObject|InfoInterface $payment
     * @return $this
     */
    public function cancel(InfoInterface $payment)
    {
        try {
            $token = $this->_paytreApi->getAuthorizeToken();
            if (isset($token['error']) && $token['error'] == 'invalid_grant') {
                // phpcs:ignore Magento2.Functions.DiscouragedFunction
                sleep(2);
                $token = $this->_paytreApi->getAuthorizeToken();
            }

            if (isset($token['access_token'])) {
                $voidsaledata = $this->_paytreApi->createVoidRefundTransaction(
                    $token['access_token'],
                    $payment,
                    0
                );
                $this->debugData([$voidsaledata]);
                if (isset($voidsaledata["success"]) &&
                    $voidsaledata["success"] &&
                    $voidsaledata['response_code'] == 109 &&
                    $voidsaledata['transaction_id']
                ) {
                    parent::cancel($payment);
                    return $this;
                } else {
                    $this->debugData([$voidsaledata]);
                    $errormessage = $this->getErrorMessageFromArray(
                        $voidsaledata["errors"]
                    );
                    throw new \Magento\Framework\Validator\Exception(
                        __($errormessage)
                    );
                }
            } else {
                throw new \Magento\Framework\Validator\Exception(
                    __('refund : Token not found.')
                );
            }

            throw new \Magento\Framework\Validator\Exception(
                __('Error in refund transaction.')
            );
        } catch (\Exception $e) {
            throw new \Magento\Framework\Validator\Exception(
                __("refund error:".$e->getMessage())
            );
        }
    }

    /**
     * Void paytrace payment method
     *
     * @param \Magento\Framework\DataObject|InfoInterface $payment
     * @return $this
     */
    public function void(InfoInterface $payment)
    {
        try {
            $token = $this->_paytreApi->getAuthorizeToken();
            if (isset($token['error']) && $token['error'] == 'invalid_grant') {
                // phpcs:ignore Magento2.Functions.DiscouragedFunction
                sleep(2);
                $token = $this->_paytreApi->getAuthorizeToken();
            }

            if (isset($token['access_token'])) {
                $voidsaledata = $this->_paytreApi->createVoidRefundTransaction(
                    $token['access_token'],
                    $payment,
                    0
                );
                $this->debugData([$voidsaledata]);
                if (isset($voidsaledata["success"]) &&
                    $voidsaledata["success"] &&
                    $voidsaledata['response_code'] == 109 &&
                    $voidsaledata['transaction_id']
                ) {
                    parent::void($payment);
                    return $this;
                } else {
                    $this->debugData([$voidsaledata]);
                    $errormessage = $this->getErrorMessageFromArray(
                        $voidsaledata["errors"]
                    );
                    throw new \Magento\Framework\Validator\Exception(
                        __($errormessage)
                    );
                }
            } else {
                throw new \Magento\Framework\Validator\Exception(
                    __('refund : Token not found.')
                );
            }

            throw new \Magento\Framework\Validator\Exception(
                __('Error in refund transaction.')
            );
        } catch (\Exception $e) {
            throw new \Magento\Framework\Validator\Exception(
                __("refund error:".$e->getMessage())
            );
        }
    }

    /**
     * Get Payment Block Info.
     *
     * @return \Elsnertech\Paytrace\Block\Info\Paytracevault
     */
    public function getInfoBlockType()
    {
        return \Elsnertech\Paytrace\Block\Info\Paytracevault::class;
    }

    /**
     * Fetch Transaction Detail info.
     *
     * @param Payment $payment
     * @param TransectionId $txnId
     * @return array
     */
    public function fetchTransactionDetailInfo($payment, $txnId)
    {
        $transaction = $this->_transactionFactory->create();
        $transaction->load($txnId, "txn_id");
        $data = [];
        if ($transaction->getAdditionalInformation() != '') {
            $infodata = $transaction->getAdditionalInformation();
            $data = isset($infodata[\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS])?
            $infodata[\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS]:
            [];
        }
        return $data;
    }
}
