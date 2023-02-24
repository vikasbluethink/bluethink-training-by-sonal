<?php
declare(strict_types=1);

namespace Syncitgroup\Sgform\Model\ResourceModel\Product;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'product_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Syncitgroup\Sgform\Model\Product::class,
            \Syncitgroup\Sgform\Model\ResourceModel\Product::class
        );
    }
}

