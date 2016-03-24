下面的代码将获取你所有启用的Magento支付模块。下面的例子返回一个数组，你可以用它在Magento的前后台创建一个下拉框或者别的东西。
<?php
function getActivPaymentMethods() {
   $payments = Mage::getSingleton('payment/config')->getActiveMethods();

   $methods = array(array('value'=>'', 'label'=>Mage::helper('adminhtml')->__('--Please Select--')));

   foreach ($payments as $paymentCode=>$paymentModel) {
        $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
        $methods[$paymentCode] = array(
            'label'   => $paymentTitle,
            'value' => $paymentCode,
        );
    }

    return $methods;

} 
