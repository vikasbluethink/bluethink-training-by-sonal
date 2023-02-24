<?php
declare(strict_types=1);

namespace Syncitgroup\Sgform\Api\Data;

interface ProductInterface
{

    const NAME = 'name';
    const WEIGHT = 'weight';
    const DESCRIPTION = 'description';
    const PRICE = 'price';
    const PRODUCT_ID = 'product_id';
    const QTY = 'qty';
    const SKU = 'sku';
    const IMAGE = 'image';
    const TAXCLASS = 'taxclass';

    /**
     * Get product_id
     * @return string|null
     */
    public function getProductId();

    /**
     * Set product_id
     * @param string $productId
     * @return \Syncitgroup\Sgform\Product\Api\Data\ProductInterface
     */
    public function setProductId($productId);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Syncitgroup\Sgform\Product\Api\Data\ProductInterface
     */
    public function setName($name);

    /**
     * Get sku
     * @return string|null
     */
    public function getSku();

    /**
     * Set sku
     * @param string $sku
     * @return \Syncitgroup\Sgform\Product\Api\Data\ProductInterface
     */
    public function setSku($sku);

    /**
     * Get price
     * @return string|null
     */
    public function getPrice();

    /**
     * Set price
     * @param string $price
     * @return \Syncitgroup\Sgform\Product\Api\Data\ProductInterface
     */
    public function setPrice($price);

    /**
     * Get taxclass
     * @return string|null
     */
    public function getTaxclass();

    /**
     * Set taxclass
     * @param string $taxclass
     * @return \Syncitgroup\Sgform\Product\Api\Data\ProductInterface
     */
    public function setTaxclass($taxclass);

    /**
     * Get image
     * @return string|null
     */
    public function getImage();

    /**
     * Set image
     * @param string $image
     * @return \Syncitgroup\Sgform\Product\Api\Data\ProductInterface
     */
    public function setImage($image);

    /**
     * Get qty
     * @return string|null
     */
    public function getQty();

    /**
     * Set qty
     * @param string $qty
     * @return \Syncitgroup\Sgform\Product\Api\Data\ProductInterface
     */
    public function setQty($qty);

    /**
     * Get weight
     * @return string|null
     */
    public function getWeight();

    /**
     * Set weight
     * @param string $weight
     * @return \Syncitgroup\Sgform\Product\Api\Data\ProductInterface
     */
    public function setWeight($weight);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \Syncitgroup\Sgform\Product\Api\Data\ProductInterface
     */
    public function setDescription($description);
}

