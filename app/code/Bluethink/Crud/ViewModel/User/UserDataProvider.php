<?php

namespace Bluethink\Crud\ViewModel\User;

use Bluethink\Crud\Helper\Data;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Provides the user data to fill the form.
 */
class UserDataProvider implements ArgumentInterface
{

    /**
     * @var Data
     */
    private $helper;

    /**
     * UserDataProvider constructor.
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Get user name
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->helper->getPostValue('name');
    }
    
    /**
     * Get Score
     *
     * @return int
     */
    public function getScore()
    {
        return $this->helper->getPostValue('score');
    }
    
    /**
     * Get ProfilePhoto
     *
     * @return string
     */
    public function getProfilePhoto()
    {
        return $this->helper->getPostValue('profilephoto');
    }

    /**
     * Get Remarks
     *
     * @return string
     */
    public function getRemarks()
    {
        return $this->helper->getPostValue('remarks');
    }

}
