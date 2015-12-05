<?php
/**
* 检测用户的使用设备，根据不同的设备类型选择指定显示不同事的店铺 Store View
* 
* @var mixed
*/
class storeByDeviceType{
    private $allowStyle = array("rwd","mobile","yourthemname","base","default");
    protected $devicesStyle = "";
    protected $currentStore = "";
    public function __construct($currentStore = ''){
        $this->currentStore = $currentStore;
    }
    public function customerDevicesStyle(){
        $this->devicesStyle = 'default';
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        #微信版
        $eregRule = 'MicroMessenger';
        if(preg_match('/('.strtolower($eregRule).')/i', strtolower($userAgent))){
            $this->devicesStyle = 'micro';
        }
        #手机移动版
        $eregRule = 'iPhone|iPod|BlackBerry|Palm|Googlebot-Mobile|Mobile|mobile|mobi|Windows Mobile|Safari Mobile|Android|Opera Mini';
        if(preg_match('/('.strtolower($eregRule).')/i', strtolower($userAgent))){
            $this->devicesStyle = 'mobilephone';
        }
        return $this;
    }
    public function init(){
        if($this->devicesStyle != ""){
            if($this->devicesStyle != $this->currentStore){
                $_GET['___store'] = $this->devicesStyle;
            }
        }
    }
}


/**
* 1. 在文件Mage.php中包含进这个这代码的, 或者把文件放在lib/Varien/的目录中
* 2. 在index文件的倒数第一代码加入判断
    #================= setting store by cuttomer's devices type ===========================
    #$gigasetInit = new storeByDeviceType( Mage::app()->getStore()->getCode() );
    #$gigasetInit->customerDevicesStyle()->init();
    #================= setting store by cuttomer's devices type ===========================
*/
?>