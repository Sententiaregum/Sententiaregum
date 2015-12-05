class sententiaregum::ssh($entry_point) {
  if $entry_point == undef {
    $path = '/var/www/sententiaregum'
  }
  else {
    validate_string($entry_point)
    $path = $entry_point
  }

  file_line { 'ssh_entry_point':
    line => "cd ${path}",
    path => '/home/vagrant/.bashrc',
  }
}
