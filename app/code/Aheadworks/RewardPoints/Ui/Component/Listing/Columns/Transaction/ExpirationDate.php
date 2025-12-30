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
namespace Aheadworks\RewardPoints\Ui\Component\Listing\Columns\Transaction;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * Class Date
 */
class ExpirationDate extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param TimezoneInterface $localeDate
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        TimezoneInterface $localeDate,
        array $components = [],
        array $data = []
    ) {
        $this->localeDate = $localeDate;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $config = $this->getData('config');
        if (!isset($config['editor']['timeOffset'])) {
            $config['editor']['timeOffset'] = $this->getTimezoneOffset();
        }
        if (!isset($config['timeOffset'])) {
            $config['timeOffset'] = $this->getTimezoneOffset();
        }
        $this->setData('config', $config);

        parent::prepare();
    }

    /**
     * Retrieve timezone offset
     *
     * @return int
     */
    private function getTimezoneOffset()
    {
        return (new \DateTime(
            'now',
            new \DateTimeZone(
                $this->localeDate->getConfigTimezone()
            )
        ))->getOffset();
    }
}
