# -*- mode: ruby; -*-

# Force Virtualbox for those people who have installed vagrant-lxc (e.g.)
ENV['VAGRANT_DEFAULT_PROVIDER'] = 'virtualbox'

Vagrant.configure("2") do |config|
  config.vm.box_url = "https://github.com/2creatives/vagrant-centos/releases/download/v6.5.3/centos65-x86_64-20140116.box"

  config.vm.define "dbmaster" do |dbmaster|
    config.vm.network "private_network", ip: "192.168.35.102"
    dbmaster.vm.box = "2creatives/centos65-x86_64-20140116"
    config.vm.hostname = "dbmaster"

    config.vm.provision "shell", inline: <<-SHELL
      COMMONSETUPDIR="/vagrant/.setup/common/"
      VMSETUPDIR="/vagrant/.setup/dbmaster/"

      cp -r $COMMONSETUPDIR/* /
      yum install -y mysql-server
      cp -r $VMSETUPDIR/* /

      service mysqld restart

      # user 'repl':
      mysql -uroot -e"CREATE USER 'repl'@'%' IDENTIFIED BY 'slavepass'; GRANT REPLICATION SLAVE, RELOAD, SUPER, REPLICATION CLIENT ON *.* TO 'repl'@'%'; FLUSH PRIVILEGES;"

      # user 'appuser':
      mysql -uroot -e"CREATE USER 'appuser'@'%' IDENTIFIED BY 'apppassword'; GRANT DROP, CREATE, SELECT, INSERT, UPDATE, DELETE ON appdb.* TO 'appuser'@'%'; FLUSH PRIVILEGES;"

    SHELL
  end

  config.vm.define "dbslave1" do |dbslave1|
    config.vm.network "private_network", ip: "192.168.35.103"
    dbslave1.vm.box = "2creatives/centos65-x86_64-20140116"
    config.vm.hostname = "dbslave1"

    config.vm.provision "shell", inline: <<-SHELL
      COMMONSETUPDIR="/vagrant/.setup/common/"
      VMSETUPDIR="/vagrant/.setup/dbslave1/"

      cp -r $COMMONSETUPDIR/* /
      yum install -y mysql-server
      cp -r $VMSETUPDIR/* /

      service mysqld restart

      # setup replication:
      mysql -hdbmaster -urepl -pslavepass -e"FLUSH TABLES WITH READ LOCK;"
      mysql -hdbmaster -urepl -pslavepass -e"SHOW MASTER STATUS;" | grep mysql > /tmp/master_status
      STATUS=$(cat /tmp/master_status)
      MASTER_LOG_FILE=${STATUS:0:16};
      MASTER_LOG_POS=${STATUS:17:3};

      echo MASTER_LOG_FILE=$MASTER_LOG_FILE MASTER_LOG_POS=$MASTER_LOG_POS

      mysql -uroot -e"CHANGE MASTER TO MASTER_HOST='dbmaster', MASTER_USER='repl',MASTER_PASSWORD='slavepass', MASTER_LOG_FILE='$MASTER_LOG_FILE', MASTER_LOG_POS=$MASTER_LOG_POS;"
      mysql -uroot -e"START SLAVE;"

      # user 'appuser' read-only:
      mysql -uroot -e"CREATE USER 'appuser'@'%' IDENTIFIED BY 'apppassword'; GRANT SELECT ON appdb.* TO 'appuser'@'%'; FLUSH PRIVILEGES;"

    SHELL

  end

  config.vm.define "dbslave2" do |dbslave2|
    config.vm.network "private_network", ip: "192.168.35.104"
    dbslave2.vm.box = "2creatives/centos65-x86_64-20140116"
    config.vm.hostname = "dbslave2"

    config.vm.provision "shell", inline: <<-SHELL
      COMMONSETUPDIR="/vagrant/.setup/common/"
      VMSETUPDIR="/vagrant/.setup/dbslave2/"

      cp -r $COMMONSETUPDIR/* /
      yum install -y mysql-server
      cp -r $VMSETUPDIR/* /

      service mysqld restart

      # setup replication:
      mysql -hdbmaster -urepl -pslavepass -e"FLUSH TABLES WITH READ LOCK;"
      mysql -hdbmaster -urepl -pslavepass -e"SHOW MASTER STATUS;" | grep mysql > /tmp/master_status
      STATUS=$(cat /tmp/master_status)
      MASTER_LOG_FILE=${STATUS:0:16};
      MASTER_LOG_POS=${STATUS:17:3};

      echo MASTER_LOG_FILE=$MASTER_LOG_FILE MASTER_LOG_POS=$MASTER_LOG_POS

      mysql -uroot -e"CHANGE MASTER TO MASTER_HOST='dbmaster', MASTER_USER='repl',MASTER_PASSWORD='slavepass', MASTER_LOG_FILE='$MASTER_LOG_FILE', MASTER_LOG_POS=$MASTER_LOG_POS;"
      mysql -uroot -e"START SLAVE;"

      # user 'appuser' read-only:
      mysql -uroot -e"CREATE USER 'appuser'@'%' IDENTIFIED BY 'apppassword'; GRANT SELECT ON appdb.* TO 'appuser'@'%'; FLUSH PRIVILEGES;"

    SHELL

  end

  config.vm.define "web" do |web|
    # Read this mysql-proxy installation article:
    # http://www.networkworld.com/article/2224080/opensource-subnet/simple-database-load-balancing-with-mysql-proxy.html

    web.vm.box = "2creatives/centos65-x86_64-20140116"
    config.vm.network "private_network", ip: "192.168.35.101"
    config.vm.hostname = "web"
    
    config.vm.provision "shell", inline: <<-SHELL
      COMMONSETUPDIR="/vagrant/.setup/common/"
      VMSETUPDIR="/vagrant/.setup/web/"

      cp -r $COMMONSETUPDIR/* /
      yum install -y --enablerepo=remi,remi-php54 php-devel php-mysql mysql mysql-proxy
      cp -r $VMSETUPDIR/* /

      service mysql-proxy start

      /bin/sh /vagrant/test/create_db.sh
    SHELL
  end

end