class sententiaregum::ssh($entry_point = '/var/www/sententiaregum') {
  validate_string($entry_point)

  file_line { 'ssh_entry_point':
    line => "cd ${entry_point}",
    path => '/home/vagrant/.bashrc',
  }
}
