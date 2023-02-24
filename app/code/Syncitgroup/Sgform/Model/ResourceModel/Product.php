<?php
declare(strict_types=1);

namespace Syncitgroup\Sgform\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Product extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('syncitgroup_sgform_product', 'product_id');
    }
}

