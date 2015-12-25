<?php
class aaa{
  public function test3Action(){
        header("Content-type: text/html; charset=utf-8");
        $addressFrm = array(
            'contact' => 'FirstName LastName'
           ,'street' => date('Ymd').'街, '.date('His').'道, '.now().'号'
           ,'district' => '龙岗区'
           ,'district_id' => '2497'
           ,'city' => '深圳市'
           ,'city_id' => '255'
           ,'country_id' => 'CN'
           ,'provice' => '广东省'
           ,'region_id' => '494'
           ,'tel' => '123456'
        );
        
        $this->saveaddressbook1($addressFrm);
        
        exit;
    }
    protected function saveaddressbook1($addressFrm){
        $customer = Mage::getModel('customer/customer')->load(8521);
        print_r($customer->getPrimaryBillingAddress()->getData());exit;
        #print_r($customer->getData()); exit;
        # ========================================================================
        $addressData = array (   'firstname' => $addressFrm['contact']
                                ,'lastname' => ' - '
                                ,'company' => ''
                                ,'street' => array ( trim($addressFrm['street']) )
                                ,'district' => $addressFrm['district']
                                ,'district_id' => (int)$addressFrm['district_id']
                                ,'city' => trim($addressFrm['city'])
                                ,'city_id' => (int)$addressFrm['city_id']
                                ,'country_id' => $addressFrm['country_id']
                                ,'region' => $addressFrm['provice']
                                ,'region_id' => (int)$addressFrm['region_id']
                                ,'postcode' => ''
                                ,'telephone' => trim($addressFrm['tel'])
                                ,'fax' => ''
                                ,'vat_id' => ''
                                ,'default_billing' => '1'
                                ,'default_shipping' => '1'
                                ,'use_as_default' => 'on'
                        );
        #print_r($addressData);
    
        $customAddress = Mage::getModel("customer/address");
        
        $customAddress->setCustomerId($customer->getId())
                ->setFirstname( $addressData['firstname'] )
                #->setMiddleName($customer->getMiddlename())
                ->setLastname($addressData['lastname'] )
                ->setCountryId($addressData['country_id'] )
                ->setRegionId($addressData['region_id'] )
                ->setRegion($addressData['region'] )
                ->setPostcode($addressData['postcode'] )
                ->setDistrict($addressData['district'] )
                ->setDistrictId($addressData['district_id'] )
                ->setCity($addressData['city'] )
                ->setCityId($addressData['city_id'] )
                ->setTelephone($addressData['telephone'] )
                ->setuni_cellphone($addressData['telephone'] )
                ->setFax('')
                ->setCompany('')
                ->setStreet($addressData['street'] )
                ->setIsDefaultBilling('1')
                ->setIsDefaultShipping('1')
                ->setSaveInAddressBook('1')
        ;
        
        try{
            $customAddress->save();
            $customer->setuni_cellphone('23333333323')->save();
            print_r('客户地址保存成功');
        } catch (Mage_Core_Exception $e) {
            print_r($e->getMessage());
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
        # ========================================================================
    }
}
?>
