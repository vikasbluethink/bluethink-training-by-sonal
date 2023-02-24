<?php
/**
 * @author Elsner Team
 * @copyright Copyright © Elsner Technologies Pvt. Ltd (https://www.elsner.com/)
 * @package Elsnertech_Paytrace
 */

namespace Elsnertech\Paytrace\Block\Form;

use Magento\Payment\Block\Form\Cc;

class Paytrace extends Cc
{
    /**
     * Paytrace form template
     *
     * @var string
     */
    protected $_template = 'Elsnertech_Paytrace::form/paytrace.phtml';

    /**
     * Get Vault Enable
     *
     * @return string
     */
    public function getVaultEnable()
    {
        return $this->getMethod()->getVaultEnable();
    }
}
