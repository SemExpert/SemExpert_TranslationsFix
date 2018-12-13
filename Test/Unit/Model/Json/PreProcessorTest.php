<?php

namespace SemExpert\TranslationsFix\Test\Unit\Model\Json;

use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\Framework\TranslateInterface;
use Magento\Framework\View\Asset\File;
use Magento\Framework\View\Asset\File\FallbackContext;
use Magento\Framework\View\Asset\PreProcessor\Chain;
use Magento\Translation\Model\Js\Config;
use Magento\Translation\Model\Js\DataProvider;
use PHPUnit\Framework\TestCase;
use SemExpert\TranslationsFix\Model\Json\PreProcessor;

/**
 * Created by PhpStorm.
 * User: Barbazul
 * Date: 29/5/2017
 * Time: 4:56 PM
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PreProcessorTest extends TestCase
{
    /**
     * @var TranslateInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $translateMock;

    /**
     * @var PreProcessor
     */
    private $model;

    /**
     * @var DataProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataProviderMock;

    /**
     * @var AreaList|\PHPUnit_Framework_MockObject_MockObject
     */
    private $areaListMock;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    public function setUp()
    {
        $this->configMock = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $this->areaListMock = $this->getMockbuilder(AreaList::class)->disableOriginalconstructor()->getMock();
        $this->dataProviderMock = $this->getMockBuilder(DataProvider::class)->disableOriginalConstructor()->getMock();
        $this->translateMock = $this->getMockForAbstractClass(TranslateInterface::class);

        $this->model = new PreProcessor(
            $this->configMock,
            $this->dataProviderMock,
            $this->areaListMock,
            $this->translateMock
        );
    }

    public function testGetData()
    {
        $chain = $this->getMockBuilder(Chain::class)->disableOriginalConstructor()->getMock();
        $asset = $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock();
        $context = $this->getMockBuilder(FallbackContext::class)->disableOriginalConstructor()->getMock();
        $area = $this->getMockBuilder(Area::class)->disableOriginalConstructor()->getMock();

        $fileName = 'js-translation.json';
        $targetPath = 'path/js-translation.json';
        $themePath = '*/*';
        $dictionary = ['hello' => 'bonjour'];
        $areaCode = 'adminhtml';

        $chain->expects($this->once())
            ->method('getTargetAssetPath')
            ->willReturn($targetPath);
        $this->configMock->expects($this->once())
            ->method('getDictionaryFileName')
            ->willReturn($fileName);
        $chain->expects($this->once())
            ->method('getAsset')
            ->willReturn($asset);
        $asset->expects($this->once())
            ->method('getContext')
            ->willReturn($context);
        $context->expects($this->once())
            ->method('getThemePath')
            ->willReturn($themePath);
        $context->expects($this->once())
            ->method('getAreaCode')
            ->willReturn($areaCode);

        $this->areaListMock->expects($this->once())
            ->method('getArea')
            ->with($areaCode)
            ->willReturn($area);

        $this->dataProviderMock->expects($this->once())
            ->method('getData')
            ->with($themePath)
            ->willReturn($dictionary);
        $chain->expects($this->once())
            ->method('setContent')
            ->with(json_encode($dictionary));
        $chain->expects($this->once())
            ->method('setContentType')
            ->with('json');

        $area->expects($this->exactly(2))
            ->method('load')
            ->withConsecutive(
                [Area::PART_DESIGN],
                [Area::PART_TRANSLATE]
            );

        try {
            /** @var $chain Chain */
            $this->model->process($chain);
        } catch (\Exception $exception) {
            $this->fail("Unhandled exception caught.");
        }
    }
}
