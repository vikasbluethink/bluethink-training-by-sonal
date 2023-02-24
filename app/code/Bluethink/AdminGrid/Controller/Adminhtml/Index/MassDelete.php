<?php

declare(strict_types=1);

namespace Bluethink\AdminGrid\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Bluethink\Crud\Api\Data\ViewInterface;
use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;
use Bluethink\Crud\Model\ResourceModel\View;
use Magento\Framework\Message\ManagerInterface;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    private View $resourceModelView;



    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
//        ViewInterface $viewInterface = null,
        LoggerInterface $logger = null,
        View $resourceModelView,
        ManagerInterface $messageManager,
        \Magento\Framework\View\Result\PageFactory $resultFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
//        $this->viewInterface = $viewInterface ?:
//            ObjectManager::getInstance()->create(ViewInterface::class);
        $this->logger = $logger ?:
            ObjectManager::getInstance()->create(LoggerInterface::class);
        $this->resourceModelView = $resourceModelView;
        return parent::__construct($context);

    }

    /**
     * Mass Delete Action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collection->addMediaGalleryData();

        $productDeleted = 0;
        $productDeletedError = 0;
        /** @var \Bluethink\Crud\Api\Data\ViewInterface $profileData */
        foreach ($collection->getItems() as $profileData) {
            try {
                $this->resourceModelView->delete($profileData);
                $productDeleted++;
            } catch (LocalizedException $exception) {
                $this->logger->error($exception->getLogMessage());
                $productDeletedError++;
            }
        }

        if ($productDeleted) {
            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been deleted.', $productDeleted)
            );
        }

        if ($productDeletedError) {
            $this->messageManager->addErrorMessage(
                __(
                    'A total of %1 record(s) haven\'t been deleted. Please see server logs for more details.',
                    $productDeletedError
                )
            );
        }

        return $this->resultRedirectFactory->create()->setPath('user/*/index');
    }
}
