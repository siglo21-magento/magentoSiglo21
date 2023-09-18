<?php

/**
 * @Author: adrian.olave@gmail.com
 * @Date:   2020-07-29 22:24:37
 * @Last Modified by: adrian.olave@gmail.com
 * @Last Modified time: 2020-07-30 13:51:02
 */

namespace Aventi\Gshipping\Block\System\Config\Form\Field;

class Banner extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @var \Magento\Backend\Block\Template\Context
     */
    private $assetRepository;


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
    */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context, array $data = []
    ) {
        $this->assetRepository = $context->getAssetRepository();
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) 
    {
        $html  = $element->getElementHtml();
        $value = $element->getData('value');
        
        $asset = $this->assetRepository->createAsset('Aventi_Gshipping::images/banner.png');
        $html .= '<div style="position: relative; margin-top:5px; max-width: auto; height: auto;"><img id="img-banner" src="' . $asset->getUrl() . '" alt="" border="0"></div>';
        return $html;
    }

}
