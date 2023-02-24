<?php

namespace Bluethink\Crud\Model;

use Bluethink\Crud\Api\Data\ViewInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class File
 * @package Bluethink\Crud\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class View extends AbstractModel implements ViewInterface, IdentityInterface
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'bluethink_testimonial';

    /**
     * Post Initialization
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Bluethink\Crud\Model\ResourceModel\View');
    }

    /**
     * Get profile_photo
     *
     * @return string|null
     */
    public function getProfilePhoto()
    {
        return $this->getData(self::PROFILE_PHOTO);
    }

    /**
     * Get Name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Get Score
     *
     * @return string|null
     */
    public function getScore()
    {
        return $this->getData(self::SCORE);
    }

    /**
     * Get Remarks
     *
     * @return string|null
     */
    public function getRemarks()
    {
        return $this->getData(self::REMARKS);
    }

    /**
     * Get Created At
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Get Updated At
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Return identities
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Set Profile photo
     *
     * @param string $profilephoto
     * @return $this
     */
    public function setProfilePhoto($profilephoto)
    {
        return $this->setData(self::PROFILE_PHOTO, $profilephoto);
    }

    /**
     * Set Name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        return $this->setData(self::NAMES, $name);
    }

    /**
     * Set Score
     *
     * @param string $score
     * @return $this
     */
    public function setScore($score)
    {
        return $this->setData(self::SCORE, $score);
    }

    /**
     * Set Remarks
     *
     * @param string $remarks
     * @return $this
     */
    public function setRemarks($remarks)
    {
        return $this->setData(self::REMARKS, $remarks);
    }

    /**
     * Set Created At
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Set Updated At
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }
}
