class sententiaregum::frontend::npm($packages = []) {
  validate_array($packages)

  ensure_packages($packages, {
    provider => npm,
    require  => Class['::nodejs']
  })
}
