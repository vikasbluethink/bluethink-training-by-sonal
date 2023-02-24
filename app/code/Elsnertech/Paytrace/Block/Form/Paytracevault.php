<?php
/**
 * @author Elsner Team
 * @copyright Copyright © Elsner Technologies Pvt. Ltd (https://www.elsner.com/)
 * @package Elsnertech_Paytrace
 */

namespace Elsnertech\Paytrace\Block\Form;

use Magento\Payment\Block\Form;

class Paytracevault extends Form
{
    /**
     * Paytrace Vault form template
     *
     * @var string
     */
    protected $_template = 'Elsnertech_Paytrace::form/paytracevault.phtml';
}
