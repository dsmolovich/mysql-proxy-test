1. Install VirtualBox
2. Install Vagrant
3. clone repository
4. cd mysql-proxy-test
5. vagrant up dbmaster
6. vagrant up dbslave1
7. vagrant up dbslave2
8. vagrant up web
7. vagrant ssh web
8. cd /vagrant/test
9. sh create_db.sh
10. sh multi_test.sh
11. mysql -hdbproxy -uadmin -padmin --port=4041
12. select * from backends;

Run #12 multiple times to see the spread across db servers.

You'll see something like this:
	mysql> select * from backends;
	+-------------+---------------------+-------+------+------+-------------------+
	| backend_ndx | address             | state | type | uuid | connected_clients |
	+-------------+---------------------+-------+------+------+-------------------+
	|           1 | 192.168.35.102:3306 | up    | rw   | NULL |                 2 |
	|           2 | 192.168.35.103:3306 | up    | ro   | NULL |                 5 |
	|           3 | 192.168.35.104:3306 | up    | ro   | NULL |                 5 |
	+-------------+---------------------+-------+------+------+-------------------+
	3 rows in set (0.01 sec)

More infromation about mysql-proxy read/write balancing can be found here:
http://www.networkworld.com/article/2224080/opensource-subnet/simple-database-load-balancing-with-mysql-proxy.html