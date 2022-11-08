<?php

namespace Bluethink\FormValidation\ViewModel;

use Bluethink\FormValidation\Helper\Data;
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
     * Get user email
     *
     * @return string
     */
    public function getUserEmail()
    {
        return $this->helper->getPostValue('email');
    }

}
