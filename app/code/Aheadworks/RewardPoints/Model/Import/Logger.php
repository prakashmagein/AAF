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

namespace Aheadworks\RewardPoints\Model\Import;

use Zend_Log;
use Zend_Log_Exception;
use Zend_Log_Writer_Stream;

/**
 * Class Logger
 */
class Logger
{
    /**
     * @param Zend_Log $logger
     */
    public function __construct(
        private readonly Zend_Log $logger
    ) {}

    /**
     * Initialize logger
     *
     * @param string $filename
     * @return $this
     * @throws Zend_Log_Exception
     */
    public function init(string $filename): self
    {
        $writer = new Zend_Log_Writer_Stream($filename);
        $this->logger->addWriter($writer);

        return $this;
    }

    /**
     * Add message to log
     *
     * @param $message
     * @return $this
     */
    public function addMessage($message)
    {
        $this->logger->info($message);

        return $this;
    }
}
