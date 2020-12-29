<h1 align="center"> Aliyun </h1>

## Requirement   
1. PHP >= 7.2
2. [Composer](https://getcomposer.org/)


## Installing

```shell
$ composer require sevming/aliyun -vvv
```

## Usage
```php
<?php

use Sevming\Aliyun\Aliyun;

$aliyun = new Aliyun([
    'access_key_id' => '',
    'access_key_secret' => '',
    'region_id' => '',
    'uid' => '',
    'mts' => [
        'host' => '',
        'bucket' => '',
        'location' => '',
        'pipeline_id' => null,
    ],
]);
```

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/sevming/aliyun/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/sevming/aliyun/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT