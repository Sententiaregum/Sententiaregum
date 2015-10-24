class application {
  contain sententiaregum::installs
  contain sententiaregum::backend::php
  contain sententiaregum::backend::server
  contain sententiaregum::infrastructure::mailcatcher
  contain sententiaregum::infrastructure::mysql
  contain sententiaregum::infrastructure::redis
  contain sententiaregum::frontend::npm
  contain sententiaregum::frontend::ruby
}

class { 'application': }
