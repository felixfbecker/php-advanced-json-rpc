# Advanced JSONRPC

[![Version](https://img.shields.io/packagist/v/felixfbecker/advanced-json-rpc.svg)](https://packagist.org/packages/felixfbecker/advanced-json-rpc)
[![Build Status](https://travis-ci.org/felixfbecker/php-advanced-json-rpc.svg?branch=master)](https://travis-ci.org/felixfbecker/php-advanced-json-rpc)
[![Coverage](https://codecov.io/gh/felixfbecker/php-advanced-json-rpc/branch/master/graph/badge.svg)](https://codecov.io/gh/felixfbecker/php-advanced-json-rpc)
[![Dependency Status](https://gemnasium.com/badges/github.com/felixfbecker/php-advanced-json-rpc.svg)](https://gemnasium.com/github.com/felixfbecker/php-advanced-json-rpc)
[![semantic-release](https://img.shields.io/badge/%20%20%F0%9F%93%A6%F0%9F%9A%80-semantic--release-e10079.svg)](https://github.com/semantic-release/semantic-release)
[![License](https://img.shields.io/packagist/l/felixfbecker/advanced-json-rpc.svg)](https://packagist.org/packages/felixfbecker/advanced-json-rpc)

Provides basic classes for requests and responses in JSONRPC and a `Dispatcher` class that can decode a JSONRPC request
and call appropiate methods on a target, coercing types of parameters by type-hints and `@param` tags.

Supports nested targets: If the method is something like `myNestedTarget->theMethod`, the dispatcher will look for a
`myNestedTarget` property on the target and call `theMethod` on it. The delimiter is configurable and defaults to the
PHP object operator `->`.

## Example

```php
use AdvancedJsonRpc\Dispatcher;

class Argument 
{
    public $aProperty;
}

class Target
{
    public function someMethod(Argument $arg)
    {
        // $arg instanceof Argument === true
        // $arg->aProperty === 123
        return 'Hello World';
    }
}

$dispatcher = new Dispatcher(new Target());

$result = $dispatcher->dispatch('
    {
        "jsonrpc": "2.0",
        "id": 1,
        "method": "someMethod", 
        "params": {
            "arg": {"aProperty": 123}
        }
    }
');

// $result === "Hello World"
```

### Nested Targets

```php
use AdvancedJsonRpc\Dispatcher;

class TextDocumentManager 
{
    public function didOpen(string $uri)
    {
        return 'Thank you for this information';
    }
}

class LanguageServer
{
    public $textDocument;

    public function __construct()
    {
        $this->textDocument = new TextDocumentManager();
    }
}

$dispatcher = new Dispatcher(new LanguageServer(), '/');

$result = $dispatcher->dispatch('
    {
        "jsonrpc": "2.0",
        "id": 1,
        "method": "textDocument/didOpen", 
        "params": {
            "uri": "file:///c/Users/felix/test.php"
        }
    }
');

// $result === "Thank you for this information"
```
