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

namespace Aheadworks\RewardPoints\Controller\Adminhtml\Spending\Rules\PostDataProcessor;

use Aheadworks\RewardPoints\Api\Data\SpendRuleInterface;
use Aheadworks\RewardPoints\Model\Data\ProcessorInterface;
use Magento\Store\Model\Website as WebsiteModel;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Website
 */
class Website implements ProcessorInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Process data
     *
     * @param array $data
     * @return array
     */
    public function process($data): array
    {
        $websiteData = [];
        if (isset($data[SpendRuleInterface::WEBSITE_IDS])
            && is_array($data[SpendRuleInterface::WEBSITE_IDS])) {
            foreach ($data[SpendRuleInterface::WEBSITE_IDS] as $key => $value) {
                $websiteData[$key] = (int)$value;
            }
        }
        if (empty($websiteData)) {
            /** @var WebsiteModel $currentWebsite */
            $currentWebsite = $this->storeManager->getWebsite();
            $websiteData[] = (int)$currentWebsite->getId();
        }
        $data[SpendRuleInterface::WEBSITE_IDS] = $websiteData;

        return $data;
    }
}
