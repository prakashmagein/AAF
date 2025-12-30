<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://aheadworks.com/end-user-license-agreement/
 *
 * @package    RewardPoints
 * @version    2.4.0
 * @copyright  Copyright (c) 2024 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\RewardPoints\Controller\Cart;

use Aheadworks\RewardPoints\Model\Config;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\SessionException;
use Magento\Store\Model\StoreManagerInterface;

class Remove extends Action
{
    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param CheckoutSession $checkoutSession
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     */
    public function __construct(
        Context $context,
        private readonly CustomerSession $customerSession,
        private readonly CheckoutSession $checkoutSession,
        private readonly StoreManagerInterface $storeManager,
        private readonly Config $config
    ) {
        parent::__construct($context);
    }

    /**
     * Only logged in users can use this functionality
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundException
     * @throws SessionException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->customerSession->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }

    /**
     * Remove Reward Points from current quote
     *
     * @return Redirect
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $quote = $this->checkoutSession->getQuote();
        $websiteId = (int)$this->storeManager->getWebsite()->getId();
        if ($quote->getAwUseRewardPoints()) {
            $this->messageManager->addSuccess(__('%1 were successfully removed.',
                $this->config->getLabelNameRewardPoints($websiteId)
                )
            );
            $quote->setAwUseRewardPoints(false)->collectTotals()->save();
        } else {
            $this->messageManager->addError(__('You are not using %1 in your shopping cart.',
                $this->config->getLabelNameRewardPoints($websiteId)
                )
            );
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('checkout/cart');
    }
}
