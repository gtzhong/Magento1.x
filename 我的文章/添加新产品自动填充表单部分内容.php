<?php
#edit.phtml 最后面加本段内容
$lastProduct =  Mage::getModel('catalog/product')->getCollection()->setOrder('entity_id','DESC')->getFirstItem();
$autoSkuPID =  $lastProduct->getId();
$autoQuickId =  $lastProduct->getquick_id();
if($autoSkuPID < 10000){
        $autoSkuPID = 10000 + $autoSkuPID;
}
$price = mt_rand(101, 999).'.'.mt_rand(1,99);
?>

<script type="text/javascript">
var disableFieldName = "<?php echo Mage::getSingleton('admin/session')->getUser()->getUsername();?>";
var is_new_sku = false;
if(document.getElementById('sku').value == ""){
    is_new_sku = true;
    document.getElementById('sku').value = "sku<?php echo ($autoSkuPID + 1); ?>";
    document.getElementById('status').value = 1;
    document.getElementById('tax_class_id').value = 0;
    document.getElementById('weight').value = 1;
    document.getElementById('price').value = "<?php echo $price?>";
}

if(document.getElementById('inventory_qty').value < 1){
    document.getElementById('inventory_qty').value = "10000";
}

if(document.getElementById('inventory_stock_availability').value < 1){
    document.getElementById('inventory_stock_availability').value = 1;
}
//----------------------------------------------------------
var frmEV={
    is_new:is_new_sku,
    short_description:function(){
        if(this.value.length < 2){
            this.value = "Short Description <br>\r\n" + $('name').value + ' <br> ' + $('name').value + ' <br> ' + $('name').value + ' <br> ' + $('name').value + ' <br> ' + $('name').value + ' <br> ' + $('name').value;
            $('description').value = this.value+this.value+ ' <br> '+this.value+ ' <br> '+this.value+ ' <br> '+this.value;
        }
    }
};
$('short_description').observe('click', frmEV.short_description);
</script>