<?php
/**
 * Copyright © aventi All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);
namespace Aventi\Webscrapping\Helper;
use Magento\Framework\App\Helper\AbstractHelper;

class Information extends AbstractHelper
{

    protected $registry;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
        parent::__construct($context);

    }

    /**
     * Returns a dummy string.
     *
     * @return string
     */

    public function getDescrition()
    {

        $brandName = '';
        $codeHtml = __('');

        $product = $this->registry->registry('current_product');
        $brandId = $product->getData('mgs_brand');

        if($brandId){
            $brandName = $product->getResource()->getAttribute('mgs_brand')->getFrontend()->getValue($product);
            if($brandName){
                $brandName = strtoupper($brandName);
            }
        }

        $data =  (object)array(
            'sku' => $product->getSku(),
            'ref' => $product->getData('ref'),
            //'distributor' => $product->getData('distributor'),
            'brand' => $brandName
        );

        if(!$data->sku || !$data->brand || !$data->ref){
            return $codeHtml;
        }

        // code webscapping to Flixmedia
        // $codeFlixmedia = '<div id="flix-minisite">&nbsp;</div> <div id="flix-inpage">&nbsp;</div> <script type="text/javascript" src="//media.flixfacts.com/js/loader.js" data-flix-distributor="'.$data->distributor.'" data-flix-language="ec" data-flix-brand="'.$data->brand.'" data-flix-mpn="'.$data->sku.'" data-flix-ean="" data-flix-sku="'.$data->ref.'" data-flix-button="flix-minisite" data-flix-inpage="flix-inpage" data-flix-button-image="" data-flix-price="" data-flix-fallback-language="t2" async></script>';

        //code webscapping to CNET









        $codeCnet  = '<div id="ccs-logos"></div>';
        $codeCnet .= '<div id="ccs-inline-content"></div>';
        $codeCnet .= '<div id="ccs-widget-windows-server-2016"></div>';
        $codeCnet .= '<div id="ccs-widget-windows-server-2016-coem"></div>';
        $codeCnet .= '<div id="ccs-widget-windows-server-2016-cal"></div>';
        $codeCnet .= '<div id="ccs-widget-windows-server-2019"></div>';
        $codeCnet .= '<div id="ccs-widget-windows-server-2019-coem"></div>';
        $codeCnet .= '<div id="ccs-widget-windows-server-2019-cal"></div>';
        $codeCnet .= '<div id="ccs-widget-microsoft-365"></div>';
        $codeCnet .= "<script type='text/javascript'>
                      var ccs_cc_args = ccs_cc_args || [];
                      ccs_cc_args.push(['cpn', '".$data->ref."']);
                      ccs_cc_args.push(['mf', '".$data->brand."']);
                      ccs_cc_args.push(['pn', '".$data->ref."']);
                      ccs_cc_args.push(['ccid', '".$data->sku."']);
                      ccs_cc_args.push(['lang', 'ES']);
                      ccs_cc_args.push(['market', 'EC']);
                      (function() {
                          var o = ccs_cc_args;
                          o.push(['_SKey', '853096ae']);
                          o.push(['_ZoneId', 'hp-auto-pp']);
                          var sc = document.createElement('script');
                          sc.type = 'text/javascript';
                          sc.async = true;
                          sc.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.cs.1worldsync.com/jsc/h1ws.js';
                          var n = document.getElementsByTagName('script')[0];
                          n.parentNode.insertBefore(sc, n);
                      })();
                  </script>";

        $codeFlixmedia = '<div id="flix-minisite"></div>';
        $codeFlixmedia .= '<div id="flix-inpage"></div>';
        $codeFlixmedia.= '<script type="text/javascript"
                        src="//media.flixfacts.com/js/loader.js"
                        data-flix-distributor="13813"
                        data-flix-language="ec"
                        data-flix-brand="'.$data->brand.'"
                        data-flix-mpn="'.$data->ref.'"
                        data-flix-ean=""
                        data-flix-sku="'.$data->sku.'"
                        data-flix-button="flix-minisite"
                        data-flix-inpage="flix-inpage"
                        data-flix-button-image=""
                        data-flix-price=""
                        data-flix-fallback-language="t2"
                        async>
                        </script>';

        $codeIcecat = '<script type="text/javascript" src="https://live.icecat.biz/js/live-current-2.js"></script>';
        $codeIcecat .= '<div id="IcecatLive"></div>';
        $codeIcecat .= '<script type="text/javascript">
                        setTimeout(function(){
                        IcecatLive.getDatasheet(
                            "#IcecatLive", {
                            "UserName": "mcuevar2110",
                            "Brand": "'.$data->brand.'",
                            "ProductCode": "'.$data->ref.'",
                            }, "es");
                            }, 200)';

        $codeIcecat .= '</script>';


        $codeHtml = $codeCnet;
        $codeHtml .= $codeFlixmedia;
        $codeHtml .= $codeIcecat;


        return $codeHtml;

    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }
}

