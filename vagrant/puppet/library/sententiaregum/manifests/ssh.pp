class sententiaregum::ssh(
  $entry_point = '/var/www/sententiaregum',
  $env         = {}
) {
  validate_string($entry_point)
  validate_hash($env)

  file_line { 'ssh_entry_point':
    line => "cd ${entry_point}",
    path => '/home/vagrant/.bashrc',
  }

  if !empty($env) {
    file { '/etc/profile.d/env_exporter.sh':
      mode    => 755,
      content => template('sententiaregum/env_exporter.sh.erb'),
    }
  }
}
