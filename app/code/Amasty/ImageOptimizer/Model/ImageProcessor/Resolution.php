<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\ImageProcessor;

use Amasty\ImageOptimizer\Api\Data\ImageSettingInterface;
use Amasty\ImageOptimizer\Api\Data\QueueInterface;
use Amasty\ImageOptimizer\Model\Command\CommandProvider;
use Amasty\ImageOptimizer\Model\OptionSource\ResizeAlgorithm;
use Amasty\PageSpeedTools\Model\OptionSource\Resolutions;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\Adapter\Gd2;

class Resolution implements ImageProcessorInterface
{
    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var Gd2
     */
    private $gd2;

    /**
     * @var ResolutionToolProvider
     */
    private $resolutionToolProvider;

    public function __construct(
        Gd2 $gd2,
        Filesystem $filesystem,
        ?CommandProvider $webpCommandProvider, // @deprecated
        ResolutionToolProvider $resolutionToolProvider = null
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->gd2 = $gd2;
        // OM for backward compatibility
        $this->resolutionToolProvider = $resolutionToolProvider
            ?? ObjectManager::getInstance()->get(ResolutionToolProvider::class);
    }

    public function process(QueueInterface $queue): void
    {
        $imagePath = $this->mediaDirectory->getAbsolutePath($queue->getFilename());
        $resolutions = $queue->getResolutions();

        try {
            $this->gd2->open($imagePath);
        } catch (\Exception $e) {
            return;
        }

        $width = $this->gd2->getOriginalWidth();
        $height = $this->gd2->getOriginalHeight();
        if ($width == 0 || $height == 0) {
            return;
        }
        $this->gd2->keepAspectRatio(true);
        $this->gd2->keepTransparency(true);

        foreach (Resolutions::RESOLUTIONS as $resolutionKey => $resolutionData) {
            if (in_array($resolutionKey, $resolutions) && $width > $resolutionData['width']) {
                switch ($queue->getResizeAlgorithm()) {
                    case ResizeAlgorithm::RESIZE:
                        try {
                            $this->gd2->resize($resolutionData['width']);
                        } catch (\Exception $e) {
                            continue 2;
                        }
                        break;
                    case ResizeAlgorithm::CROP:
                        try {
                            $this->gd2->crop(0, 0, $width - $resolutionData['width'], 0);
                        } catch (\Exception $e) {
                            continue 2;
                        }
                        break;
                }

                $newName = str_replace(
                    $queue->getFilename(),
                    $resolutionData['dir'] . $queue->getFilename(),
                    $imagePath
                );
                if (!$this->mediaDirectory->isExist($this->dirname($newName))) {
                    $this->mediaDirectory->create($this->dirname($newName));
                }
                $this->gd2->save($newName);

                foreach ($this->resolutionToolProvider->getTools() as $tool) {
                    if ($queue->getData($tool->getToolName())) {
                        $tool->process($queue, $newName);
                    }
                }

                $this->gd2->open($imagePath);
            }
        }
    }

    public function prepareQueue(string $file, ImageSettingInterface $imageSetting, QueueInterface $queue): bool
    {
        $resolutions = [];
        if ($imageSetting->isCreateMobileResolution()) {
            $resolutions[] = Resolutions::MOBILE;
        }
        if ($imageSetting->isCreateTabletResolution()) {
            $resolutions[] = Resolutions::TABLET;
        }
        $queue->setResolutions($resolutions);
        $queue->setResizeAlgorithm($imageSetting->getResizeAlgorithm());

        return !empty($resolutions);
    }

    private function dirname(string $file): string
    {
        //phpcs:ignore
        return dirname($file);
    }
}
