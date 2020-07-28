EXPIRENZA Short Url Helper
=======

Helper for generate short url trough EXPIRENZA Short Url Service


## Installation

- All the `code` required to get started
- For start to use this helper, you must have server API address and access token

#### Clone

- Clone this repo to your local machine using `https://github.com/EXPIRENZA/shorturl-helper`

#### Composer

```shell
$ composer require expirenza/short-helper "^1.0.0"
```

### How to use

##### Example #1
Convert a single url to getting single short url
```code
<?php 

use Expirenza\short\Link;

$class = new Link('some token', 'https://server.url');
$result = $class->getOne('https://google.com');

// output
var_dump($result);
```

Output: 
```code
string(35) "https://server.url/somerandomstring"
```
##### Example #2
Convert an url array to random urls arrays
```code
<?php 

use Expirenza\short\Link;

$class = new Link('some token', 'https://server.url');
$result = $class->getMany(['https://google.com', 'https://mail.com']);

// output
var_dump($result);
```

Output: 
```code
array(2) {
  ["https://google.com"]    => string(32) "https://server.url/randomstring0"
  ["https://mail.com"]      => string(32) "https://server.url/randomstring1"
}

```

##### Example #3
Convert an array of urls with early predefined codes
```code
<?php 

use Expirenza\short\Link;

$class = new Link('some token', 'https://server.url');

$source = [
    0 => $class->getUniqueFormatItem('https://google.com', 'test_'.time().'_'.random_int(0, 99)),
    1 => $class->getUniqueFormatItem('https://mail.com', 'test_'.time().'_'.random_int(0, 99)),
];

$result = $class->getUniqueMany($source);

// output
var_dump($result);
```

Output: 
```code
array(2) {
  [0]=>
  object(Expirenza\short\objects\UniqueResponse)#7 (3) {
    ["url":protected] => string(18) "https://google.com"
    ["code":protected] => string(18) "test_1595918241_18"
    ["success":protected] => bool(true)
  }
  [1]=>
  object(Expirenza\short\objects\UniqueResponse)#8 (3) {
    ["url":protected] => string(16) "https://mail.com"
    ["code":protected]=> string(17) "test_1595918241_4"
    ["success":protected]=> bool(true)
  }
}

```
In this case, access to protected property realised trough "get<Property>()" or "asArray()" methods:
```code
<?php
foreach ($result as $item) {
    echo $item->getUrl();
    echo $item->getCode();
    echo $item->getSuccess();
}

foreach ($result as &$item) {
    $item = $item->asArray();
    echo $item['url'];
    echo $item['code'];
    echo $item['success'];
}
``` 
##### Warning! If your predefined code is already used, you will get success property equal to false. 


## Support

Reach out to me at one of the following places!

<https://go.expirenza.com?code=SUPPORT>

info@expirenza.com
