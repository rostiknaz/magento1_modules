<?php

/**
 * Price model.
 *
 * @category   Cgi
 * @package    Cgi_UpdatePrice
 * @author     Nazymko Rostyslav CGI Trainee group beta
 */
class Cgi_UpdatePrice_Model_Price extends Mage_Core_Model_Abstract
{
    const ADD_OPTION            = 'add';
    const SUBSTR_OPTION         = 'substract';
    const MULTILPE_OPTION       = 'multiple';
    const ADD_PERCENT_OPTION    = 'add_percent';
    const SUBSTR_PERCENT_OPTION = 'substr_percent';

    protected $_error;

    /**
     * Save new product prices.
     *
     * @return void
     */
    public function updatePrice($product_ids, $option, $number)
    {
        try {
            $productCollection = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect('price')
                ->addFieldToFilter('entity_id',['in' => $product_ids]);
            $this->_setPrice($productCollection, $option, $number);
            if(!$this->_error) {
                $productCollection->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('cgi_blog')->__(
                        'Total of %d record(s) were updated.', count($product_ids)
                    )
                );
            } else {
                Mage::getSingleton('adminhtml/session')->addError($this->_error);
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }

    /**
     * Set product prices.
     *
     * @return void
     */
    protected function _setPrice($productCollection, $option, $number)
    {
        $ids = [];
        foreach($productCollection as $product){
            $oldPrice = $product->getPrice();
            $newPrice = $this->_countNewPrice($option, $oldPrice, $number);
            if(Mage::helper('cgi_updateprice/validator')->validateNewPrice($newPrice)){
                $product->setPrice($newPrice);
            } else {
                $ids[] = $product->getId();
            }
        }
        if($ids){
            $ids = implode(',', $ids);
            $this->_error = 'Products with id ' . $ids . ' has negative new price!';
        }
    }

    /**
     * Calculate new price depending on the option.
     *
     * @param int|float $oldPrice Old product price.
     * @return int|float
     */
    protected function _countNewPrice($option, $oldPrice, $number)
    {
        switch ($option){
            case self::ADD_OPTION:
                $newPrice = $oldPrice + $number;
                break;
            case self::SUBSTR_OPTION:
                $newPrice = $oldPrice - $number;
                break;
            case self::MULTILPE_OPTION:
                $newPrice = $oldPrice * $number;
                break;
            case self::ADD_PERCENT_OPTION:
                $newPrice = $oldPrice + ($oldPrice * $number)/100;
                break;
            case self::SUBSTR_PERCENT_OPTION:
                $newPrice = $oldPrice - ($oldPrice * $number)/100;
                break;
        }
        return $newPrice;
    }

}