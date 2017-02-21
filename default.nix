with import <nixpkgs> {};

stdenv.mkDerivation rec {
  name = "sententiaregum";

  buildInputs =
  [
    vagrant
    (import ./ruby_env.nix)
  ];
}
