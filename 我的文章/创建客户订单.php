<?php
class createOrder{
    public function newOrder(){
        $product_id = Mage::getModel('catalog/product')->getProductId();
        $customer = Mage::getModel('customer/customer')->load(8523);
        try{
            $order = $this->_createOrder($product_id, $customer, 100);
            #$order = $this->maintenanceToOrder_Backend($associateOrder, $currentItem);
            print_r($order->getIncrementId());
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }
    protected function _createOrder($productId, $customer, $qty = 1) { 
      $post ['product_id'] [0] = $productId; 
      $post ['product_qty'] [0] = $qty; 
       
      $shoppingCart = array (); 
      for($i = 0; $i < count ( $post ['product_id'] ); $i ++) { 
         $productId = $post ['product_id'] [$i]; 
         $productQty = $post ['product_qty'] [$i]; 
         $product = Mage::getModel ( 'catalog/product' )->load ( $productId ); 
         $shoppingCart [] = array ( 
               'product' => $product, 
               'productQty' => $productQty 
         ); 
      } 
       
      $params = array ( 
            'customer' => $customer, 
            'shoppingCart' => $shoppingCart 
      ); 
      
      $quote = $this->_prepareOrder ( $params ); 
      $order = $this->_confirmOrder ( $quote ); 
      $xdata['order_id'] = $order->getId();
      $xdata['order_increment_id'] = $order->getincrement_id();
      $xdata['customer_id'] = $order->getcustomer_id();
      $xdata['email'] = $order->getcustomer_email();
      $xdata['firstname'] = $order->getcustomer_firstname();
      $xdata['lastname'] = $order->getcustomer_lastname();
      $xdata['country_id'] = $customer->getPrimaryShippingAddress()->getcountry_id();
      
      return $xdata;
      return $order;
   } 
   /**
   * 订单预处理数据
   * 
   * @param mixed $params
   * @return Mage_Core_Model_Abstract
   */
   protected function _prepareOrder($params) { 
      $storeId = Mage::app ()->getStore ()->getId (); 
      $quote = Mage::getModel ( 'sales/quote' );
      $store = $quote->getStore ()->load ( $storeId ); 
      $quote->setStore ( $store ); 
       
      $quote->removeAllItems(); 
      foreach ( $params['shoppingCart'] as $products ) { 
         $quoteItem = Mage::getModel ( 'sales/quote_item' ); 
         $quoteItem->setQuote ( $quote ); 
         $quoteItem->setProduct ( $products['product'] );          
         $quoteItem->setQty ( $products['productQty'] ); 
          
         $quote->addItem ( $quoteItem ); 
      } 
       
      #$quote->loadByCustomer($params['customer']); 
      /*
      $quote->loadByCustomer((int)$params['customer']->getId());
      $shippingAddress = $quote->getShippingAddress();
      $this->wlog($shippingAddress->getData());
      $shippingAddress->setShippingMethod ( 'your-shipping-method' ); 
      $rate = new Mage_Sales_Model_Quote_Address_Rate (); 
      $rate->setCode ( 'your-shipping-method' ); 
      $shippingAddress->addShippingRate ( $rate ); 
      $shippingAddress->setCollectShippingRates ( true ); 
       
      $quote->getPayment ()->setMethod ( 'your-payment-method' ); 
      */
      $quote->assignCustomer($params['customer']);
      #$this->wlog($params['customer']->getData());
      $billingAddress = $quote->getBillingAddress()->addData($params['customer']->getPrimaryBillingAddress());
      $shippingAddress = $quote->getShippingAddress()->addData($params['customer']->getPrimaryShippingAddress());
      $shippingAddress->setCollectShippingRates(true)->collectShippingRates()
                        ->setShippingMethod('your-shipping-method')
                        ->setPaymentMethod('your-payment-method');
                        
      $quote->getPayment()->importData(array('method' => 'your-payment-method'));
      
      $quote->collectTotals ()->save (); 
       
      return $quote; 
   }
   /**
   * 确认订单
   * 
   * @param mixed $quote
   */
   protected function _confirmOrder($quote) { 
      $service = Mage::getModel ( 'sales/service_quote', $quote ); 
      $service->submitAll (); 
      Mage::getSingleton ( 'checkout/session' )->setLastQuoteId ( $quote->getId () )->setLastSuccessQuoteId ( $quote->getId () )->clearHelperData (); 
      $order = $service->getOrder (); 
      $quote->removeAllItems(); 
      return $order;
   }
   
   
   #---------------------------------------------------------------------------------------------------------------------------
   public function maintenanceToOrder_Backend($associateOrder, $currentItem = ''){
        $adminSession = Mage::getSingleton('adminhtml/session_quote');
        $customer = Mage::getModel('customer/customer')->load($associateOrder->getCustomerId());
        $address = $customer->getPrimaryShippingAddress();
        $product = $this->getProduct(201);
        
        $orderData = array(
                'currency' => 'CNY',
                'account'  => array(
                    'group_id' => Mage_Customer_Model_Group::NOT_LOGGED_IN_ID,
                    'email'    => $customer->getEmail()
                ),
                'billing_address' => array(
                    'firstname'  => $address->getFirstname(),
                    'lastname'   => $address->getLastname(),
                    'street'     => $address->getStreet(),
                    'city'       => $address->getCity(),
                    'country_id' => $address->getCountryId(),
                    'region_id'  => $address->getRegionId(),
                    'postcode'   => $address->getTelephone(),
                    'telephone'  => $address->getPostcode(),
                ),
                #'comment' => array( 'customer_note' => "服务工单号 : " . $currentItem->getIncrementId() ),
                'comment' => array( 'customer_note' => $currentItem ),
                'send_confirmation' => false # does that something?
        );
        $orderData['shipping_address'] = $orderData['billing_address'];
        $paymentMethod = 'your-payment-method';
        $shippingMethod = 'your-shipping-method';
        $this->wlog($orderData);
        
        # Get the backend quote session
        $quoteSession = $adminSession;
        # Set the session store id
        $quoteSession->setStoreId(Mage::app()->getStore('default')->getId());
        # Set the session customer id
        $quoteSession->setCustomerId($customer->getId());
        # Get the backend order create model
        $orderCreate = Mage::getSingleton('adminhtml/sales_order_create');
        # Import the data
        $orderCreate->importPostData($orderData);
        # Calculate the shipping rates
        $orderCreate->collectShippingRates();
        # Set the shipping method
        $orderCreate->setPaymentMethod($paymentMethod);
        # Set the payment method to the payment instance
        $orderCreate->getQuote()->getPayment()->addData(array('method' => $paymentMethod));
        # Set the shipping method
        $orderCreate->setShippingMethod($shippingMethod);
        # Set the quote shipping address shipping method
        $orderCreate->getQuote()->getShippingAddress()->setShippingMethod($shippingMethod);
        # Add the product
        $orderCreate->addProducts(array($product->getId() => array('qty' => 0)));
        # Initialize data for price rules
        $orderCreate->initRuleData();
        # Save the quote
        $orderCreate->saveQuote(); # neccessary?
        # Create the order
        $order = $orderCreate->createOrder();
        $quoteSession->clear();
        $adminSession->clear();
        
        return $order;
        #------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        
        
        $adminOrder = Mage::getSingleton('adminhtml/sales_order_create');
        $adminOrder->importPostData($data['order']);
        $adminOrder->getBillingAddress();
        $adminOrder->setShippingAsBilling(true);
        if (!empty($data['add_products'])) {
            $adminOrder->addProducts($data['add_products']);
        }
        #Collect shipping rates
        $adminOrder->collectShippingRates();
        #Add payment data
        if (!empty($data['payment'])) {
            $adminOrder->getQuote()->getPayment()->addData($data['payment']);
        }
        $adminOrder->initRuleData()->saveQuote();
        if (!empty($data['payment'])) {
            $adminOrder->getQuote()->getPayment()->addData($data['payment']);
        }
        #xxx
        if (!empty($orderData['payment'])) {
            $adminOrder->setPaymentData($orderData['payment']);
            $adminOrder->getQuote()->getPayment()->addData($orderData['payment']);
        }
        /*
        $item = $adminOrder->getQuote()->getItemByProduct($this->_product);
        $item->addOption(new Varien_Object(array(
            'product' => $this->_product,
            'code' => 'option_ids',
            'value' => '5'
            #Option id goes here. If more options, then comma separate
        )));
        $item->addOption(new Varien_Object(array(
            'product' => $this->_product,
            'code' => 'option_5',
            'value' => 'Some value here'
        )));
        */
        Mage::app()->getStore()->setConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_ENABLED, "0");
        $_order = $adminOrder->importPostData($orderData['order'])->createOrder();
        $adminSession->clear();
        return $_order;
        
        $order = Mage::getModel('sales/order');
        $order->setQuote($quoteModelInstance);
        $order->setCustomer($customerModelInstance);
        $order->setPayment($paymentModelInstance);
        $order->setShipping($customerModelInstance->getShippingRelatedInfo());
        $order->save();
    }
}
?>
