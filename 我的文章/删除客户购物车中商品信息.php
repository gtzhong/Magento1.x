<?php
require_once 'app/Mage.php';
Mage::app()->getConfig()->getTempVarDir();

$customer = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getWebsite()->getId());
$customerId = $customerEmail = '';
if ( isset($_REQUEST['customerId']) ) {
    $customerId = $_REQUEST['customerId'];
}

if ( isset($_REQUEST['customerEmail']) ) {
    $customerEmail = $_REQUEST['customerEmail'];
}
    
if (isset($customerId) and $customerId != '') {  
    $customer = $customer->load($customerId);
} elseif (isset($customerEmail) and $customerEmail != '' ) {
    $customer = $customer->loadByEmail($customerEmail);
} else {
   echo "customerId or customerEmail parameter required";
   exit;
}

if ($customer->getId()) {
    $storeIds = Mage::app()->getWebsite(Mage::app()->getWebsite()->getId())->getStoreIds();
    $quote = Mage::getModel('sales/quote')->setSharedStoreIds($storeIds)->loadByCustomer($customer);
    if ($quote) {
        $collection = $quote->getItemsCollection(false);
        if ($collection->count() > 0) {
            foreach( $collection as $item ) {
                if ($item && $item->getId()) {
                    $quote->removeItem($item->getId());
                    $quote->collectTotals()->save();
                }
            }
        }
    }
}
