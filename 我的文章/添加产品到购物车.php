<?php
class addProductToShoppingCart{
    ##############################
    private function hasOptions($product) {
        if ($product->getTypeInstance(true)->hasOptions( $product ) ) {
            return true;
        }
        return false;
    }
    private function hasRequiredOptions($product) {
        return $product->getTypeInstance(true)->hasRequiredOptions($product);
    }
    #Get product options
    private function getOptions($product){
        return $product->getOptions();
    }
    #Get product options detail
    private function getRadioOptionDetail($productOptions){
        $result = null;
        foreach($productOptions as $_option){
            $optionvValues = $_option->getValues();
            if($optionvValues && $_option->getType() == 'radio'){
                $option_id = $_option->getoption_id();
                $result[$option_id] = $_option->getData();
                foreach($optionvValues as $optionDetail){
                    $result[$option_id]['detail'][] = $optionDetail->getData();
                }
            }
        }
        return $result;
    }
    /**
    * 添加产品购物车
    * $options数据格式 just radio
     $options = array(
                   'option_id1' => 'option_type_id1'
                  ,'option_id2' => array('option_type_id2-1', 'option_type_id2-2', 'option_type_id2-3', ...)
                  ,'option_id3' => 'option_type_id1'
               );
    * 
    * @param mixed $product_id
    * @param mixed $qty
    * @param mixed $options
    * @param mixed $related_product
    */
    private function cartParamsProcess($product_id = 0,$qty = 1,$options = null, $related_product = ''){
        $preOption = null;
        if(!is_null($options)){
            foreach($options as $option_id => $option){
                $currentOptionId = $option['option_id'];
                if( count($option['detail']) === 1){
                    $opitonItem = end($option['detail']);
                    $preOption[$currentOptionId] = $opitonItem['option_type_id'];
                }else{
                    foreach($option['detail'] as $detail){
                        $preOption[$currentOptionId][] = $detail['option_type_id'];
                    }
                }
                
            }
        }
        $options = $preOption;
        /*
        $params = array(
            'product' => intval($product_id)
            ,'related_product' => $related_product
            ,'options' => array (
                    '7' => '27'
                   ,'6' => array ( 22,23,24 )
                )
            ,'qty' => $qty
        );
        */
        $params = array(
            'product' => intval($product_id)
            ,'related_product' => $related_product
            ,'options' => $options
            ,'qty' => $qty
        );
        return $params;
    }
    /**
    * 添加产品到购物车
    * 
    * @param mixed $params
    */
    private function addItemCart($params){
        $session = Mage::getSingleton('checkout/session');
        $cart   = Mage::getModel('checkout/cart');
        try{
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = Mage::getModel('catalog/product')->load($params['product']);
            $related = $params['related_product'];
            if (!$product) {
                $this->_goBack();
                return;
            }

            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }
            $cart->save();
            $session->setCartWasUpdated(true);
            
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    public function testAction(){
        $id = (int)$this->getRequest()->getParam('id', 0);
        $product = Mage::getModel('catalog/product')->load($id);
        $hasOpitons = $this->hasOptions($product);
        $options = null;
        if($hasOpitons){
            $_options = $this->getOptions($product);        
            $options = $this->getRadioOptionDetail($_options);
            #$this->wlog($options);
        }
        $cartParams = $this->cartParamsProcess($id, 1, $options);
        $_GET = $cartParams;
        $addCartRid = $this->addItemCart($cartParams);
        $this->wlog($cartParams);
        $this->wlog(array('$addCartRid' => $addCartRid));
        exit;
    }
    ##############################
}
?>
