# FyreCSP

**FyreCSP** is a free, content security policy library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Methods](#methods)
- [Policies](#policies)
- [Middleware](#middleware)



## Installation

**Using Composer**

```
composer require fyre/csp
```

In PHP:

```php
use Fyre\Security\CspBuilder;
```


## Methods

**Add Headers**

Add CSP headers to a [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses).

```php
CspBuilder::addHeaders($response);
```

**Clear**

Clear all policies.

```php
CspBuilder::clear();
```

**Create**

Create a [*Policy*](#policies).

- `$key` is a string representing the policy key, and should be one of either "*policy*" or "*report*".
- `$directives` is an array containing the directives to add, and will default to *[]*.

```php
CspBuilder::create($key, $directives);
```

**Get**

Get a [*Policy*](#policies).

- `$key` is a string representing the policy key, and should be one of either "*policy*" or "*report*".

```php
$policy = CspBuilder::get($key);
```

**Get Policies**

Get all policies.

```php
$policies = CspBuilder::getPolicies();
```


## Policies

**Add Directive**

Add options to a directive.

- `$directive` is a string representing the directive.
- `$value` is a string, or an array of strings containing the values to add. For directives that don't require values, you can set this to *true* or *false* indicating whether to include the directive.

```php
$policy->addDirective($directive, $value);
```

**Get Header**

Get the header string.

```php
$header = $policy->getHeader();
```


## Middleware

```php
use Fyre\CSP\Middleware\CspMiddleware;
```

- `$options` is an array containing options for the middleware.
    - `default` is an array containing the default directives, and will default to *[]*.
    - `report` is an array containing the report-only directives, and will default to *null*.
    - `reportTo` is an array containing the Report-To header value, and will default to *[]*.

```php
$middleware = new CspMiddleware($options);
```

**Process**

- `$request` is a [*ServerRequest*](https://github.com/elusivecodes/FyreServer#server-requests).
- `$handler` is a [*RequestHandler*](https://github.com/elusivecodes/FyreMiddleware#request-handlers).

```php
$response = $middleware->process($request, $handler);
```

This method will return a [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses).