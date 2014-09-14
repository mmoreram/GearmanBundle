# Upgrade from 2.* to 3.*

This guide will help you upgrading from GearmanBundle 2.* to GearmanBundle 3.*

## FrameworkBundle

Version 3 of this bundle will require FramworkBundle ~2.4 because needs support
for using Commands as a Service, introduced in
[this Pull Request](https://github.com/symfony/symfony/pull/9199).

## Dependencies

All composer dependencies have changed to semantic model. It means that all
Symfony and Doctrine packages were requiring `>=2.1` while now all packages are
expected to use highest stable version until next major version: `~2.1`.

PHPUnit and PHPFormatter are no longer dependencies, because they can be
downloaded and used through their phar files. This reduces with some packages
the development environment.