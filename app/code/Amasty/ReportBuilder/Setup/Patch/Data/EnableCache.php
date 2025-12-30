<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Setup\Patch\Data;

use Amasty\ReportBuilder\Model\Cache\Type;
use Magento\Framework\App\Cache\Manager;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Enable schema cache on module installation
 */
class EnableCache implements DataPatchInterface
{
    /**
     * @var Manager
     */
    private $cacheManager;

    public function __construct(
        Manager $cacheManager
    ) {
        $this->cacheManager = $cacheManager;
    }

    /**
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return $this
     */
    public function apply()
    {
        $this->cacheManager->setEnabled([Type::CACHE_ID], true);

        return $this;
    }
}
