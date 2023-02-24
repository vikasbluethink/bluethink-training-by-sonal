<?php
declare(strict_types=1);

namespace Syncitgroup\Sgform\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ProductRepositoryInterface
{

    /**
     * Save Product
     * @param \Syncitgroup\Sgform\Api\Data\ProductInterface $product
     * @return \Syncitgroup\Sgform\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Syncitgroup\Sgform\Api\Data\ProductInterface $product
    );

    /**
     * Retrieve Product
     * @param string $productId
     * @return \Syncitgroup\Sgform\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($productId);

    /**
     * Retrieve Product matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Syncitgroup\Sgform\Api\Data\ProductSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Product
     * @param \Syncitgroup\Sgform\Api\Data\ProductInterface $product
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Syncitgroup\Sgform\Api\Data\ProductInterface $product
    );

    /**
     * Delete Product by ID
     * @param string $productId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($productId);
}

