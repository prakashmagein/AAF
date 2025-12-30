<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Plugin\Deploy\Config;

use Magento\Deploy\Config\BundleConfig;

class ExcludeVendorDirectory
{
    public function afterGetExcludedDirectories(
        BundleConfig $subject,
        array $result,
        string $area,
        string $theme
    ): array {
        $result[] = 'Amasty_ReportBuilder::js/vendor';

        return $result;
    }
}
