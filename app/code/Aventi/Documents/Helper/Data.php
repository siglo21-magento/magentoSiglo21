<?php

namespace Aventi\Documents\Helper;

use Magento\Framework\Exception\FileSystemException;

/**
 * Class Data
 *
 * @package Aventi\Documents\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private $directoryList;

    const XML_PATH_PDF = 'pdf/options/path';

    private $urlInterface;
    /**
     * @var \Magento\Framework\Registry
     */
    private $_coreRegistry;

    /**
     * Data constructor.
     *
     * @param \magento\framework\app\helper\context $context
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \magento\framework\app\helper\context $context,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->directoryList = $directoryList;
        $this->urlInterface = $urlInterface;
        $this->_coreRegistry = $registry;
    }

    /**
     * return the path for the files of productos
     *
     * @author  Carlos Hernan Aguilar <caguilar@aventi.co>
     * @date 28/01/19
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getPath()
    {
        $media =  $this->directoryList->getPath('media') . '/pdf';
        if (!file_exists($media)) {
            mkdir($media, 0700);
        }
        return $media;
    }
    /**
     * @param $sku
     * @author  Carlos Hernan Aguilar <caguilar@aventi.co>
     * @date 28/01/19
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function haveDirectory($sku)
    {
        $media = $this->getPdfPath();
        return (file_exists($media)) ? true : false;
    }

    /**
     * get the real path for the files
     *
     * @param $sku
     * @param $type
     * @author  Carlos Hernan Aguilar <caguilar@aventi.co>
     * @date 28/01/19
     * @return bool|string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getNameFile($sku, $type = '')
    {
        $path = $this->getPdfPath();

        $path .= strtoupper($sku . '_' . $type) . '.pdf';

        return (file_exists($path)) ? $path : false;
    }

    public function hasDocument($sku)
    {
        $cont = 0;
        foreach (['1', '2'] as $value) {
            $exist = ($this->getNameFile($sku, $value) != false) ? true : false;
            $cont += ($exist) ? 1 : 0;
        }
        return ($cont > 0) ? true : false;
    }

    /**
     * Generate the url name
     *
     * @param $title
     * @author  Carlos Hernan Aguilar <caguilar@aventi.co>
     * @date 28/01/19
     * @return string
     */
    public function createUrlKey($title)
    {
        $url = preg_replace('#[^0-9a-z]+#i', '-', $title);
        return  strtolower($url);
    }

    /**
     * Generate the button for download
     * @param $sku
     * @return string
     * @author  Carlos Hernan Aguilar <caguilar@aventi.co>
     * @date 28/01/19
     */
    public function generateButton($sku)
    {
        $html = '';
        try {
            if (($this->hasDocument($sku))) {
                $html = $this->createButtons($sku);
            } else {
                throw new FileSystemException(new \Magento\Framework\Phrase('An error occurred during execution.'));
            }
        } catch (FileSystemException $e) {
            $e->getMessage();
        }
        return $html;
    }

    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    public function getPdfPath()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PDF, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function createButtons($sku)
    {
        $html = '';
        foreach (['1', '2'] as $value) {
            if ($this->getNameFile($sku, $value)) {
                $url = $this->urlInterface->getUrl('documents/download/', ['sku' => $sku, 'type' => $value]);
                $html .= sprintf('<a href="%s" title="%s" ><div class="download-pdf-container"><i class="fa fa-file-pdf-o"></i><p>Descargar PDF</p></div></a>', $url, __('Download Data Sheet'));
            }
        }
        return $html;
    }
}
