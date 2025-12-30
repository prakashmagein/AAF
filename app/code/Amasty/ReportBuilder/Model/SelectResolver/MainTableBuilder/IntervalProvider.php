<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\MainTableBuilder;

use Amasty\ReportBuilder\Model\Source\IntervalType;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class IntervalProvider
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    public function __construct(TimezoneInterface $timezone)
    {
        $this->timezone = $timezone;
    }

    public function getInterval(string $fieldName, ?string $interval = null): array
    {
        switch ($interval) {
            case IntervalType::TYPE_YEAR:
                $expression = sprintf('YEAR(DATE(%s))', $this->wrapTimezone($fieldName));
                $groups = [$expression];
                break;
            case IntervalType::TYPE_MONTH:
                $fieldName = $this->wrapTimezone($fieldName);
                $expression = 'ADDDATE(DATE(%1$s), INTERVAL 1-DAYOFMONTH(DATE(%1$s)) DAY)';
                $expression = sprintf($expression, $fieldName);
                $groups = [
                    sprintf('YEAR(DATE(%s))', $fieldName),
                    sprintf('MONTH(DATE(%s))', $fieldName)
                ];
                break;
            case IntervalType::TYPE_WEEK:
                $fieldName = $this->wrapTimezone($fieldName);
                $expression = 'ADDDATE(DATE(%1$s), INTERVAL 1-DAYOFWEEK(DATE(%1$s)) DAY)';
                $expression = sprintf($expression, $fieldName);
                $groups = [
                    sprintf('YEAR(DATE(%s))', $fieldName),
                    sprintf('MONTH(DATE(%s))', $fieldName),
                    sprintf('WEEK(DATE(%s))', $fieldName)
                ];
                break;
            case IntervalType::TYPE_DAY:
            default:
                $expression = sprintf('DATE(%s)', $this->wrapTimezone($fieldName));
                $groups = [$expression];
                break;
        }

        return [$expression, $groups];
    }

    /**
     * Convert date field from current session timezone to config timezone.
     * Needed because convert to timezone must be before DATE/MONTH/YEAR mysql function applied.
     * Use +00:00 as from timezone , because magento set +00:00 as @@session.time_zone in mysql.
     * @see \Magento\Framework\DB\Adapter\Pdo\Mysql::_connect
     */
    private function wrapTimezone(string $fieldName): string
    {
        return sprintf(
            'CONVERT_TZ(%s, \'+00:00\', \'%s\')',
            $fieldName,
            $this->timezone->date(null, null, true)->format('P')
        );
    }
}
