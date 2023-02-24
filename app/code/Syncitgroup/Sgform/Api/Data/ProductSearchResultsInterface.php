<?php
declare(strict_types=1);

namespace Syncitgroup\Sgform\Api\Data;

interface ProductSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Product list.
     * @return \Syncitgroup\Sgform\Api\Data\ProductInterface[]
     */
    public function getItems();

    /**
     * Set name list.
     * @param \Syncitgroup\Sgform\Api\Data\ProductInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

