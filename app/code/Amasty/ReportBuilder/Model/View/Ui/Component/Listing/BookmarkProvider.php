<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\View\Ui\Component\Listing;

use Magento\Ui\Api\BookmarkManagementInterface;

class BookmarkProvider
{
    const VIEW_LISTING = 'amreportbuilder_view_listing';

    /**
     * @var BookmarkManagementInterface
     */
    private $bookmarkManagement;

    public function __construct(
        BookmarkManagementInterface $bookmarkManagement
    ) {
        $this->bookmarkManagement = $bookmarkManagement;
    }

    /**
     * @param int $reportId
     * @return array
     */
    public function execute(int $reportId): array
    {
        $namespace = sprintf('%s_%s', self::VIEW_LISTING, $reportId);
        $bookmarks = [];

        if (!empty($namespace)) {
            $bookmarksData = $this->bookmarkManagement->loadByNamespace($namespace);
            $bookmarks = $bookmarksData->getItems();
        }

        return $bookmarks;
    }
}
