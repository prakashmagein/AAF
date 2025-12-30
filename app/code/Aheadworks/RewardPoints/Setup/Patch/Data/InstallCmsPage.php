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
namespace Aheadworks\RewardPoints\Setup\Patch\Data;

use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Model\PageFactory;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class InstallCmsPage
 */
class InstallCmsPage implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /**
     * Page factory
     *
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param PageFactory $pageFactory
     * @param Logger $logger
     * @param PageRepositoryInterface $pageRepository
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        PageFactory $pageFactory,
        Logger $logger,
        PageRepositoryInterface $pageRepository
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->pageFactory = $pageFactory;
        $this->logger = $logger;
        $this->pageRepository = $pageRepository;
    }

    /**
     * Add Cms page
     *
     * @return $this
     */
    public function apply()
    {
        $cmsPage = [
            'title' => 'Reward Points',
            'page_layout' => '1column',
            'identifier' => 'aw-reward-points',
            'content_heading' => 'Reward Points',
            'is_active' => 1,
            'stores' => [0],
            'content' => '<p>The Reward Points Program allows you to earn points for certain actions you take '
                . 'on the site. Points are awarded based on making purchases and customer actions such as submitting '
                . 'reviews.</p>',
        ];

        $page = $this->pageFactory->create();
        if (!$page->checkIdentifier('aw-reward-points', 0)) {
            try {
                $page->setData($cmsPage);
                $this->pageRepository->save($page);
            } catch (\Exception $exception) {
                $this->logger->critical($exception->getMessage());
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }
}
