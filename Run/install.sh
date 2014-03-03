mysql -e 'create database ecminer';
mysql -h localhost -p -u root ecminer < ../Database/ecminer.sql --default-character-set='utf8';
mysql -h localhost -p -u root ecminer < ../Database/functions.sql --default-character-set='utf8';
mysql -e 'create database prestashop';
mysql -h localhost -p -u root prestashop < ../Database/prestashop.sql --default-character-set='utf8';
mysql -e 'create database oscommerce';
mysql -h localhost -p -u root oscommerce < ../Database/oscommerce.sql --default-character-set='utf8';
cp ../Code/ecminer /var/www/ecminer -R
chown $USER /var/www/ecminer -R
chgrp $USER /var/www/ecminer -R
chmod 777 /var/www/ecminer -R
cp ../Code/prestashop /var/www/prestashop -R
chown $USER /var/www/prestashop -R
chgrp $USER /var/www/prestashop -R
chmod 777 /var/www/prestashop -R
cp ../Code/oscommerce /var/www/oscommerce -R
chown $USER /var/www/oscommerce -R
chgrp $USER /var/www/oscommerce -R
chmod 777 /var/www/oscommerce -R
