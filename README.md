# RemgGeneratorBundle

[![Build Status](https://travis-ci.org/Remg/GeneratorBundle.svg?branch=master)](https://travis-ci.org/Remg/GeneratorBundle)
[![Test Coverage](https://codeclimate.com/github/Remg/GeneratorBundle/badges/coverage.svg)](https://codeclimate.com/github/Remg/GeneratorBundle/coverage)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/be1eac25-1aba-4fc3-8ddc-c59124425955/mini.png)](https://insight.sensiolabs.com/projects/be1eac25-1aba-4fc3-8ddc-c59124425955)

## Introduction

This bundle provides commands to intuitively generate code inside 
[Symfony](http://symfony.com/)-based projects.

## Overview

![Example](Resources/doc/images/remg_generator_example.png?raw=true "RemgGeneratorBundle")

## Features

### 1. Entity generation

#### Fields

* Handles all [Doctrine2 column types](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html).

#### Associations

* Handles all [Doctrine2 association types](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html)
(OneToOne, OneToMany, ManyToOne, ManyToMany).
* Handles unidirectional and bidirectional associations.
* Auto-detect association mappings when generating an Entity already targetted
by other entities.

#### Configuration formats

* Handles all [Doctrine2 metadata drivers](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/metadata-drivers.html)
(annotations, yaml, xml, php).

### 2. Entity re-generation

The bundle provides a command that will start an entity generation from the
mappping informations of an existing entity. It is then possible to edit or add
fields and associations before the entity is regenerated from scratch.

This same command can be used to regenerate an entity in a different mapping
configuration format.

## Installation

### Step 1: Download the bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require --dev remg/generator-bundle dev-master
```

This command requires you to have [Composer](https://getcomposer.org/) installed
globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer
documentation.

### Step 2: Enable the bundle

Then, enable the bundle by adding
*new Remg\GeneratorBundle\RemgGeneratorBundle()* to the list of registered
bundles for the **dev** environment in the `app/AppKernel.php` file of your
project:

```php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        // ...
        if (in_array($this->getEnvironment(), ['dev', 'test'])) {
            $bundles[] = new Remg\GeneratorBundle\RemgGeneratorBundle();
            // ...
        }
    }
    // ...
}
```

### Step 3: Configure the bundle

The bundle comes with a default configuration, which is listed below. You can
define these options in your configuration if you need to change them:

```yaml
remg_generator:
    entity:
        # available configuration formats are: 'annotation', 'yaml', 'xml' and 'php'.
        configuration_format: annotation
```

Since this bundle is only useful in command line interface, you can override the
bundle configuration in your development configuration in
`app/config/config_dev.yml`.

## Usage

You can now generate new Doctrine2 entities using the command below:

```bash
$ php bin/console remg:generate:entity
```

You can also regenerate Doctrine2 entities from scratch using the command below:

```bash
$ php bin/console remg:regenerate:entity
```

**NOTE:** Since this bundle can only generate code, and not manipulate existing
code, this command will regenerate the entity from scratch without implementing
any custom code that could live in the existing entity.

**No code can be lost. Each file generation checks if the target file already
exists, and creates a timestamped backup of the file before generation.**

## Under development

* Repository generation.
* Entity edition.
* CRUD generation (handling embed form collections).

## License

This bundle is under the MIT license. See the complete license [in the bundle](LICENSE).
