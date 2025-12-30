<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Validation;

class ReportFailedFlag
{
    /**
     * @var bool
     */
    private $flag = false;

    public function set(): void
    {
        $this->flag = true;
    }

    public function get(): bool
    {
        return $this->flag;
    }
}
