<?php


namespace Aventi\Documents\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;

class Ficha  extends \Magento\Ui\Component\Listing\Columns\Column
{

    /**
     * Column name
     */
    const NAME = 'ficha';

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    private $helper;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        StoreManagerInterface $storeManager,
        \Aventi\Documents\Helper\Data $helper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->storeManager = $storeManager;
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     * @deprecated 101.0.0
     */
    public function prepareDataSource(array $dataSource)
    {

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if($this->helper->haveDirectory($item['sku'])){
                    $html = 'Disponible';
                }else{
                    $html = '<strong>No disponible</strong>';
                }
                $item[$this->getData('name')] =$html;
            }
        }



        return $dataSource;
    }

    /**
     * Prepare component configuration
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        if ($this->storeManager->isSingleStoreMode()) {
            $this->_data['config']['componentDisabled'] = true;
        }
    }
}