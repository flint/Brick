Brick
=====

Brick is the new version of Flint. It brings power and awesomeness to Silex by bridging Silex with multiple components
from Symfony and Flint.

Caution
-------

This is build using the next generation of Silex and Pimple. It does not require the full installation of Silex but
only its Api package.

Because the next Pimple and Silex versions are still in development, usage of Brick is currently not recommended.

Documentation
-------------

Documentation can be found in the `doc` directory or rendered [at read the docs.](http://brick.rtfd.org).

Tests
-----

Brick is tested with PhpSpec and its service providers have integration tests powered by PHPUnit.
Both can be run with

``` bash
$ ./vendor/bin/phpspec
$ phpunit
```

### Updating providers subsplit

First install https://github.com/dflydev/git-subsplit. Then update the subsplit and publish it to the right
repository.

``` bash
$ git subsplit publish src/Provider:git@github.com:flint/providers.git --heads=master --update
```
