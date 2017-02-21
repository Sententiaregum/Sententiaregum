with import <nixpkgs> {};

stdenv.mkDerivation rec {
  name = "sententiaregum-ruby-env";

  env = bundlerEnv {
    name = "sententiaregum-env";

    gemfile  = ./Gemfile;
    lockfile = ./Gemfile.lock;
    gemset   = ./gemset.nix;

    inherit ruby;
  };

  buildInputs = [ makeWrapper ];

  phases = [ "installPhase" ];
  installPhase = ''
    mkdir -p $out/bin
    makeWrapper ${env}/bin/puppet $out/bin/puppet
    makeWrapper ${env}/bin/cap $out/bin/cap
  '';
}
