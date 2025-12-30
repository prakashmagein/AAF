<?php
declare(strict_types=1);

namespace Gwl\Datalayerevents\Controller\Select;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 */
class Shipping extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context               $context,
        PageFactory           $resultPageFactory,
        JsonFactory           $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Create block and template event
     *
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        
            $resultPage = $this->resultPageFactory->create();
            $block = $resultPage->getLayout()
                ->createBlock('Gwl\Datalayerevents\Block\Select\Shipping')
                ->setTemplate('Gwl_Datalayerevents::select/shipping.phtml')
                ->toHtml();
            $result->setData(['output' => $block]);
            return $result;
        return $this->resultPageFactory->create();
    }
}
