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
namespace Aheadworks\RewardPoints\Block\Information;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class Bar
 *
 * @method array getInformationMessages()
 * @package Aheadworks\RewardPoints\Block\Information
 */
class Bar extends \Magento\Framework\View\Element\Template
{
    /**
     * Block template filename
     *
     * @var string
     */
    protected $_template = 'Aheadworks_RewardPoints::information/bar.phtml';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->objectManager = $objectManager;
    }

    /**
     * Retrieve information messages
     *
     * @return array
     */
    public function getMessages()
    {
        $messages = [];
        foreach ($this->getInformationMessages() as $informationMessage) {
            $messageBlock = $this->objectManager->create($informationMessage);
            if ($messageBlock->canShow()) {
                $messages[] = $messageBlock->toHtml();
            }
        }

        return $messages;
    }
}
