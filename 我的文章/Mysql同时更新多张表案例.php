<?php

function updateGivepresentItemStock($order, $isNew = false){
        $sql = "";
        $increment_id = $order->getincrement_id();
        if($isNew == false){
            #取消订单时的SQL
            $sql = "UPDATE gigaset_givepresent_b1s1m AS gb,cataloginventory_stock_item AS si SET si.qty=si.qty+gb.qty,gb.status=3 "
                  ."WHERE gb.`increment_id`='".$increment_id."' AND gb.type=1 AND gb.`product_id`>0 AND gb.`product_id`=si.`product_id` AND gb.status=2";
        }
        if($isNew == true){
            #生成订单时的SQL
            $sql = "UPDATE gigaset_givepresent_b1s1m AS gb,cataloginventory_stock_item AS si SET si.qty=si.qty-gb.qty,gb.status=2 "
                  ."WHERE gb.`increment_id`='".$increment_id."' AND gb.type=1 AND gb.`product_id`>0 AND gb.`product_id`=si.`product_id` AND gb.status=1";
        }
        
        $handleWrite = $this->getSqlHandle();
        
        if($handleWrite  && strlen($sql) > 0   &&   strlen($increment_id) > 5){
            $handleWrite = $this->getSqlHandle();
            $handleWrite->query($sql);
        }
        
    }