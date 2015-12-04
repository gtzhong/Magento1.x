<?php
  //获取magento网店当前货币code：

$currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();


//获取magento网店当前货币符号：

$currency_code = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();


//得到当前购物车中的全部产品的金额：

$grandTotal = Mage::getModel('checkout/cart')->getQuote()->getGrandTotal();


//获取当前购物车中所有购买的商品ID

$grandTotal = Mage::getModel('checkout/cart')->getQuoteProductIds();


//获取当前购物车里所有商品总量

$grandTotal = Mage::getModel('checkout/cart')->getSummaryQty();


//OR

$grandTotal = Mage::getModel('checkout/cart')->getItemsQty();


//获取当前自己收藏的商品总量

$grandTotal = Mage::getModel('checkout/cart')->getItemsCount();
?>
