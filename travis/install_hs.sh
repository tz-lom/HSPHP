sudo apt-get update
sudo apt-get install mysql-source-5.5
git clone https://github.com/DeNADev/HandlerSocket-Plugin-for-MySQL.git hs
cd hs
./autogen.sh
cp /usr/src/mysql/mysql-source-5.5.tar.gz .
tar -zxf mysql-source-5.5.tar.gz
./configure --with-mysql-source=mysql-5.5/
make
sudo make install
sudo cp ../travis/hs.cnf /etc/mysql/conf.d/hs.cnf
sudo service mysql restart
