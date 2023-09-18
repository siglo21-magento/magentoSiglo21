<?php
namespace Aventi\SAP\Ui\Component\Listing\Quote;

use Aventi\SAP\Api\DocumentStatusRepositoryInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Psr\Log\LoggerInterface;

/**
 * Class Grid
 * @package Aventi\SAP\Ui\Component\Listing\Quote
 */
class Grid extends Column
{
    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * System store
     *
     * @var SystemStore
     */
    protected $systemStore;

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var DocumentStatusRepositoryInterface
     */
    private $documentStatusRepository;

    /**
     * Grid constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Escaper $escaper
     * @param array $components
     * @param array $data
     * @param LoggerInterface $logger
     * @param DocumentStatusRepositoryInterface $documentStatusRepository
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Escaper $escaper,
        array $components = [],
        array $data = [],
        LoggerInterface $logger,
        DocumentStatusRepositoryInterface $documentStatusRepository
    ) {
        $this->escaper = $escaper;
        $this->logger = $logger;
        $this->documentStatusRepository = $documentStatusRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $documentStatus = null;
                try {
                    $documentStatus = $this->documentStatusRepository->getByParentId($item['id']);
                } catch (LocalizedException $e) {
                    $this->logger->debug("Error to quote grid: " . $e->getMessage());
                }
                switch ($this->getData('name')) {
                    case 'sap':
                        $item[$this->getData('name')] = $this->formatToGrid($this->getData('name'), ($documentStatus) ? $documentStatus->getSap() : '');
                        break;
                    case 'sap_state':
                    default:
                        $item[$this->getData('name')] = $this->formatToGrid($this->getData('name'), ($documentStatus) ? $documentStatus->getSapStatus() : '');
                        break;
                }
            }
        }

        return $dataSource;
    }

    /**
     * @param $key
     * @param $value
     */
    private function formatToGrid($key, $value)
    {
        $returnValue = '';
        switch ($key) {
            case 'sap_state':
            default:
                $returnValue = __($value);
                break;
        }
        return $returnValue;
    }

    /**
     * @param $value
     * @return string
     */
    /*public function formatSapState($value)
    {
        $returnValue = '';
        switch ($value) {
            case 'created':
                $returnValue = '<span class="grid-severity-notice"><span>' . __($value) . '</span></span>';
                break;
            case 'updated':
                $returnValue = '<span class="grid-severity-critical"><span>' . __($value) . '</span></span>';
                break;
            default:
                $returnValue = '<span class="grid-severity-warning"><span>' . __($value) . '</span></span>';
                break;
        }
        return $returnValue;
    }*/
}
