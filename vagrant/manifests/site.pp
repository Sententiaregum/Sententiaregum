Exec { path => '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/var/www/sententiaregum/bin' }
Class['::apt::update'] -> Package <|
  title != 'python-software-properties'
  and title != 'software-properties-common'
|>

hiera_include('classes')
