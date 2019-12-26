github_vagrant = "https://raw.githubusercontent.com/rthompson961/vagrant-lamp/master"
github_project = "https://github.com/rthompson961/fashion"
php_version = "7.4"
mysql_pass = "root"

Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/bionic64"
  config.vm.network "forwarded_port", guest: 80, host: 8000, host_ip: "127.0.0.1"
  config.vm.network :private_network, type: "dhcp"
  config.vm.synced_folder ".", "/vagrant", type: "nfs", linux__nfs_options: ['rw','no_subtree_check','all_squash','async']
  config.vm.provision :shell, path: "#{github_vagrant}/base.sh"
  config.vm.provision :shell, path: "#{github_vagrant}/nginx.sh", args: [php_version]
  config.vm.provision :shell, path: "#{github_vagrant}/php.sh", args: [php_version]
  config.vm.provision :shell, path: "#{github_vagrant}/mysql.sh", args: [mysql_pass]
  config.vm.provision :shell, path: "#{github_vagrant}/composer.sh"
  config.vm.provision :shell, path: "#{github_vagrant}/symfony.sh", args: [github_project]
end