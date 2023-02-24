<?php

namespace Bluethink\AdminGrid\Controller\Adminhtml\Index;

use \Bluethink\Crud\Api\Data\ViewInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use \Bluethink\Crud\Api\ViewRepositoryInterface as ViewRepository;

class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Bluethink_AdminGrid::user';

    /**
     * @var \Bluethink\Crud\Api\ViewRepositoryInterface
     */
    protected $viewRepository;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        JsonFactory $jsonFactory,
        ViewRepository $viewRepository
    ) {
        parent::__construct($context);
        $this->viewRepository = $viewRepository;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $viewId) {

                    $view = $this->viewRepository->getById($viewId);
                    try {
                        $view->setData(array_merge($view->getData(), $postItems[$viewId]));
                        $this->viewRepository->save($view);
                    } catch (\Exception $e) {
                        $messages[] = $this->getErrorWithUserId(
                            $view,
                            __($e->getMessage())
                        );
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    protected function getErrorWithUserId(ViewInterface $view, $errorText)
    {
        return '[User ID: ' . $view->getId() . '] ' . $errorText;
    }
}
