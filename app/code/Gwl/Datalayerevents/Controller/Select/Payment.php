<?php
declare(strict_types=1);

namespace Gwl\Datalayerevents\Controller\Select;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 */
class Payment extends \Magento\Framework\App\Action\Action
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
     * @param Config $config
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
     * @return ResponseInterface|Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
            $block = $this->resultPageFactory->create()->getLayout()
                ->createBlock('Gwl\Datalayerevents\Block\Select\Payment')
                ->setTemplate('Gwl_Datalayerevents::select/payment.phtml')
                ->toHtml();
            $result->setData(['output' => $block]);
            return $result;
        return $this->resultPageFactory->create();
    }
}
