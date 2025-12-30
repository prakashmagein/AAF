<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Cron\Schedule\Time;

use Magento\Framework\Stdlib\DateTime\DateTime;

class GetNextMinute
{
    /**
     * @var DateTime
     */
    private $dateTime;

    public function __construct(DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public function execute(): string
    {
        return date('Y-m-d H:i', $this->dateTime->gmtTimestamp('+1 minute'));
    }
}
