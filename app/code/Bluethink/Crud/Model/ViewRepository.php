<?php

namespace Bluethink\Crud\Model;

use Bluethink\Crud\Api\ViewRepositoryInterface;
use Bluethink\Crud\Model\ResourceModel\View as ResourceView;
use Bluethink\Crud\Model\ResourceModel\View\CollectionFactory as ViewCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class ViewRepository implements ViewRepositoryInterface
{
    /**
     * @var ResourceView
     */
    protected $resource;

    /**
     * @var ViewFactory
     */
    protected $viewFactory;

    /**
     * @var ViewCollectionFactory
     */
    protected $viewCollectionFactory;


    public function __construct(
        ResourceView $resource,
        ViewFactory $viewFactory,
        ViewCollectionFactory $viewCollectionFactory,
    ) {
        $this->resource = $resource;
        $this->viewFactory = $viewFactory;
        $this->viewCollectionFactory = $viewCollectionFactory;
    }

    public function save(\Bluethink\Crud\Api\Data\ViewInterface $view)
    {
        try {
            $this->resource->save($view);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $view;
    }

    public function getById($viewId)
    {
        $view = $this->viewFactory->create();
        $this->resource->load($view, $viewId);
        if (!$view->getId()) {
            throw new NoSuchEntityException(__('The user with the "%1" ID doesn\'t exist.', $viewId));
        }
        return $view;
    }

    public function delete(\Bluethink\Crud\Api\Data\ViewInterface $view)
    {
        try {
            $this->resource->delete($view);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    public function deleteById($viewId)
    {
        return $this->delete($this->getById($viewId));
    }

    /**
     * Retrieve collection processor
     *
     * @deprecated 102.0.0
     * @return CollectionProcessorInterface
     */
    private function getCollectionProcessor()
    {
        //phpcs:disable Magento2.PHP.LiteralNamespaces
        if (!$this->collectionProcessor) {
            $this->collectionProcessor = \Magento\Framework\App\ObjectManager::getInstance()->get(
                'Magento\Cms\Model\Api\SearchCriteria\BlockCollectionProcessor'
            );
        }
        return $this->collectionProcessor;
    }
}
