<?php

namespace Bluethink\Crud\Controller\User;

use Bluethink\Crud\Model\ViewFactory;
use Psr\Log\LoggerInterface;

class Delete extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

    protected $viewFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        ViewFactory $viewFactory,
        LoggerInterface $logger)
    {
        $this->_pageFactory = $pageFactory;
        $this->viewFactory = $viewFactory;
        $this->logger = $logger;
        return parent::__construct($context);
    }

    public function execute()
    {
        try {
            $request = $this->getRequest();
            $viewFactory = $this->viewFactory->create();

            $id = $request->getParam('id');
            if($id){
                $viewFactory->load($id);
                $viewFactory->delete();
                $viewFactory->save();
            }
            $this->messageManager->addSuccessMessage(
                __('Profile is deleted successfully.')
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $this->resultRedirectFactory->create()->setPath('jobapply/user/');
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(
                __('An error occurred while processing your form. Please try again later.')
            );
            return $this->resultRedirectFactory->create()->setPath('jobapply/user/');
        }
        return $this->resultRedirectFactory->create()->setPath('jobapply/user/display');
    }
}
