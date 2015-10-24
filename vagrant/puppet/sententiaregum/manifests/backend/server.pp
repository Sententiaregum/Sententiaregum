class sententiaregum::backend::server($host_name, $doc_root) {
  validate_string($host_name)
  validate_string($doc_root)

  class { 'apache': }

  apache::module { 'rewrite': }
  apache::vhost { $host_name:
    server_name              => $host_name,
    port                     => 80,
    docroot                  => $doc_root,
    directory_allow_override => 'All',
  }
}
