<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\Image;

use Amasty\ImageOptimizer\Api\Data\ImageSettingInterface;
use Amasty\ImageOptimizer\Model\Command\CommandProvider;

class CheckTools
{
    /**
     * @var array ['provider_code' => CommandProvider, ...]
     */
    private $commandProviders;

    public function __construct(
        ?CommandProvider $jpegCommandProvider, // @deprecated
        ?CommandProvider $pngCommandProvider, // @deprecated
        ?CommandProvider $gifCommandProvider, // @deprecated
        ?CommandProvider $webpCommandProvider, // @deprecated
        array $commandProviders
    ) {
        $this->commandProviders = $commandProviders;
    }

    public function check(ImageSettingInterface $model): array
    {
        $errors = [];
        foreach ($this->commandProviders as $code => $provider) {
            if ($setting = $model->hasData($code) ? (string)$model->getData($code) : null) {
                $tool = $provider->get($setting);
                if (!$tool->isAvailable()) {
                    $errors[] = $tool->getUnavailableReason();
                }
            }
        }

        return $errors;
    }
}
