讓設定生效
# systemctl restart firewalld
檢視設定是否生效
# iptables -L -n | grep 21

启用端口 
#firewall-cmd [--zone=<zone>] --add-port=<port>[-<port>]/<protocol> [--timeout=<seconds>]


永久開放 ftp 服務
# firewall-cmd --zone=public --add-service=ftp --permanent
# firewall-cmd --zone=public --add-service=ftp --permanent

# firewall-cmd --zone=public --add-port=80/tcp --permanent
# firewall-cmd --zone=public --add-port=80/tcp --permanent



永久關閉
# firewall-cmd --zone=public --remove-service=ftp --permanent


查詢服務的啟用狀態
# firewall-cmd --query-service ftp
yes
# firewall-cmd --query-service ssh
yes
# firewall-cmd --query-service samba
no
# firewall-cmd --query-service http
no