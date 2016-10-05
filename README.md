ZF Doctrine QueryBuilder
===============================

[![Build status](https://api.travis-ci.org/zfcampus/zf-doctrine-querybuilder.svg)](http://travis-ci.org/zfcampus/zf-doctrine-querybuilder)
[![Total Downloads](https://poser.pugx.org/zfcampus/zf-doctrine-querybuilder/downloads)](https://packagist.org/packages/zfcampus/zf-doctrine-querybuilder)

This library provides query builder directives from array parameters.  This library was designed to apply filters from an HTTP request to give an API fluent filter and order-by dialects.

[![Watch and learn from the maintainer of this repository](https://raw.githubusercontent.com/API-Skeletons/zf-doctrine-querybuilder/master/media/api-skeletons-play.png)](https://apiskeletons.pivotshare.com/media/zf-doctrine-querybuilder/50592)


Philosophy
----------

Given developers identified A and B:  A == B with respect to ability and desire to filter and sort the entity data.

The Doctrine entity to share contains
```
id integer,
name string,
startAt datetime,
endAt datetime,
```

Developer A or B writes the API.  The resource is a single Doctrine Entity and the data is queried using a Doctrine QueryBuilder ```$objectManager->createQueryBuilder()```  This module gives the other developer the same filtering and sorting ability to the Doctrine query builder, but accessed through request parameters, as the API author.  For instance, ```startAt between('2015-01-09', '2015-01-11'); ``` and ```name like ('%arlie')``` are not common API filters for hand rolled APIs and perhaps without this module the API author would choose not to implement it for their reason(s).  With the help of this module the API developer can implement complex queryability to resources without complicated effort thereby maintaining A == B.


Installation
------------

Installation of this module uses composer. For composer documentation, please refer to [getcomposer.org](http://getcomposer.org/).

``` console
$ php composer.phar require zfcampus/zf-doctrine-querybuilder ^1.3
```

Once installed, add `ZF\Doctrine\QueryBuilder` to your list of modules inside
`config/application.config.php`.


Configuring the Module
----------------------

Copy `config/zf-doctrine-querybuilder.global.php.dist` to `config/autoload/zf-doctrine-querybuilder.global.php` and edit the list of invokables for orm and odm to those you want enabled by default.


Use With Apigility Doctrine
---------------------------

To enable all filters you may override the default query providers in zf-apigility-doctrine.  Add this to your `zf-doctrine-querybuilder.global.php` config file and filters and order-by will be applied if they are in `$_GET['filter']` or `$_GET['order-by']` request.  These $_GET keys are customizable through `zf-doctrine-querybuilder-options`

```php
'zf-apigility-doctrine-query-provider' => [
    'factories' => [
        'ZF\Doctrine\QueryBuilder\Query\Provider\DefaultOrm' => 'ZF\Doctrine\QueryBuilder\Query\Provider\Service\DefaultOrmFactory',
        'ZF\Doctrine\QueryBuilder\Query\Provider\DefaultOdm' => 'ZF\Doctrine\QueryBuilder\Query\Provider\Service\DefaultOdmFactory',
    ],
    'aliases' => [
        'default_orm' => 'ZF\Doctrine\QueryBuilder\Query\Provider\DefaultOrm',
        'default_odm' => 'ZF\Doctrine\QueryBuilder\Query\Provider\DefaultOdm',
    ]
],
```

Or: to use with apigility doctrine events see [docs/apigility.example.php](https://github.com/zfcampus/zf-doctrine-querybuilder/blob/master/docs/apigility.example.php)


Use
---

Configuration example
```php
    'zf-doctrine-querybuilder-orderby-orm' => array(
        'invokables' => array(
            'field' => 'ZF\Doctrine\QueryBuilder\OrderBy\ORM\Field',
        ),
    ),
    'zf-doctrine-querybuilder-filter-orm' => array(
        'invokables' => array(
            'eq' => 'ZF\Doctrine\QueryBuilder\Filter\ORM\Equals',
        ),
    ),
```

Request example
```php
$_GET = array(
    'filter' => array(
        array(
            'type' => 'eq',
            'field' => 'name',
            'value' => 'Tom',
        ),
    ),
    'order-by' => array(
        array(
            'type' => 'field',
            'field' => 'startAt',
            'direction' => 'desc',
        ),
    ),
);
```

Resource example
```php
$serviceLocator = $this->getApplication()->getServiceLocator();
$objectManager = $serviceLocator->get('doctrine.entitymanager.orm_default');

$filterManager = $serviceLocator->get('ZfDoctrineQueryBuilderFilterManagerOrm');
$orderByManager = $serviceLocator->get('ZfDoctrineQueryBuilderOrderByManagerOrm');

$queryBuilder = $objectManager->createQueryBuilder();
$queryBuilder->select('row')
    ->from($entity, 'row')
;

$metadata = $objectManager->getMetadataFactory()->getMetadataFor(ENTITY_NAME); // $e->getEntity() using doctrine resource event
$filterManager->filter($queryBuilder, $metadata, $_GET['filter']);
$orderByManager->orderBy($queryBuilder, $metadata, $_GET['order-by']);

$result = $queryBuilder->getQuery()->getResult();
```


Filters
-------

Filters are not simple key/value pairs.  Filters are a key-less array of filter definitions.  Each filter definition is an array and the array values vary for each filter type.

Each filter definition requires at a minimum a 'type'.  A type references the configuration key such as 'eq', 'neq', 'between'.

Each filter definition requires at a minimum a 'field'.  This is the name of a field on the target entity.

Each filter definition may specify 'where' with values of either 'and', 'or'.

Embedded logic such as and(x or y) is supported through AndX and OrX filter types.

### Building HTTP GET query:

Javascript Example:

```js
$(function() {
    $.ajax({
        url: "http://localhost:8081/api/db/entity/user_data",
        type: "GET",
        data: {
            'filter': [
            {
                'field': 'cycle',
                'where': 'or',
                'type': 'between',
                'from': '1',
                'to': '100'
            },
            {
                'field': 'cycle',
                'where': 'or',
                'type': 'gte',
                'value': '1000'
            }
        ]
        },
        dataType: "json"
    });
});
```


Querying Relations
------------------

### Single valued
It is possible to query collections by relations - just supply the relation name as `fieldName` and
identifier as `value`.

Assuming we have defined 2 entities, `User` and `UserGroup`...

```php
/**
 * @Entity
 */
class User {
    /**
     * @ManyToOne(targetEntity="UserGroup")
     * @var UserGroup
     */
    protected $group;
}
```

```php
/**
 * @Entity
 */
class UserGroup {}
```

find all users that belong to UserGroup id #1 by querying the user resource with the following filter:

```php
    array('type' => 'eq', 'field' => 'group', 'value' => '1')
```

### Collection valued
To match entities A that have entity B in a collection use `ismemberof`.
Assuming `User` has a ManyToMany (or OneToMany) association with `UserGroup`...

```php
/**
 * @Entity
 */
class User {
    /**
     * @ManyToMany(targetEntity="UserGroup")
     * @var UserGroup[]|ArrayCollection
     */
    protected $groups;
}
```
find all users that belong to UserGroup id #1 by querying the user resource with the following filter:

```php
    array('type' => 'ismemberof', 'field' => 'groups', 'value' => '1')
```

Format of Date Fields
---------------------

When a date field is involved in a filter you may specify the format of the date using PHP date
formatting options.  The default date format is `Y-m-d H:i:s` If you have a date field which is
just `Y-m-d`, then add the format to the filter.  For complete date format options see [DateTime::createFromFormat](http://php.net/manual/en/datetime.createfromformat.php)

```php
    'format' => 'Y-m-d',
    'value' => '2014-02-04',
```


Joining Entities and Aliasing Queries
-------------------------------------

There is an included ORM Query Type for Inner Join so for every filter type there is an optional `alias`.
The default alias is 'row' and refers to the entity at the heart of the REST resource.  There is not a filter to add other entities to the return data.  That is, only the original target resource, by default 'row', will be returned regardless of what filters or order by are applied through this module.

Inner Join is not included by default in the ```zf-doctrine-querybuilder.global.php.dist```

This example joins the report field through the inner join already defined on the row entity then filters
for `r.id = 2`:

```php
    array('type' => 'innerjoin', 'field' => 'report', 'alias' => 'r'),
    array('type' => 'eq', 'alias' => 'r', 'field' => 'id', 'value' => '2')
```

You can inner join tables from an inner join using `parentAlias`:

```php
    array('type' => 'innerjoin', 'parentAlias' => 'r', 'field' => 'owner', 'alias' => 'o'),
```

To enable inner join add this to your configuration.

```php
    'zf-doctrine-querybuilder-filter-orm' => array(
        'invokables' => array(
            'innerjoin' => 'ZF\Doctrine\QueryBuilder\Filter\ORM\InnerJoin',
        ),
    ),
```


Included Filter Types
---------------------

### ORM and ODM

Equals:

```php
array('type' => 'eq', 'field' => 'fieldName', 'value' => 'matchValue')
```

Not Equals:

```php
array('type' => 'neq', 'field' => 'fieldName', 'value' => 'matchValue')
```

Less Than:

```php
array('type' => 'lt', 'field' => 'fieldName', 'value' => 'matchValue')
```

Less Than or Equals:

```php
array('type' => 'lte', 'field' => 'fieldName', 'value' => 'matchValue')
```

Greater Than:

```php
array('type' => 'gt', 'field' => 'fieldName', 'value' => 'matchValue')
```

Greater Than or Equals:

```php
array('type' => 'gte', 'field' => 'fieldName', 'value' => 'matchValue')
```

Is Null:

```php
array('type' => 'isnull', 'field' => 'fieldName')
```

Is Not Null:

```php
array('type' => 'isnotnull', 'field' => 'fieldName')
```

Note: Dates in the In and NotIn filters are not handled as dates.  It is recommended you use multiple Equals statements instead of these filters for date datatypes.

In:

```php
array('type' => 'in', 'field' => 'fieldName', 'values' => array(1, 2, 3))
```

NotIn:

```php
array('type' => 'notin', 'field' => 'fieldName', 'values' => array(1, 2, 3))
```

Between:

```php
array('type' => 'between', 'field' => 'fieldName', 'from' => 'startValue', 'to' => 'endValue')
```

Like (`%` is used as a wildcard):

```php
array('type' => 'like', 'field' => 'fieldName', 'value' => 'like%search')
```

### ORM Only

Is Member Of:

```php
array('type' => 'ismemberof', 'field' => 'fieldName', 'value' => 1)
```

AndX:

In AndX queries, the `conditions` is an array of filter types for any of those described
here.  The join will always be `and` so the `where` parameter inside of conditions is
ignored.  The `where` parameter on the AndX filter type is not ignored.

```php
array(
    'type' => 'andx',
    'conditions' => array(
        array('field' =>'name', 'type'=>'eq', 'value' => 'ArtistOne'),
        array('field' =>'name', 'type'=>'eq', 'value' => 'ArtistTwo'),
    ),
    'where' => 'and'
)
```

OrX:

In OrX queries, the `conditions` is an array of filter types for any of those described
here.  The join will always be `or` so the `where` parameter inside of conditions is
ignored.  The `where` parameter on the OrX filter type is not ignored.

```php
array(
    'type' => 'orx',
    'conditions' => array(
        array('field' =>'name', 'type'=>'eq', 'value' => 'ArtistOne'),
        array('field' =>'name', 'type'=>'eq', 'value' => 'ArtistTwo'),
    ),
    'where' => 'and'
)
```

### ODM Only

Regex:

```php
array('type' => 'regex', 'field' => 'fieldName', 'value' => '/.*search.*/i')
```


Included Order By Type
---------------------

Field:

```php
array('type' => 'field', 'field' => 'fieldName', 'direction' => 'desc');
```

