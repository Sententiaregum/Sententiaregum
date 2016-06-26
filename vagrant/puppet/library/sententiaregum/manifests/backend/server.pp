class sententiaregum::backend::server(
  $host_name,
  $doc_root,
  $front_controller,
  $port     = 80
) {
  validate_string($host_name)
  validate_string($doc_root)
  validate_string($front_controller)
  validate_integer($port)

  include '::apache'
  include '::apache::mod::php'

  ::apache::vhost { "custom-${hostname}-${port}":
    docroot         => $doc_root,
    manage_docroot  => false,
    default_vhost   => true,
    port            => $port,
    override        => 'none',
    directoryindex  => $front_controller,
    notify          => Service['apache2'],
    rewrites        => [
      {
        rewrite_cond => ['/var/www/sententiaregum/web/%{REQUEST_URI} !-f'],
        rewrite_rule => ["^(.*)$ /var/www/sententiaregum/web/${front_controller} [QSA,L]"]
      }
    ],
  }

  Package['libapache2-mod-php7.0'] ~> Service['apache2']

  exec { 'disable default site':
    command => 'a2dissite 15-default.conf',
    notify  => Service['apache2'],
    require => ::Apache::Vhost["custom-${hostname}-${port}"],
  }
}
