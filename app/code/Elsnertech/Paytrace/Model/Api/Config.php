<?php
/**
 * @author Elsner Team
 * @copyright Copyright © Elsner Technologies Pvt. Ltd (https://www.elsner.com/)
 * @package Elsnertech_Paytrace
 */

namespace Elsnertech\Paytrace\Model\Api;

use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Model\AbstractModel;

class Config extends AbstractModel
{
    /**
     * Vault Enable
     */
    public const PAYTRACE_VALUT_ENABLE = 'payment/paytrace/paytrace_cc_vault_active';

    /**
     * Is send email recipt
     */
    public const PAYTRACE_RECIPT_EMAIL = 'payment/paytrace/transaction';

    /**
     * Enable paytrace captcha
     */
    public const PAYTRACE_CAPTCHA_ENABLE = 'payment/paytrace/paytrace_checkout_captcha';

    /**
     * Enable paytrace vault captcha
     */
    public const PAYTRACE_CAPTCHA_VALUT_ENABLE = 'payment/paytracevault/paytrace_checkout_captcha';

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $_encryption;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param EncryptorInterface $encription
     * @param Data $jsonHelper
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        EncryptorInterface $encription,
        Data $jsonHelper,
        ScopeConfigInterface $scopeConfig
    ) {
       
        $this->scopeConfig = $scopeConfig;
        $this->_encryption = $encription;
        $this->storeManager  = $storeManager;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context, $registry);
    }

    /**
     * Get Store config value.
     *
     * @param value $value
     * @return string
     */
    public function getConfigDataValue($value)
    {
        return $this->scopeConfig->getValue(
            $value,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is Paytrace payment method enable.
     *
     * @return string
     */
    public function getPaytraceVaultEnable()
    {
        return $this->scopeConfig->getValue(
            self::PAYTRACE_VALUT_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is Customer Email Recipt fpr Paytrace payment method enable.
     *
     * @return string
     */
    public function isReciptEmailEnable()
    {
        return $this->scopeConfig->getValue(
            self::PAYTRACE_RECIPT_EMAIL,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is Paytrace Captcha Enable.
     *
     * @return string
     */
    public function getPaytraceCaptchaEnable()
    {
        return $this->scopeConfig->getValue(
            self::PAYTRACE_CAPTCHA_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is Paytrace Vault Captcha Enable.
     *
     * @return string
     */
    public function getPaytraceVaultCaptchaEnable()
    {
        return $this->scopeConfig->getValue(
            self::PAYTRACE_CAPTCHA_VALUT_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get valid Transection Id.
     *
     * @param Transection $txnId
     * @return string
     */
    public function getValidateTransectionIdString($txnId)
    {
        return str_replace('-capture', '', $txnId);
    }

    /**
     * Get Host name.
     *
     * @return string
     */
    public function getHostName()
    {
        $name = $this->storeManager->getStore()->getBaseUrl();
        return $this->getUrlToDomain($name);
    }

    /**
     * Get Domain from url.
     *
     * @param url $url
     * @return string
     */
    public function getUrlToDomain($url)
    {   // phpcs:ignore Magento2.Functions.DiscouragedFunction
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) {
            $host = $url;
        }
        if (substr($host, 0, 4) == "www.") {
            $host = substr($host, 4);
        }
        if (strlen($host) > 50) {
            $host = substr($host, 0, 47) . '...';
        }
            $hostName = explode('.', $host);
        if (count($hostName) == 3) {
            return $hostName[1];
        } elseif (count($hostName) == 2) {
            return $hostName[0];
        }

        return implode('', $hostName);
    }

    /**
     * Get error message.
     *
     * @param data $temp
     * @return string
     */
    public function getErrorMessageFromArray($temp)
    {
        if (is_array($temp)) {
            $extractval = reset($temp);
            if (is_array($extractval)) {
                return $this->getErrorMessageFromArray(
                    $extractval
                );
            } else {
                return $extractval;
            }
        } else {
            return $temp;
        }
    }

    /**
     * Encrypt Text.
     *
     * @param text $temp
     * @return string
     */
    public function encryptText($temp)
    {
        return $this->_encryption->encrypt($temp);
    }

    /**
     * Decrypt Text.
     *
     * @param text $temp
     * @return string
     */
    public function decryptText($temp)
    {
        return $this->_encryption->decrypt($temp);
    }

    /**
     * Array converted to Json Text.
     *
     * @param text $temp
     * @param array $array
     * @return string
     */
    public function jsonEncode($temp, $array = false)
    {
        return $this->jsonHelper->jsonEncode($temp);
    }

    /**
     * Json String converted to Array.
     *
     * @param text $temp
     * @param array $array
     * @return string
     */
    public function jsonDecode($temp, $array = false)
    {
        return $this->jsonHelper->jsonDecode($temp);
    }

    /**
     * Get Deploy key.
     *
     * @return string
     */
    public function getDeployKeys()
    {
        return $this->_encryption->exportKeys();
    }

    /**
     * Create Customer Vault from card detail.
     *
     * @param Year $year
     * @param month $month
     * @param Ccnumber $ccnumber
     * @param customerId $customerId
     * @return string
     */
    public function createCustomerVaultKey(
        $year,
        $month,
        $ccnumber,
        $customerId
    ) {
        $paytraceid = $year.'_'.$month.'_'.$this->getDeployKeys().'_'.$ccnumber.'_'.$customerId;
        return hash('md5', $paytraceid);
    }
}
