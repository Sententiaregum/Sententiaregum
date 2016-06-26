class sententiaregum::backend::php(
  $fpm        = false,
  $extensions = {},
  $settings   = {},
) {
  class { '::php':
    fpm        => $fpm,
    extensions => $extensions,
    settings   => $settings,
    require    => Class['::sententiaregum::installs'],
  }
}
