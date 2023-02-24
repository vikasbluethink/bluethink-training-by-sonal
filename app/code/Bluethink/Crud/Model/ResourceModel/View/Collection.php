<?php
namespace Bluethink\Crud\Model\ResourceModel\View;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Remittance File Collection Constructor
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Bluethink\Crud\Model\View::class, \Bluethink\Crud\Model\ResourceModel\View::class);
    }
}
