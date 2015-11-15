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
      mysql -uroot -e"CREATE USER 'repl'@'%' IDENTIFIED BY 'slavepass';GRANT REPLICATION SLAVE, RELOAD, SUPER, REPLICATION CLIENT ON *.* TO 'repl'@'%';FLUSH PRIVILEGES;"

      # user 'test':
      mysql -uroot -e"CREATE USER 'test'@'%' IDENTIFIED BY 'test';GRANT ALL ON test.* TO 'test'@'%';FLUSH PRIVILEGES;"

    SHELL
  end

  config.vm.define "dbslave" do |dbslave|
    config.vm.network "private_network", ip: "192.168.35.103"
    dbslave.vm.box = "2creatives/centos65-x86_64-20140116"
    config.vm.hostname = "dbslave"

    config.vm.provision "shell", inline: <<-SHELL
      COMMONSETUPDIR="/vagrant/.setup/common/"
      VMSETUPDIR="/vagrant/.setup/dbslave/"

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

      # user 'test' read-only:
      mysql -uroot -e"CREATE USER 'test'@'%' IDENTIFIED BY 'test';GRANT SELECT ON test.* TO 'test'@'%';FLUSH PRIVILEGES;"

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

    SHELL
  end

end