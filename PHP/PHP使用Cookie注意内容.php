<?php
#����Cookie,   
setcookie($cookie_name, $cookie_value, $cookie_expire, $path, $domain);
#ɾ��
setcookie($cookie_name, "", time() - 36000, $path, $domain);
#��ȡ
$aaa = $_COOKIE[$cookie_name];


#�����û���ɾ����ʱ��Ҫָ�� $path, $domain�Ĳ����� ������Ӻ�ɾ����ʱ��
�������������뱣��һ�£������ȡ���������������⣬�����޷�ɾ����Ҫɾ��������



?>