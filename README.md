# CakeSoftDelete plugin for CakePHP

## Purpose

This Cakephp plugin enables you to make your models soft deletable.

## Requirements

This plugins has been developped for cakephp 3.x.

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

Update your composer file to include this plugin:

```
composer require pgbi/cakephp3-soft-delete "dev-master"
```

## Configuration

1. Load the plugin:

```
// In /config/bootstrap.php
Plugin::load('SoftDelete');
```

2. Make a model soft deleteable by using SoftDelete trait:

```
// in src/Model/Table/UsersTable.php
...
use SoftDelete\Model\Table\SoftDeleteTrait;

class UsersTable extends Table
{
    use SoftDeleteTrait;
    ...
```

3. Your soft deletable model database table should have a field called `deleted` of type DateTime with NULL as default value.

## Use

### Soft deleting records

`delete` and `deleteAll` functions will now soft delete records by populating `deleted` field with the date of the deletion.

### Finding records

`find`, `get` or dynamic finders (such as `findById`) will only return non soft deleted records.
To also return soft deleted records, `$options` must contain `'withDeleted'`. Example:

```
// in src/Model/Table/UsersTable.php
$nonSoftDeletedRecords = $this->find('all');
$allRecords            = $this->find('all', ['withDeleted']);
```

### Hard deleting records

Only soft deleted records can be hard deleted. To delete all records that were soft deleted before a given date:

```
// in src/Model/Table/UsersTable.php
$date = new \DateTime('some date');
$affectedRowsCount = $this->hardDeleteAll($date);
```

## Soft deleting & associations

Associations are correctly handled by SoftDelete plugin.

1. Soft deletion will be cascaded to related models as usual. If related models also use SoftDelete Trait, they will be soft deleted.
2. Soft deletes records will be excluded from counter cache.
