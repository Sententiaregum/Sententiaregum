class sententiaregum::backend::php($extensions = {}, $ppa = 'php5-5.6') {
  validate_hash($extensions)
  validate_string($ppa)

  ::apt::key { '4F4EA0AAE5267A6C': } ->
  ::apt::ppa { "ppa:ondrej/${ppa}":
    require => Apt::Key['4F4EA0AAE5267A6C'],
    before  => [Class['::php']]
  }

  create_resources('::php::module', $extensions)
}
