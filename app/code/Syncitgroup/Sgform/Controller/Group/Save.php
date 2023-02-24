<?php

namespace Syncitgroup\Sgform\Controller\Group;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Psr\Log\LoggerInterface;
use Syncitgroup\Sgform\Model\ProductFactory;

class Save extends Action
{
    protected $pageFactory;

    /**
     * @var UploaderFactory
     */
    protected UploaderFactory $uploaderFactory;

    /**
     * @var AdapterFactory
     */
    protected AdapterFactory $adapterFactory;

    /**
     * @var Filesystem
     */
    protected Filesystem $filesystem;

    protected $viewFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    private $resultJsonFactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        UploaderFactory $uploaderFactory,
        AdapterFactory $adapterFactory,
        Filesystem $filesystem,
        ProductFactory $productFactory,
        LoggerInterface $logger,
        JsonFactory $resultJsonFactory,
    ) {
        $this->pageFactory = $pageFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
        $this->productFactory = $productFactory;
        $this->logger = $logger;
        $this->resultJsonFactory = $resultJsonFactory;
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

            if (isset($files['image']) && !empty($files['image'])) {
            }

            foreach ($files['image'] as $key=>$value) {
                $uploaderFactory = $this->uploaderFactory->create(['fileId' => $files['image'][$key]]);
                $uploaderFactory->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'pdf', 'docx', 'doc']);
                $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                $destinationPath = $mediaDirectory->getAbsolutePath('syncitgroup');
                $result = $uploaderFactory->save($destinationPath);
                if (!$result) {
                    throw new LocalizedException(
                        __('File cannot be saved to path: $1', $destinationPath)
                    );
                }
                $filePath[] = $result['file'];
            }
            //Set file path with name for save into database
            $data['image'] = $filePath;
            $productFactory = $this->productFactory->create();
            $data['image'] = implode(",",$data['image']);
            $productFactory->setData($data);
            if($productFactory->save()){
                echo "saved";
            }else{
                echo "failed";
            }

            $this->messageManager->addSuccessMessage(
                __('Thanks for save new Product!')
            );

//            $resultJson = $this->resultJsonFactory->create();
//            return $resultJson->setData(['success' => 'Thanks for save new Product!']);

        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(
                __('An error occurred while processing your form. Please try again later.')
            );
        }
//        return $this->resultRedirectFactory->create()->setPath('*/*/');
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
        if (trim($request->getParam('sku', '')) === '') {
            throw new LocalizedException(__('Please select your score.'));
        }
        if (!isset($files['image'])) {
            throw new LocalizedException(__('Please select a file.'));
        }
        if (trim($request->getParam('description', '')) === '') {
            throw new LocalizedException(__('Enter the Remarks and try again.'));
        }
//        if (trim($request->getParam('hideit', '')) !== '') {
//            // phpcs:ignore Magento2.Exceptions.DirectThrow
//            throw new \Exception();
//        }

        return $request->getParams();
    }
}
