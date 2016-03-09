MAGENTO 开发之 DISPATCHEVENT
以个人的经验， 事件的分发使用频率应该高于对类的重写(overriding)， 为什么这么说呢， 当有多个模块的时候， 重写同一个类(class)时，那它们互相将会有冲突， 只有一个模块将会正常工作， 但是如果你使用事件的话， 那么多个模块都可以很轻松的去调用它

Magento 中的事件也是根据观察者(Observer)设计模式， 它是这样工作的， 在 Magento 核心代码中， 许多地方都分发了事件， 每一个事件都有自己唯一的名字和其他相关的参数， 在我们自己的模块中， 同样也可以调用这些事件， 当 Magento 分发这些事件的时候， 在我们自己模块中的一个方法将会被触发， 在这个方法中我们可以进行相关的操作

Magento 调用 Mage::dispatchEvent() 方法来分发事件， 你全盘搜索一下的话，会发现 Magento 代码中很多地方都调用了

现在我们来拿 Mage_Checkout_Model_Type_Onepage 类中的 saveOrder 方法来举例
===================================================================================================
Mage::dispatchEvent(
                'checkout_type_onepage_save_order_after', 
                array(
                    'order' => $order,
                    'quote' => $this->getQuote()
                )
);
===================================================================================================
每一个事件都有自己的名字和相关参数
在上述方法中， ‘checkout_type_onepage_save_order_after’ 就是自己的名字， array(‘order’=>$order, ‘quote’=>$this->getQuote()) 就是相关参数

在我们的模块中如果想调用(subscribe)或监听(listen)这个事件的时候， 需要添加如下代码至 config.xml 文件中

===================================================================================================
<events>
    <checkout_type_onepage_save_order_after> <!-- 事件的名字 -->
        <observers>
            <save_after> <!-- 任何唯一的标示符 -->
                <type>singleton</type>
                <class>Excellence_Test_Model_Observer</class> <!-- 我们自己的类(class) -->
                <method>saveOrderAfter</method> <!-- 方法名 -->
            </save_after>
        </observers>
    </checkout_type_onepage_save_order_after>    
</events>
===================================================================================================

现在在我们自己模块的 Model 文件夹中建立一个 Observer.php 文件， 随后定义一个方法名为: saveOrderAfter()
===================================================================================================
class Excellence_Test_Model_Observer 
{
    public function saveOrderAfter($evt){
        $order = $evt->getOrder(); //这样就能获得到在 Mage::dispatchEvent() 方法中传的参数
        $quote = $evt->getQuote(); //这样就能获得到在 Mage::dispatchEvent() 方法中传的参数
        /*
        ....
        这里可以执行相关操作
        发送邮件
        等等...
        ....
        */
    }
}
===================================================================================================

另外，每当一个模块(Model)执行完保存之后，这两个事件会被触发:
===================================================================================================
Mage::dispatchEvent(
                'model_save_before', 
                array('object' => $this)
);

Mage::dispatchEvent(
                'model_save_after', 
                array('object'=>$this)
);
===================================================================================================