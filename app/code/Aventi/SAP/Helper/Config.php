<?php

namespace Aventi\SAP\Helper;

use Magento\Store\Model\ScopeInterface as ScopeInterface;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{

    const PATH_CSV_CITIES = 'sap/options_city/path_file_city';

    public function getPathCsvCities($store = null)
    {
        return $this->scopeConfig->getValue(self::PATH_CSV_CITIES, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Cast string without accents and returns new string.
     * @param $string
     * @return string
     */
    public function stripAccents($string): string
    {
        return strtr($string,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
            'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
    }
}
