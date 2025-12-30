<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\View\Ui\Component\Listing;

use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Api\BookmarkRepositoryInterface;

class BookmarkCleaner
{
    /**
     * @var BookmarkRepositoryInterface
     */
    private $bookmarkRepository;

    /**
     * @var BookmarkProvider
     */
    private $bookmarkProvider;

    public function __construct(
        BookmarkRepositoryInterface $bookmarkRepository,
        BookmarkProvider $bookmarkProvider
    ) {
        $this->bookmarkProvider = $bookmarkProvider;
        $this->bookmarkRepository = $bookmarkRepository;
    }

    /**
     * @param int $reportId
     */
    public function cleanByReportId(int $reportId): void
    {
        $bookmarks = $this->bookmarkProvider->execute($reportId);

        foreach ($bookmarks as $bookmark) {
            try {
                $this->bookmarkRepository->delete($bookmark);
            } catch (LocalizedException $e) {
                continue;
            }
        }
    }
}
