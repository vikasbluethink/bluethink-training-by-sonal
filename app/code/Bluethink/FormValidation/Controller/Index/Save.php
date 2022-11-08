<?php

namespace Bluethink\FormValidation\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;

class Save extends Action
{
    protected $pageFactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        DataPersistorInterface $dataPersistor
    ) {
        $this->pageFactory = $pageFactory;
        $this->dataPersistor = $dataPersistor;
        return parent::__construct($context);
    }

    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        try {
            $this->validatedParams();
            $this->messageManager->addSuccessMessage(
                __('Thanks for apply. We\'ll respond to you very soon.')
            );
            $this->dataPersistor->clear('job_form');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('job_form', $this->getRequest()->getParams());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(
                __('An error occurred while processing your form. Please try again later.')
            );
            $this->dataPersistor->set('job_form', $this->getRequest()->getParams());
        }
        return $this->resultRedirectFactory->create()->setPath('jobapply/index');
    }

    /**
     * Method to validated params.
     *
     * @return array
     * @throws \Exception
     */
    private function validatedParams()
    {
        $request = $this->getRequest();

        $name = trim($request->getParam('name', ''));

        if ($name === '') {
            throw new LocalizedException(__('Enter the Name and try again.'));
        } elseif (!preg_match('/^[a-zA-Z0-9 ]+$/u', $name)) {
            throw new LocalizedException(__('Please use only letters (a-z or A-Z), numbers (0-9) or spaces only in this field.'));
        } elseif (!preg_match('/^[^0-9-\.].*$/u', $name)) {
            throw new LocalizedException(__('First character of name field must be letter.'));
        }
        if (\strpos($request->getParam('email', ''), '@') === false) {
            throw new LocalizedException(__('The email address is invalid. Verify the email address and try again.'));
        }
        if (trim($request->getParam('job-option', '')) === '') {
            throw new LocalizedException(__('Please select a job option.'));
        }
        // if (trim($request->getParam('resume', '')) === '') {
        //     throw new LocalizedException(__('Please select a file.'));
        // }
        if (trim($request->getParam('description', '')) === '') {
            throw new LocalizedException(__('Enter the description and try again.'));
        }
//        if (trim($request->getParam('hideit', '')) !== '') {
//            // phpcs:ignore Magento2.Exceptions.DirectThrow
//            throw new \Exception();
//        }

        return $request->getParams();
    }
}
