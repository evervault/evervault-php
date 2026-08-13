{
  description = "Evervault Bindings for PHP";

  inputs.nixpkgs.url = "github:NixOS/nixpkgs/nixos-unstable";

  outputs = { self, nixpkgs }:
    let
      systems = [ "x86_64-linux" "aarch64-linux" "x86_64-darwin" "aarch64-darwin" ];
      forAllSystems = f: nixpkgs.lib.genAttrs systems (system: f nixpkgs.legacyPackages.${system});
    in
    {
      devShells = forAllSystems (pkgs:
        let
          php = pkgs.php.withExtensions ({ enabled, all }:
            enabled ++ (with all; [ curl openssl mbstring gmp ]));
        in
        {
          default = pkgs.mkShell {
            packages = [
              php
              php.packages.composer
              pkgs.nodejs
            ];
          };
        });
    };
}
