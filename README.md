`config/bundles.php`

```
<?php

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
	....
    Oro\Bundle\DataGridBundle\OroDataGridBundle::class => ['dev' => true, 'test' => true],
    Oro\Bundle\FilterBundle\OroFilterBundle::class => ['dev' => true, 'test' => true],
];
```

`src/Kernel.php`

```
  /**
     * {@inheritdoc}
     */
    protected function initializeBundles()
    {
        // clear state of CumulativeResourceManager
        CumulativeResourceManager::getInstance()->clear();

        parent::initializeBundles();

        // initialize CumulativeResourceManager
        $bundles = [];
        foreach ($this->bundles as $name => $bundle) {
            $bundles[$name] = get_class($bundle);
        }
        CumulativeResourceManager::getInstance()
            ->setBundles($bundles)
            ->setAppRootDir($this->getProjectDir());
    }
```

`php bin/console gorgo:profile:datagrid test --bind=./test.json`

```
+-----------------------+
| noinc.role.name.label |
+-----------------------+
| ROLE_STUDENT          |
| ROLE_ADMIN            |
+-----------------------+

SQL Query:
SELECT r0_.name AS name_0 FROM roles r0_ WHERE LOWER(r0_.name) NOT LIKE LOWER(?) ORDER BY name_0 DESC LIMIT 10

SQL Parameters:
+---------------+--------------+------+
| name          | value        | type |
+---------------+--------------+------+
| name391932701 | %ROLE_STAFF% | 2    |
+---------------+--------------+------+

```


`php bin/console gorgo:debug:datagrid test --bind=./test.json`

```
+---------------+------+--------+
| Datagrid Name | Type | Parent |
+---------------+------+--------+
| test          | orm  |        |
+---------------+------+--------+

Datagrid configuration:

source:
    type: orm
    query:
        select:
            - 'l.name as name'
        from:
            -
                table: NoInc\User\Entity\Role
                alias: l
    hints:
        - HINT_PRECISE_ORDER_BY
        - HINT_DISABLE_ORDER_BY_MODIFICATION_NULLS
columns:
    name:
        label: noinc.role.name.label
        data_name: name
        type: field
        frontend_type: string
        translatable: true
        editable: false
        shortenableLabel: true
sorters:
    columns:
        name:
            data_name: name
filters:
    columns:
        name:
            type: string
            label: noinc.role.name.label
            data_name: name
            enabled: true
            visible: true
            translatable: true
            force_like: false
            case_insensitive: true
            min_length: 0
            max_length: 9223372036854775807
    default: {  }
name: test
properties: {  }
options:
    toolbarOptions:
        hide: false
        addResetAction: true
        addRefreshAction: true
        addDatagridSettingsManager: true
        turnOffToolbarRecordsNumber: 0
        pageSize:
            hide: false
            default_per_page: 10
            items:
                - 10
                - 25
                - 50
                - 100
        pagination:
            hide: false
            onePage: false
        placement:
            top: true
            bottom: false


SQL:
SELECT r0_.name AS name_0 FROM roles r0_ WHERE LOWER(r0_.name) NOT LIKE LOWER(?) ORDER BY name_0 DESC LIMIT 10

```