# DataMapper

Class for mapping data by filters

## Instalation
```bash
composer require alexmcarrow/datamapper
```

## Using
```php
use AlexMcArrow\DataMapper\DataMapper;

new DataMapper();
DataMapper::setFieldsFilter([
            'key' => 'KeyID',
            'name' => 'User Name'
        ]);

$rawdata = [
    [
        'key' => 'a1',
        'name' => 'Alex',
        'email' => 'alex@domain.tld'
    ],
    [
        'key' => 'b2',
        'name' => 'Axel'
    ]
];

$cleardata = DataMapper::parseMapData($rawdata, DataMapper::$FILTER_PASS_CUT, DataMapper::$FILTER_TYPE_HARD, DataMapper::$MAP_KEY, DataMapper::$MAP_VALUE);

print_r($cleardata);
```

```php
[
    [
        'keyid' => 'a1',
        'user name' => 'Alex'
    ],
    [
        'keyid' => 'b2',
        'user name' => 'Axel'
    ]
]
```

## License
MIT
