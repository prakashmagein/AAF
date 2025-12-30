<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

use Amasty\ReportBuilder\Model\ReportRepository;
use Amasty\ReportBuilder\Test\Registry;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;

/** @var ReportRepository $reportRepository */
$reportRepository = Bootstrap::getObjectManager()->get(ReportRepository::class);

try {
    $id = Registry::$REPORT_ID;
    if ($id) {
        $reportRepository->deleteById($id);
    }
    Registry::$REPORT_ID = null;
} catch (NoSuchEntityException $e) {
    //Report already removed
}
