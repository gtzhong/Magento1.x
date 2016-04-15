<?php
#设置Cookie,   
setcookie($cookie_name, $cookie_value, $cookie_expire, $path, $domain);
#删除
setcookie($cookie_name, "", time() - 36000, $path, $domain);
#获取
$aaa = $_COOKIE[$cookie_name];


#在设置或者删除的时候，要指定 $path, $domain的参数， 并且添加和删除的时候，
这两个参数必须保持一致，否则获取出来的数据有问题，或者无法删除想要删除的数据



?>