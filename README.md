# FyreCSP

**FyreCSP** is a free, open-source content security policy library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Basic Usage](#basic-usage)
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
use Fyre\Security\ContentSecurityPolicy;
```


## Basic Usage

- `$options` is an array containing options for the middleware.
    - `default` is an array containing the policy directives, and will default to *[]*.
    - `report` is an array containing the report-only directives, and will default to *null*.
    - `reportTo` is an array containing the Report-To header value, and will default to *[]*.

```php
$csp = new ContentSecurityPolicy($options);
```

**Autoloading**

It is recommended to bind the *ContentSecurityPolicy* to the [*Container*](https://github.com/elusivecodes/FyreContainer) as a singleton.

```php
$container->singleton(ContentSecurityPolicy::class);
```


## Methods

**Add Headers**

Add CSP headers to a [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses).

- `$response` is a [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses).

```php
$newResponse = $csp->addHeaders($response);
```

**Clear**

Clear all policies.

```php
$csp->clear();
```

**Create Policy**

Create a [*Policy*](#policies).

- `$key` is a string representing the policy key, and should be one of either `ContentSecurityPolicy::DEFAULT` or `ContentSecurityPolicy::REPORT`.
- `$directives` is an array containing the directives to add, and will default to *[]*.

```php
$csp->createPolicy($key, $directives);
```

**Get Policy**

Get a [*Policy*](#policies).

- `$key` is a string representing the policy key, and should be one of either `ContentSecurityPolicy::DEFAULT` or `ContentSecurityPolicy::REPORT`.

```php
$policy = $csp->getPolicy($key);
```

**Get Policies**

Get all policies.

```php
$policies = $csp->getPolicies();
```

**Get Report To**

Get the Report-To values.

```php
$reportTo = $csp->getReportTo();
```

**Has Policy**

Determine whether a policy exists.

- `$key` is a string representing the policy key, and should be one of either `ContentSecurityPolicy::DEFAULT` or `ContentSecurityPolicy::REPORT`.

```php
$hasPolicy = $csp->hasPolicy($key);
```

**Set Policy**

Set a policy.

- `$key` is a string representing the policy key, and should be one of either `ContentSecurityPolicy::DEFAULT` or `ContentSecurityPolicy::REPORT`.
- `$policy` is a [*Policy*](#policies).

```php
$csp->setPolicy($key, $policy);
```

**Set Report To**

Set the Report-To values.

- `$reportTo` is an array containing the [Report-To](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/report-to) values.

```php
$csp->setReportTo($reportTo);
```


## Policies

**Add Directive**

Add options to a directive.

- `$directive` is a string representing the directive.
- `$value` is a string, or an array of strings containing the values to add. For directives that don't require values, you can set this to *true* or *false* indicating whether to include the directive.

```php
$newPolicy = $policy->addDirective($directive, $value);
```

**Get Directive**

Get the options for a directive.

- `$directive` is a string representing the directive.

```php
$options = $policy->getDirective($directive);
```

**Get Header**

Get the header string.

```php
$header = $policy->getHeader();
```

**Has Directive**

Determine whether a directive exists.

- `$directive` is a string representing the directive.

```php
$hasDirective = $policy->hasDirective($directive);
```

**Remove Directive**

Remove a directive.

- `$directive` is a string representing the directive.

```php
$newPolicy = $policy->removeDirective($directive);
```


## Middleware

```php
use Fyre\Security\Middleware\CspMiddleware;
```

- `$csp` is a *ContentSecurityPolicy*.

```php
$middleware = new CspMiddleware($csp);
```

**Process**

- `$request` is a [*ServerRequest*](https://github.com/elusivecodes/FyreServer#server-requests).
- `$handler` is a [*RequestHandler*](https://github.com/elusivecodes/FyreMiddleware#request-handlers).

```php
$response = $middleware->process($request, $handler);
```

This method will return a [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses).