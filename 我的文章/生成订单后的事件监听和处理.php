<?php
/**
config.xml部分  

<!--监听取消订单后-->
<order_cancel_after>
    <observers>
      <yourspace_givepresent_observer>
        <type>singleton</type>
        <class>Yourspace_Givepresent_Model_Observer</class>
        <method>cancelItems</method>
      </yourspace_givepresent_observer>
    </observers>
</order_cancel_after>

<!--监听生成订单后-->
<sales_order_place_after>
    <observers>
      <yourspace_givepresent_observer>
        <type>singleton</type>
        <class>Yourspace_Givepresent_Model_Observer</class>
        <method>updateItems</method>
      </yourspace_givepresent_observer>
    </observers>
</sales_order_place_after>

*/

class Yourspace_Givepresent_Model_Observer {
    public function cancelItems($observer){
        #获取被取消订单的 order_id 编号
        $id = $observer->getEvent()->getOrder()->getId();
        
        $this->orderItemsProcess($observer);
    }
    public function updateItems($observer){
        #获取被取消订单的 order_id 编号
        $id = $observer->getEvent()->getOrder()->getId();
        
        $this->orderItemsProcess($observer);
    }
    /**
    * 循环处理订单中的每个产品
    * 
    * @param mixed $observer
    */
    public function orderItemsProcess(&$observer){
        #获取被的取消订单
        $order = $observer->getEvent()->getOrder();
        #循环处理订单产品
        foreach($order->getAllItems() as $currentItem){
            $product_id = $currentItem->getproduct_id();
            #获取产品
            $product = Mage::getModel('catalog/product')->load( $product_id );
        }
    }
}
?>
