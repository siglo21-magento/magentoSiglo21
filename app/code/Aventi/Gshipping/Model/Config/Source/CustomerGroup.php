<?php
/*
 * @category aventi
 * @copyright: Copyright (c) 2020 
 * @author: adrian.olave@gmail.com
 * @create date: 2020-07-29 17:19:50
 */

namespace Aventi\Gshipping\Model\Config\Source;

class CustomerGroup implements \Magento\Framework\Option\ArrayInterface
{

	/**
	* Customer Group
	*
	* @var \Magento\Customer\Model\ResourceModel\Group\Collection
	*/
    protected $_customerGroup;
 
    /**
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup
	 * @param array $data
	 */

    public function __construct(
    	\Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup      
    )
    {
        $this->_customerGroup = $customerGroup;        
    }
 
	 /**
	 * Get customer groups
	 * 
	 * @return array
	 */ 
    public function toOptionArray()
    {

    $customerGroups = $this->_customerGroup->toOptionArray();
    array_unshift($customerGroups, array('value'=>'', 'label'=>'Ninguno Seleccionado'));
    return $customerGroups;
    }


}