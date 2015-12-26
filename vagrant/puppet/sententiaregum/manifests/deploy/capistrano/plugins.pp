class sententiaregum::deploy::capistrano::plugins($plugins) {
  validate_hash($plugins)
  create_resources('plugin', $plugins)
}
