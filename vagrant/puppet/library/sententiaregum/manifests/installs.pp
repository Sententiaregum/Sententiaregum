class sententiaregum::installs($packages) {
  validate_array($packages)

  include ::apt
  ensure_packages([$packages])
}
