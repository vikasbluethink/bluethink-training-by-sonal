<?php

namespace Bluethink\Crud\Block;

use Bluethink\Crud\Model\ResourceModel\View\CollectionFactory;
use Bluethink\Crud\Model\ViewFactory;
use Magento\Framework\View\Element\Template;

/**
 * Main contact form block
 *
 * @api
 * @since 100.0.2
 */
class View extends Template
{
    protected $viewFactory;

    /**
     * CollectionFactory
     * @var null|CollectionFactory
     */
    protected $collectionFactory = null;

    /**
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        ViewFactory $viewFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->viewFactory = $viewFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Returns data
     *
     * @return string
     */
    public function getUserData()
    {
        $collectionFactory = $this->collectionFactory->create();
        $collectionFactory->addFieldToSelect('*')->load();
        return $collectionFactory->getItems();
    }

    /**
     * Returns data
     *
     * @return string
     */
    public function getUserDataById($id)
    {
        $viewFactory = $this->viewFactory->create();
        $data = $viewFactory->load($id);
        return $data;
    }

    /**
     * Returns action url for contact form
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('jobapply/user', ['_secure' => true]);
    }

    public function getMediaUrl()
    {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl;
    }
}
