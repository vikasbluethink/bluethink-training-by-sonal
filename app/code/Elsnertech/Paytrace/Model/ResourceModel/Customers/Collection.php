<?php
/**
 * @author Elsner Team
 * @copyright Copyright © Elsner Technologies Pvt. Ltd (https://www.elsner.com/)
 * @package Elsnertech_Paytrace
 */

namespace Elsnertech\Paytrace\Model\ResourceModel\Customers;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Entity field
     *
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Model event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'elsnertech_paytrace_customers_collection';

    /**
     * Model event object
     *
     * @var string
     */
    protected $_eventObject = 'paytrace_customers_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Elsnertech\Paytrace\Model\Customers::class,
            \Elsnertech\Paytrace\Model\ResourceModel\Customers::class
        );
    }
}
