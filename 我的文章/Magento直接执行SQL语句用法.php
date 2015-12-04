<?php
class Xxxxxxxx_Givepresent_Helper_Data    extends Mage_Core_Helper_Abstract {

    /**
    * 直接操作数据库句柄
    * 
    */
    private function getSqlHandle(){
        return Mage::getSingleton('core/resource')->getConnection('core_write');
    }
    /**
    * 更新订单中赠品商品的库存信息
    * cancel Order时 $isNew=false, 
    * create Order时 $isNew=true, 
    * @param mixed $isNew
    */
    protected function updateGivepresentItemStock($order, $isNew = false){
        $sql = "";
        $increment_id = $order->getincrement_id();
        if($isNew == false){
            #取消订单时的SQL
            $sql = "UPDATE xxxxxxxx_givepresent_b1s1m AS gb,cataloginventory_stock_item AS si SET si.qty=si.qty+gb.qty,gb.status=3 "
                  ."WHERE gb.`increment_id`='".$increment_id."' AND gb.type=1 AND gb.`product_id`>0 AND gb.`product_id`=si.`product_id` AND gb.status=2";
        }
        if($isNew == true){
            #生成订单时的SQL
            $sql = "UPDATE xxxxxxxx_givepresent_b1s1m AS gb,cataloginventory_stock_item AS si SET si.qty=si.qty-gb.qty,gb.status=2 "
                  ."WHERE gb.`increment_id`='".$increment_id."' AND gb.type=1 AND gb.`product_id`>0 AND gb.`product_id`=si.`product_id` AND gb.status=1";
        }
        
        $handleWrite = $this->getSqlHandle();
        
        if($handleWrite  && strlen($sql) > 0   &&   strlen($increment_id) > 5){
            $handleWrite = $this->getSqlHandle();
            $handleWrite->query($sql);
        }
        
    }
    protected function updateGivepresentItemStock_bak($order, $isNew = false){
        $b1s1m    = Mage::getModel('xxxxxxxx_givepresent/b1s1m')->getCollection()
                    ->addFieldToFilter('order_id', $order->getId())
                    ->addFieldToFilter('type', self::GIVEPRESENT_TYPE)
        ;
        if($b1s1m){
            foreach($b1s1m as $item){
                $product = $this->getProduct($item->getproduct_id());
                $currentQty = $product->getstock_item()->getQty();
                $finalQty = $currentQty - $item->getQty();
                if($isNew == false){
                    $finalQty = $currentQty + $item->getQty();
                }
                #$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
                #$stockItem->setQty($finalQty)->save();
                
                
                $this->wlog(  $item->getincrement_id().'[currentQty/finalQty, '.intval($currentQty).'/'.intval($finalQty).']'.','.self::STR4C.'product_id['.$item->getproduct_id().'],'.self::STR4C.'sku['.$item->getSku().'],'.self::STR4C.'qty['.(($isNew)?'-':'+').']['.$item->getQty().']');
            }
        }
    }
}
