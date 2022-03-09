GearmanBundle for Symfony2, 3 and 4
=====
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/cf3d97c6-e026-4489-8493-a3f4a1e75a68/mini.png)](https://insight.sensiolabs.com/projects/cf3d97c6-e026-4489-8493-a3f4a1e75a68)
[![Build Status](https://travis-ci.org/mmoreram/GearmanBundle.png?branch=master)](https://travis-ci.org/mmoreram/GearmanBundle)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/mmoreram/GearmanBundle/badges/quality-score.png?s=29f1ed342d3df54678614b58b0e243136aa24726)](https://scrutinizer-ci.com/g/mmoreram/GearmanBundle/)
[![Latest Stable Version](https://poser.pugx.org/mmoreram/gearman-bundle/v/stable.png)](https://packagist.org/packages/mmoreram/gearman-bundle)
[![Latest Unstable Version](https://poser.pugx.org/mmoreram/gearman-bundle/v/unstable.png)](https://packagist.org/packages/mmoreram/gearman-bundle)
[![License](https://poser.pugx.org/mmoreram/gearman-bundle/license.png)](https://packagist.org/packages/mmoreram/gearman-bundle)
[![Total Downloads](https://poser.pugx.org/mmoreram/gearman-bundle/downloads.png)](https://packagist.org/packages/mmoreram/gearman-bundle)

GearmanBundle is a bundle for Symfony4/5/6 intended to provide an easy way to 
support developers who need to use job queues. For example: mail queues, Solr 
generation queues or Database upload queues.  For Symfony Flex you can use [the recipe in the contributed
repository](https://github.com/symfony/recipes-contrib) to get started quickly.

Documentation
-------------

Check the documentation in [ReadTheDocs](http://gearmanbundle.readthedocs.org/).  
Some recipes will be written soon. If you have some ideas and you want to share
them with us, don't hesitate to open a RFC issue or a Pull Request.


Tags
----
* Symfony 4.0 is supported by tags 4.1+ (they still work with Symfony 3.X)
* All 4.X tags support Symfony 3.X
* Use tags lower than 4.X for Symfony 2.X versions 
* Use last unstable version ( alias of `dev-master` ) to stay always in last commit
* Use last stable version tag to stay in a stable release.
* [![Latest Unstable Version](https://poser.pugx.org/mmoreram/gearman-bundle/v/unstable.png)](https://packagist.org/packages/mmoreram/gearman-bundle)  [![Latest Stable Version](https://poser.pugx.org/mmoreram/gearman-bundle/v/stable.png)](https://packagist.org/packages/mmoreram/gearman-bundle)



Contributing
------------

All code is Symfony2 Code formatted, so every pull request must validate phpcs
standards. You should read 
[Symfony2 coding standards](http://symfony.com/doc/current/contributing/code/standards.html)
and install [this](https://github.com/opensky/Symfony2-coding-standard) 
CodeSniffer to check all code is validated.

There is also a policy for contributing to this project. All pull request must
be all explained step by step, to make us more understandable and easier to
merge pull request. All new features must be tested with PHPUnit.

If you'd like to contribute, please read the [Contributing Code][1] part of the
documentation. If you're submitting a pull request, please follow the guidelines
in the [Submitting a Patch][2] section and use the [Pull Request Template][3].

[1]: http://symfony.com/doc/current/contributing/code/index.html
[2]: http://symfony.com/doc/current/contributing/code/patches.html#check-list
[3]: http://symfony.com/doc/current/contributing/code/patches.html#make-a-pull-request
