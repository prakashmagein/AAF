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

namespace Aheadworks\RewardPoints\Ui\Component\Form\Field\EarnRule;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Store extends Select
{
    /**
     * @param ContextInterface $context
     * @param StoreManagerInterface $storeManager
     * @param array|null $options
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        private readonly StoreManagerInterface $storeManager,
        $options = null,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $options, $components, $data);
    }

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare(): void
    {
        if ($this->isNeedToHideStore()) {
            $this->hideStore();
        }
        parent::prepare();
    }

    /**
     * Check if need to hide store field
     *
     * @return bool
     */
    private function isNeedToHideStore(): bool
    {
        return $this->storeManager->isSingleStoreMode();
    }

    /**
     * Hide store field
     */
    private function hideStore()
    {
        $config = $this->getConfig();
        $config['visible'] = false;
        $this->setConfig($config);
    }
}
