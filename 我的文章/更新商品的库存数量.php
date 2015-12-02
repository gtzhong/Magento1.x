<?php
$product_id = 1;
#获取产品
$product = Mage::getModel('catalog/product')->load( $product_id );
#获取产品当前的库存数量
$currentQty = $product->getstock_item()->getQty();
#商品库存管理对象
$stock_item = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);

#准备要更新的库存数量, 在当前的数量再增加1
$finalQty =  $currentQty + 1;
#保存并更新库存数量
$stockItem->setQty($finalQty)->save();

?>
