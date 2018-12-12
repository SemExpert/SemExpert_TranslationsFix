<?php

/**
 * Created by PhpStorm.
 * User: Barbazul
 * Date: 19/5/2017
 * Time: 12:36 PM
 */

namespace SemExpert\TranslationsFix\Model\Json;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\Area;
use Magento\Framework\View\Asset\File\FallbackContext;
use Magento\Framework\View\Asset\PreProcessor\Chain;
use Magento\Translation\Model\Json\PreProcessor as BasePreProcessor;

class PreProcessor extends BasePreProcessor
{
    /**
     * {@inheritdoc}
     *
     * Override: Added loading of PART_DESIGN
     *
     * @see https://github.com/magento/magento2/issues/8508#issuecomment-279332346
     * @param Chain $chain
     * @throws \Exception
     * @return void
     */
    public function process(Chain $chain)
    {
        if ($this->isDictionaryPath($chain->getTargetAssetPath())) {
            $context = $chain->getAsset()->getContext();

            $themePath = '*/*';
            $areaCode = FrontNameResolver::AREA_CODE;

            if ($context instanceof FallbackContext) {
                $themePath = $context->getThemePath();
                $areaCode = $context->getAreaCode();
                $this->translate->setLocale($context->getLocale());
            }

            $area = $this->areaList->getArea($areaCode);
            $area->load(Area::PART_DESIGN);
            $area->load(Area::PART_TRANSLATE);

            $chain->setContent(json_encode($this->dataProvider->getData($themePath)));
            $chain->setContentType('json');
        }
    }
}
