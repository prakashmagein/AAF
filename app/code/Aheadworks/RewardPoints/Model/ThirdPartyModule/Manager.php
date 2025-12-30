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
namespace Aheadworks\RewardPoints\Model\ThirdPartyModule;

use Magento\Framework\Module\ModuleListInterface;

/**
 * Class Manager
 *
 * @package Aheadworks\RewardPoints\Model\ThirdPartyModule
 */
class Manager
{
    /**
     * Aheadworks SARP2 module name
     */
    const SARP2_MODULE_NAME = 'Aheadworks_Sarp2';
    const RAF_MODULE_NAME = 'Aheadworks_Raf';

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        ModuleListInterface $moduleList
    ) {
        $this->moduleList = $moduleList;
    }

    /**
     * Check if SARP2 module enabled
     *
     * @return bool
     */
    public function isSarp2ModuleEnabled()
    {
        return $this->moduleList->has(self::SARP2_MODULE_NAME);
    }

    /**
     * Check if Raf module enabled
     *
     * @return bool
     */
    public function isRafModuleEnabled()
    {
        return $this->moduleList->has(self::RAF_MODULE_NAME);
    }
}
