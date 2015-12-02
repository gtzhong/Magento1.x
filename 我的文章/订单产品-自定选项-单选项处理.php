<?php
class orderProductCustomOption{
    /**
    * 获取产品所有单选项的全部内容
    * 
    * @param mixed $product
    */
    public function getProductRadioOptions($product){
        $options = $product->getOptions();
        $result = null;
        foreach($options as $o) {
            $type = $o->getType();#radio
            $title = $o->getTitle();
            $values = $o->getValues();
            foreach ($values as $v) {
                $result[$v->getoption_type_id()] = $v;
            }
        }
        return $result;
    }
    /**
    * 获取订单中商品被选定的【 自定义选项 / custom_option 】的对象列表
    * 
    * @param mixed $options
    */
    public function getOrderItemCustomOption($productAllOptions, $options){
        $result = null;
        foreach($options as $option_id => $option_type_id ){
            foreach($productAllOptions as $customOption){
                if($customOption->getoption_id() == $option_id && $customOption->getoption_type_id() == $option_type_id){
                    $result[] = $customOption;
                }
            }
            
        }
        return $result;
    }
}
#*******************************************************************************************************************
$order_id = 1;
#获取订单
$order = Mage::getModel('sales/order')->load($order_id);
#获取并循环订单的所有产品
foreach($order->getAllItems() as $currentItem){
    $product_id = $currentItem->getproduct_id();
    #获取产品
    $product = Mage::getModel('catalog/product')->load( $product_id );
    #获取订购产品的产品选项
    $product_options = unserialize($currentItem->getproduct_options());
    #检测订单的产品当是否有【 自定义选项 / custom_options 】
    if(isset($product_options['options'])){
        #$product_options['info_buyRequest']['options']的值是 [$option_id]=> $option_type_id
        $options = $product_options['info_buyRequest']['options'];
        
        #产品所有自定义选项中所有的单选项的列表
        $productAllOptions = orderProductCustomOption::getProductRadioOptions($product);
        
        #获取被选定项的详情
        $result = orderProductCustomOption::getOrderItemCustomOption($productAllOptions, $options);
    }
}
?>
