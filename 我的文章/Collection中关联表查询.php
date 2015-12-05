<?php
class resourceCollection{
    public function joinProducts() {
        $tableName = Mage::getSingleton('core/resource')->getTableName('catalog/product');
        $productResource = Mage::getResourceSingleton('catalog/product');
        $nameAttr = $productResource->getAttribute('name');
        $nameAttrId = $nameAttr->getAttributeId();
        $nameAttrTable = $nameAttr->getBackend()->getTable();
        $this->getSelect()->joinLeft(
                        array('_table_product_name' => $nameAttrTable),
                        '(_table_product_name.entity_id = main_table.product_id)
                             AND (_table_product_name.attribute_id = ' . (int) $nameAttrId . ')',
                        array('product_name' => '_table_product_name.value')           
                )
                ->joinLeft(
                        array('_table_product_sku' => $tableName),
                        '(_table_product_name.entity_id = _table_product_sku.entity_id)',
                        array('product_sku' => '_table_product_sku.sku')
        );

        return $this;
    }
}
?>
