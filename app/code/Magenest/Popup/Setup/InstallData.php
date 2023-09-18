<?php
namespace Magenest\Popup\Setup;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface {
    protected $_helperData;
    protected $_popupTemplateFactory;
    public function __construct(
        \Magenest\Popup\Helper\Data $helperData,
        \Magenest\Popup\Model\TemplateFactory $popupTemplateFactory
    ){
        $this->_helperData = $helperData;
        $this->_popupTemplateFactory = $popupTemplateFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $popup_type = [
            [
                'path' => 'yesno_button/popup_1',
                'type' => '1',
                'class' => 'popup-default-3'
            ],
            [
                'path' => 'contact_form/popup_1',
                'type' => '2',
                'class' => 'popup-default-1'
            ],
            [
                'path' => 'contact_form/popup_2',
                'type' => '2',
                'class' => 'popup-default-4'
            ],
            [
                'path' => 'share_social/popup_1',
                'type' => '3',
                'class' => 'popup-default-5'
            ],
            [
                'path' => 'subcribe_form/popup_2',
                'type' => '4',
                'class' => 'popup-default-6'
            ],
            [
                'path' => 'subcribe_form/popup_1',
                'type' => '4',
                'class' => 'popup-default-2'
            ],
            [
                'path' => 'static_form/popup_1',
                'type' => '5',
                'class' => 'popup-default-7'
            ],
            [
                'path' => 'static_form/popup_2',
                'type' => '5',
                'class' => 'popup-default-8'
            ],
            [
                'path' => 'subcribe_form/popup_3',
                'type' => '4',
                'class' => 'popup-default-9'
            ],
            [
                'path' => 'subcribe_form/popup_4',
                'type' => '4',
                'class' => 'popup-default-10'
            ],
            [
                'path' => 'yesno_button/popup_3',
                'type' => '1',
                'class' => 'popup-default-11'
            ],
            [
                'path' => 'static_form/popup_3',
                'type' => '5',
                'class' => 'popup-default-12'
            ],
            [
                'path' => 'static_form/popup_4',
                'type' => '5',
                'class' => 'popup-default-13'
            ],
            [
                'path' => 'subcribe_form/popup_5',
                'type' => '4',
                'class' => 'popup-default-14'
            ],
            [
                'path' => 'contact_form/popup_3',
                'type' => '2',
                'class' => 'popup-default-15'
            ],
            [
                'path' => 'subcribe_form/popup_6',
                'type' => '4',
                'class' => 'popup-default-16'
            ],
            [
                'path' => 'share_social/popup_2',
                'type' => '3',
                'class' => 'popup-default-17'
            ],
            [
                'path' => 'subcribe_form/popup_7',
                'type' => '4',
                'class' => 'popup-default-18'
            ],
            [
                'path' => 'subcribe_form/popup_8',
                'type' => '4',
                'class' => 'popup-default-19'
            ],
            [
                'path' => 'subcribe_form/popup_9',
                'type' => '4',
                'class' => 'popup-default-20'
            ],
            [
                'path' => 'static_form/popup_8',
                'type' => '5',
                'class' => 'popup-default-21'
            ],
            [
                'path' => 'static_form/popup_7',
                'type' => '5',
                'class' => 'popup-default-22'
            ],
            [
                'path' => 'subcribe_form/popup_10',
                'type' => '4',
                'class' => 'popup-default-23'
            ],
            [
                'path' => 'static_form/popup_6',
                'type' => '5',
                'class' => 'popup-default-24'
            ],
            [
                'path' => 'static_form/popup_9',
                'type' => '5',
                'class' => 'popup-default-25'
            ],
            [
                'path' => 'subcribe_form/popup_13',
                'type' => '4',
                'class' => 'popup-default-27'
            ],
            [
                'path' => 'static_form/popup_12',
                'type' => '5',
                'class' => 'popup-default-28'
            ],
            [
                'path' => 'share_social/popup_3',
                'type' => '3',
                'class' => 'popup-default-29'
            ],
            [
                'path' => 'share_social/popup_4',
                'type' => '3',
                'class' => 'popup-default-30'
            ],
            [
                'path' => 'contact_form/popup_6',
                'type' => '2',
                'class' => 'popup-default-31'
            ],
            [
                'path' => 'subcribe_form/popup_14',
                'type' => '4',
                'class' => 'popup-default-32'
            ],
            [
                'path' => 'static_form/popup_13',
                'type' => '5',
                'class' => 'popup-default-33'
            ],
            [
                'path' => 'yesno_button/popup_2',
                'type' => '1',
                'class' => 'popup-default-34'
            ],
            [
                'path' => 'yesno_button/popup_4',
                'type' => '1',
                'class' => 'popup-default-35'
            ],
            [
                'path' => 'subcribe_form/popup_12',
                'type' => '4',
                'class' => 'popup-default-36'
            ],
            [
                'path' => 'contact_form/popup_4',
                'type' => '2',
                'class' => 'popup-default-26'
            ],
            [
                'path' => 'contact_form/popup_5',
                'type' => '2',
                'class' => 'popup-default-37'
            ],
            [
                'path' => 'static_form/popup_10',
                'type' => '5',
                'class' => 'popup-default-38'
            ],
            [
                'path' => 'subcribe_form/popup_11',
                'type' => '4',
                'class' => 'popup-default-39'
            ]
        ];
        $data = [];
        $count = 0;
        foreach ($popup_type as $type){
            $data[] = [
                'template_name' => "Default Template ".$count,
                'template_type' => $type['type'],
                'html_content' => $this->_helperData->getTemplateDefault($type['path']),
                'css_style' => '',
                'class' => $type['class']
            ];
            $count++;
        }
        $popupTemplateModel = $this->_popupTemplateFactory->create();
        $popupTemplateModel->insertMultiple($data);
        $installer->endSetup();
    }
}