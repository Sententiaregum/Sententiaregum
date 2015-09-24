class sententiaregum::backend::server(
  $host_name,
  $doc_root,
  $front_controller = 'app_dev.php',
  $server_aliases   = '',
  $port             = 80,
  $modules          = {}
) {
  validate_string($host_name)
  validate_string($doc_root)
  validate_string($front_controller)
  validate_string($server_aliases)
  validate_integer($port)
  validate_hash($modules)

  class { '::apache': }

  create_resources('::apache::module', $modules)

  apache::vhost { $host_name:
    server_name   => $host_name,
    port          => $port,
    docroot       => $doc_root,
    serveraliases => $server_aliases,
    template      => 'sententiaregum/vhost.erb',
  }
}
