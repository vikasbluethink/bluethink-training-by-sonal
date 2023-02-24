<?php
/**
 * @author Elsner Team
 * @copyright Copyright © Elsner Technologies Pvt. Ltd (https://www.elsner.com/)
 * @package Elsnertech_Paytrace
 */

namespace Elsnertech\Paytrace\Model\Payment;

use Magento\Vault\Model\VaultPaymentInterface;

class Paytracevault
{
    /**
     * @param \Magento\Vault\Model\VaultPaymentInterface $vault
     */
    public function __construct(
        VaultPaymentInterface $vault
    ) {
        $this->vault = $vault;
    }
    
    /**
     * Get object model in array
     *
     * @param Payment $payment
     * @param Pmount $amount
     * @return void
     */
    public function mymethod($payment, $amount)
    {
        $this->vault->authorize($payment, $amount);
    }
}
