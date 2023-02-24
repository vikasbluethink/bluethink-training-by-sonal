<?php

namespace Bluethink\Crud\Controller\User;

use Bluethink\Crud\Model\ViewFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Psr\Log\LoggerInterface;

class Save extends Action
{
    protected $pageFactory;

    /**
    * @var UploaderFactory
    */
    protected $uploaderFactory;

    /**
    * @var AdapterFactory
    */
    protected $adapterFactory;

    /**
    * @var Filesystem
    */
    protected $filesystem;

    protected $viewFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        DataPersistorInterface $dataPersistor,
        UploaderFactory $uploaderFactory,
        AdapterFactory $adapterFactory,
        Filesystem $filesystem,
        ViewFactory $viewFactory,
        LoggerInterface $logger
    ) {
        $this->pageFactory = $pageFactory;
        $this->dataPersistor = $dataPersistor;
        $this->uploaderFactory = $uploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
        $this->viewFactory = $viewFactory;
        $this->logger = $logger;
        return parent::__construct($context);
    }

    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        try {
            $this->validatedParams();
            $request = $this->getRequest();

            $data = $request->getParams();

            $files = $request->getFiles();
            if (isset($files['profilephoto']) && !empty($files['profilephoto']["name"])) {
            }

            $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'profilephoto']);
            $uploaderFactory->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'pdf', 'docx', 'doc']);
            // $uploaderFactory->setAllowRenameFiles(true);
            // $uploaderFactory->setFilesDispersion(true);
            $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $destinationPath = $mediaDirectory->getAbsolutePath('bluethink');
            $result = $uploaderFactory->save($destinationPath);
            if (!$result) {
                throw new LocalizedException(
                    __('File cannot be saved to path: $1', $destinationPath)
                );
            }
            $filePath = $result['file'];

            //Set file path with name for save into database
            $data['profilephoto'] = $filePath;

            $viewFactory = $this->viewFactory->create();

            $id = $request->getParam('id');
            if ($id) {
                $viewFactory->load($id);
                $viewFactory->addData($data);  //To update data
                $viewFactory->setProfilePhoto("bluethink/" . $data['profilephoto']);
                $viewFactory->save();
                $this->messageManager->addSuccessMessage(
                    __('Your profile has been updated successfully.')
                );
            } else {
                $viewFactory->setData($data);
                $viewFactory->setProfilePhoto("bluethink/" . $data['profilephoto']);
                $viewFactory->save();
                $this->messageManager->addSuccessMessage(
                    __('Your profile has been submitted successfully.')
                );
            }

            $this->dataPersistor->clear('job_form');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('job_form', $this->getRequest()->getParams());
            $id = $this->getRequest()->getParam('id');
            $urlpath = isset($id) ? "jobapply/user/index?id=" . $id : "jobapply/user/";
            return $this->resultRedirectFactory->create()->setPath($urlpath);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(
                __('An error occurred while processing your form. Please try again later.')
            );
            $this->dataPersistor->set('job_form', $this->getRequest()->getParams());
            $id = $this->getRequest()->getParam('id');
            $urlpath = isset($id) ? "jobapply/user/index?id=" . $id : "jobapply/user/";
            return $this->resultRedirectFactory->create()->setPath($urlpath);
        }
        return $this->resultRedirectFactory->create()->setPath('jobapply/user/display');
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
        $files = $request->getFiles();

        $name = trim($request->getParam('name', ''));

        if ($name === '') {
            throw new LocalizedException(__('Enter the Name and try again.'));
        } elseif (!preg_match('/^[a-zA-Z0-9 ]+$/u', $name)) {
            throw new LocalizedException(__('Please use only letters (a-z or A-Z), numbers (0-9) or spaces only in this field.'));
        } elseif (!preg_match('/^[^0-9-\.].*$/u', $name)) {
            throw new LocalizedException(__('First character of name field must be letter.'));
        }
        if (trim($request->getParam('score', '')) === '') {
            throw new LocalizedException(__('Please select your score.'));
        }
        if (!isset($files['profilephoto'])) {
            throw new LocalizedException(__('Please select a file.'));
        }
        if (trim($request->getParam('remarks', '')) === '') {
            throw new LocalizedException(__('Enter the Remarks and try again.'));
        }
//        if (trim($request->getParam('hideit', '')) !== '') {
//            // phpcs:ignore Magento2.Exceptions.DirectThrow
//            throw new \Exception();
//        }

        return $request->getParams();
    }
}
