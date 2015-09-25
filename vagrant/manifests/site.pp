class application {
  $defaultPackages = [
    'vim',
    'git',
    'wget',
    'bash-completion',
    'ruby-dev',
    'software-properties-common',
    'python-software-properties',
    'libsqlite3-dev',
  ]

  anchor { 'sententiaregum::initial::before': } ->
    class { '::sententiaregum':
      packages => $defaultPackages,
    } ->
    class { '::sententiaregum::infrastructure':
      database_name => 'sententiaregum',
      build_redis   => true,
    } ->
    class { '::sententiaregum::server':
      host_name => 'sententiaregum.dev',
      doc_root  => '/var/www/sententiaregum/web',
    } ->
    class { '::sententiaregum::frontend':
      npm_packages  => ['grunt-cli', 'karma-cli', 'karma-jasmine', 'karma-browserify'],
      ruby_packages => ['compass', 'sass'],
    } ->
    class { '::sententiaregum::backend':
      use_composer   => true,
      ondrej_ppa_key => '4F4EA0AAE5267A6C',
      php56          => true,
    } ->
  anchor { 'sententiaregum::initial::after': }
}

class { 'application': }
