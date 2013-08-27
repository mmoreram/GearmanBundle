#GearmanBundle for Symfony2

###Installing [GearmanBundle](https://github.com/mmoreram/gearman-bundle)
You have to add require line into you composer.json file

    "require": {
        "php": ">=5.3.3",
        "symfony/symfony": "2.3.*",
        ...
        "mmoreram/gearman-bundle": "dev-master"
    },

Then you have to use composer to update your project dependencies

    php composer.phar update

And register the bundle in your appkernel.php file

    return array(
        // ...
        new Mmoreram\GearmanBundle\GearmanBundle(),
        // ...
    );