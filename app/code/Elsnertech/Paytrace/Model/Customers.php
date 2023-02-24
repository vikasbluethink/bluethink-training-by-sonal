<?php
/**
 * @author Elsner Team
 * @copyright Copyright © Elsner Technologies Pvt. Ltd (https://www.elsner.com/)
 * @package Elsnertech_Paytrace
 */

namespace Elsnertech\Paytrace\Model;

use Magento\Framework\Model\AbstractModel;
use Elsnertech\Paytrace\Model\ResourceModel\Customers as ResourceModelCustomers;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Elsnertech\Paytrace\Model\Api\Config;

class Customers extends AbstractModel
{
    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModelCustomers::class);
    }

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Config $config,
        array $data = []
    ) {
        $this->_config = $config;
        parent::__construct($context, $registry);
    }

    /**
     * Get Vault card detail
     *
     * @param sting $paytraceid
     * @param sting $customerId
     * @param sting $ccnumber
     * @param sting $month
     * @param sting $year
     * @param sting $cType
     * @return object
     */
    public function getVaultCardByDetail(
        $paytraceid,
        $customerId,
        $ccnumber,
        $month,
        $year,
        $cType
    ) {

        $data = $this->_getResource()->getVaultCardByDetail(
            $customerId,
            $ccnumber,
            $month,
            $year,
            $cType
        );
        $entityId = 0;
        if (empty($data) !== true) {
            foreach ($data as $key => $value) {
                if ($this->_config->decryptText($value['paytrace_customer_id']) == $paytraceid) {
                    $entityId = $value['entity_id'];
                }
            }
        }
        if ($entityId) {
            $this->load($entityId);
        }
        return $this;
    }
}
