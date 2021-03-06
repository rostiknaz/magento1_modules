<?php

/**
 * Created by PhpStorm.
 * User: naro
 * Date: 26.07.16
 * Time: 14:58
 */
class Cgi_UpdatePrice_Model_Observer
{
    public function prepareMassaction(Varien_Event_Observer $observer)
    {
//        print_r($this->_getOptionArray());exit;
        $block = $observer->getEvent()->getBlock();
        if(get_class($block) =='Mage_Adminhtml_Block_Widget_Grid_Massaction'
            && $block->getRequest()->getControllerName() == 'catalog_product')
        {
            $block->addItem('update_price', array(
                'label'=> Mage::helper('cgi_updateprice')->__('Update price'),
                'url'  => Mage::app()->getStore()->getUrl('adminhtml/price/massPrice', array('_current'=>true)), //$block->getUrl('*/*/massPrice', array('_current'=>true)),
                'additional' => array(
                    'visibility' => array(
                        'name'   => 'option',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('cgi_updateprice')->__('Option'),
                        'values' => $this->_getOptionArray()
                    ),
                    'visibility1' => array(
                        'name'  => 'number',
                        'style' => 'width:50px',
                        'type'  => 'text',
                        'class' => 'required-entry',
                        'label' => Mage::helper('cgi_updateprice')->__('Number'),
                    )
                )
            ));
        }
    }

    private function _getOptionArray()
    {
        $operations = Mage::app()->getConfig()->getNode('global/price_mass_action/operations')->asArray();
        $options = [];
        foreach ($operations as $index=>$operation){
            $options[] = [
                'value' => $index,
                'label' => $operation['label']
            ];
        }
        return $options;
    }

}