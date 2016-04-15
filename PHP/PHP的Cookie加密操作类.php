<?php
#客户Cookie
class xxxxx_Cookie {
    const XML_Q2O = 'common2'; //前缀
    const COOKIE_KEY = 'common_'; //前缀
    const COOKIE_KEY_METHOD_PAYMENT = 'payment_'; //前缀
    const COOKIE_KEY_METHOD_SHIPPING= 'shipping_'; //前缀
    const COOKIE_KEY_PRODUCTS = 'PID'; //前缀
    const COOKIE_KEY_PRODUCT_ID = 'PID'; //前缀
    const COOKIE_KEY_EXPIRE_DAY = 'expire_day';
    const COOKIE_KEY_CART_PATH = '/';//Cookie存储路径
    private $_securekey = 'ekOt4_Ut0f3XE-fJcpBvRFrg506jpcuJeixezgPNyALm'; //加密密钥 encrypt key
    private $_expire = 31536000; //设置默认过期时间 1 * 365 * 24 * 3600  小时
    
    public function wlog($message) {
        Mage::helper('ck_q2o')->wlog($message);
    }
    public function getExpire() {
        $expireValue = $this->_expire;
        $expire_day = trim(Mage::getStoreConfig(self::XML_Q2O . DS . self::COOKIE_KEY_EXPIRE_DAY));
        if($expire_day > 0){
            $expireValue = 1 * $expire_day  * 24 * 3600;
        }
        return $expireValue;
    }
    #添加
    public function set($name, $value, $expire = 0) {
        $cookie_name = self::COOKIE_KEY.$name;
        $cookie_expire = time() + ($expire ? $expire : $this->getExpire());
        $cookie_value = $this->pack($value, $cookie_expire);
        $cookie_value = $this->authcode($cookie_value, 'ENCODE');
        if ($cookie_name && $cookie_value && $cookie_expire) {
            setcookie($cookie_name, $cookie_value, $cookie_expire,self::COOKIE_KEY_CART_PATH);
            $this->wlog("Cookie[+]\t\t" . $cookie_name . "\t\t". $cookie_value);
        }
    }
    #获取
    public function get($name) {
        $cookie_name = self::COOKIE_KEY.$name;
        if (isset($_COOKIE[$cookie_name])) {
            #$this->wlog("Cookie[g]\t\t" . $cookie_name);
            $cookie_value = $this->authcode($_COOKIE[$cookie_name], 'DECODE');
            $cookie_value = $this->unpack($cookie_value);
            return $cookie_value;
        } else {
            $this->clear($name);
            return null;
        }
    }
    #更新
    public function update($name, $value) {
        $cookie_name = self::COOKIE_KEY.$name;
        
        #if (isset($_COOKIE[$cookie_name])) {
            $old_cookie_value = $this->authcode($_COOKIE[$cookie_name], 'DECODE');
            $old_cookie_value = $this->unpack($old_cookie_value);
            $this->set($name, $value);
        #}
    }
    #直接删除
    public function clear($name) {
        $cookie_name = self::COOKIE_KEY.$name;
        setcookie($cookie_name,"", time() - 36000, self::COOKIE_KEY_CART_PATH);
        $this->wlog("Cookie[-]\t\t" . $cookie_name);
        #unset($_COOKIE[$cookie_name]);
    }
    private function pack($data) {
        if ($data === '') {
            return '';
        }
        return json_encode($data);
    }
    private function unpack($data) {
        if ($data === '') {
            return '';
        }
        $cookie_data = json_decode($data, true);
        return $cookie_data;
    }
    private function authcode($string, $operation = 'DECODE') {
        $ckey_length = 4;
        $key = $this->_securekey;
        $key = md5($key);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()) , -$ckey_length)) : '';
        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);
        $string = ($operation == 'DECODE') ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', 0) . substr(md5($string . $keyb) , 0, 16) . $string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result.= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb) , 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc . str_replace('=', '', base64_encode($result));
        }
    }
    
    public function addProduct($product_id, $value){
        $keyName = self::COOKIE_KEY_PRODUCT_ID.$product_id;
        
        $this->set($keyName, $value);
    }
    public function updateProduct($product_id, $value){
        $keyName = self::COOKIE_KEY_PRODUCT_ID.$product_id;
        $this->update($keyName, $value);
    }
    public function delProduct($product_id){
        $keyName = self::COOKIE_KEY_PRODUCT_ID.$product_id;
        $this->clear($keyName);
    }
    #添加/更新产品的数据
    public function addItems($products = array()){
        $keyName = self::COOKIE_KEY_PRODUCTS;
        $product_ids = array();
        if(count($products) > 0){
            #删除原来所有的产品Cookie
            $old_ids = $this->get($keyName);
            $old_products = null;#储存旧的产品数据
            if($old_ids){
                foreach($old_ids as $md5keystr){
                    $keyNameForId = $keyName . $md5keystr;
                    $old_products[$md5keystr] = $this->get($keyNameForId);
                    $this->clear($keyNameForId);
                }
            }
            #更新产品相关Cookie
            $new_product = $old_products;
            foreach($products as $md5keystr => $product_val){
                $final_product_id = $md5keystr;
                $new_product[$final_product_id] = $product_val;
            }
            $product_ids = array_keys($new_product);
            $this->set($keyName, $product_ids);
            foreach($new_product as $md5keystr => $product_val){
                $this->addProduct($md5keystr, $product_val);
            }
        }
    }
    #删除产品
    public function delItems($products = array()){
        $keyName = self::COOKIE_KEY_PRODUCTS;
        $product_ids = array();
        if(count($products) > 0){
            #删除原来所有的产品Cookie
            $old_ids = $this->get($keyName);
            if($old_ids){
                $product_ids = array_keys($products);
                $diff_ids = array_diff($old_ids,$product_ids);
                $this->set($keyName, $diff_ids);
                foreach($product_ids as $product_id){
                    $keyNameForId = $keyName . $product_id;
                    $this->clear($keyNameForId);
                }
                if(count($diff_ids) == 0){
                    $this->clear($keyName);
                }
            }
        }
    }
    #获取
    public function getItems(){
        $items = null;
        $products = array();
        $keyName = self::COOKIE_KEY_PRODUCTS;
        $product_ids = $this->get($keyName);
        if($product_ids == "" || $product_ids == 0){
            return null;
        }
        if(count($product_ids) > 0){
            foreach($product_ids as $product_id){
                $keyNameForId = $keyName . $product_id;
                $products[$product_id] = $this->get($keyNameForId);
            }
        }
        $items = array('product_ids' => $product_ids, 'products' => $products);
        return $items;
    }
    
    #保存支付方式
    public function setPaymentMethod($value){
        $keyName = self::COOKIE_KEY_METHOD_PAYMENT;
        $this->set($keyName, $value);
    }
    #保存物流运输方式
    public function setShipMethod($value){
        $keyName = self::COOKIE_KEY_METHOD_SHIPPING;
        $this->set($keyName, $value);
    }
    #获取支付方式
    public function getPaymentMethod(){
        $keyName = self::COOKIE_KEY_METHOD_PAYMENT;
        $this->get($keyName);
    }
    #获取物流运输方式
    public function getShipMethod(){
        $keyName = self::COOKIE_KEY_METHOD_SHIPPING;
        $this->set($keyName);
    }
}

