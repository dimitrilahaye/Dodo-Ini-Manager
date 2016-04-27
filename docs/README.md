# My Project Documentation

## Table of Contents

* [FileIni](#fileini)
    * [__construct](#__construct)
    * [getPath](#getpath)
    * [rename](#rename)
    * [copy](#copy)
    * [move](#move)
    * [get](#get)
    * [set](#set)
    * [rewrite](#rewrite)
    * [rm](#rm)
    * [before](#before)
    * [after](#after)
    * [hasNext](#hasnext)
    * [getNext](#getnext)
    * [getKey](#getkey)
    * [setKey](#setkey)
    * [writeInKey](#writeinkey)
    * [rewriteKey](#rewritekey)
    * [rewriteInKey](#rewriteinkey)
    * [rmInKey](#rminkey)
    * [rmKey](#rmkey)
    * [keyHasNext](#keyhasnext)
    * [getNextKey](#getnextkey)
    * [moveKey](#movekey)
    * [beforeKey](#beforekey)
    * [afterKey](#afterkey)

## FileIni

General class for DodoIniManager. Provides all the methods to :
<ul>
<li> Create and modify file.ini </li>
<li> Create and modify sections </li>
<li> Create and modify section's keys and their values </li>
</ul>



* Full name: \DodoIniManager\Classes\FileIni


### __construct

Method constructor. When a new FileIni object is instantiated, if file doesn't exists, we create it.

```php
FileIni::__construct( string $path ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$path` | **string** | File's path for this FileIni object. |



**See Also:**

* \DodoIniManager\Classes\DodoIniManager\Classes\FileIni::createFile() - For the creation of the file.ini.

---

### getPath

Get the path for this FileIni object.

```php
FileIni::getPath(  ): string
```





**Return Value:**

Returns the file's path for this FileIni object.



---

### rename

Change the file's name.

```php
FileIni::rename( string $name ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string** | New name for this file. |




---

### copy

Create a copy of the file with another name, at the same location in file system.

```php
FileIni::copy( string $name ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string** | Name for the copied file. |




---

### move

Change location for this file. The original file is deleted.

```php
FileIni::move( string $path ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$path` | **string** | The new path for this file. |




---

### get

Get a section contained into this file.

```php
FileIni::get( string $section ): mixed[]
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$section` | **string** | The target section's key. |


**Return Value:**

Returns the section from parsed file.



---

### set

Add a new section to this file. You are able to add an array of sub-keys for this new section.

```php
FileIni::set( string $section,  $array = null ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$section` | **string** | The section's key to add. |
| `$array` | **** |  |




---

### rewrite

Modify the key of a section.

```php
FileIni::rewrite( string $section, string $newSection ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$section` | **string** | The section's key to modify. |
| `$newSection` | **string** | The new section's key. |




---

### rm

Remove the section with the key passed in argument.

```php
FileIni::rm( string $section ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$section` | **string** | The section's key of the section to remove. |




---

### before

Move an entire section before another one.

```php
FileIni::before( string $section, string $before ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$section` | **string** | The section's key of the section to move. |
| `$before` | **string** | The section's key of the section before wich we want to move our section. |




---

### after

Move an entire section after another one.

```php
FileIni::after( string $section, string $after ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$section` | **string** | The section's key of the section to move. |
| `$after` | **string** | The section's key of the section after wich we want to move our section. |




---

### hasNext

Check if this section has another section after it.

```php
FileIni::hasNext( string $section ): boolean
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$section` | **string** | The section's key we want to evaluate. |


**Return Value:**

True if this section has another one after it, false if not.



---

### getNext

Get the next section after the section'key passed in argument.

```php
FileIni::getNext( string $section ): mixed[]
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$section` | **string** | The section's key that we want to return the following. |


**Return Value:**

Returns the following section from parsed file.



---

### getKey

Get an element contained into a section of this file.

```php
FileIni::getKey( string $section, string $element ): string
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$section` | **string** | The section's key of the target element. |
| `$element` | **string** | The target element. |


**Return Value:**

Returns the value of the target element



---

### setKey

Add a new element into a section of this file. You are able to add an array of sub-keys in second
argument in order to add many elements into the target section or just a string in order to add a simple
sub-key.

```php
FileIni::setKey( string $section, mixed[] $element ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$section` | **string** | The section's key of the target element. |
| `$element` | **array<mixed,mixed>** | The target element. |




---

### writeInKey

Add a value into the element of a section into this file.

```php
FileIni::writeInKey( string $section, string $element, string $content ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$section` | **string** | The section's key of the target element. |
| `$element` | **string** | The target element. |
| `$content` | **string** | The value to write in the element. |




---

### rewriteKey

Modify the element's key of a section into this file.

```php
FileIni::rewriteKey( string $section, string $element,  $newElement ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$section` | **string** | The section's key of the target element. |
| `$element` | **string** | The target element. |
| `$newElement` | **** |  |




---

### rewriteInKey

Overwrite a value into the element of a section into this file.

```php
FileIni::rewriteInKey( string $section, string $element, string $content ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$section` | **string** | The section's key of the target element. |
| `$element` | **string** | The target element. |
| `$content` | **string** | The value to write in the element. |




---

### rmInKey

Remove the value into a target element in a section of this file.

```php
FileIni::rmInKey( string $section, string $element ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$section` | **string** | The section's key of the target element. |
| `$element` | **string** | The target element. |




---

### rmKey

Remove the entire element's key/value into a section of this file.

```php
FileIni::rmKey( string $section, string $element ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$section` | **string** | The section's key of the target element. |
| `$element` | **string** | The target element to remove. |




---

### keyHasNext

Check if this section's element has another element after it.

```php
FileIni::keyHasNext( string $section, string $element ): boolean
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$section` | **string** | The section's key of the target element. |
| `$element` | **string** | The target element to evaluate. |


**Return Value:**

True if this element has another one after it, false if not.



---

### getNextKey

Get the next element after the element'key passed in argument.

```php
FileIni::getNextKey( string $section, string $element ): string
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$section` | **string** | The section's key of the target element. |
| `$element` | **string** | The section's element's key that we want to return the following. |


**Return Value:**

Returns the following element from parsed file.



---

### moveKey

Move the element into another section. The original element is deleted.

```php
FileIni::moveKey( string $section, string $element,  $newSection ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$section` | **string** | The section's key where to move the element. |
| `$element` | **string** | The element's key to move. |
| `$newSection` | **** |  |




---

### beforeKey

Move an entire element's key/value before another one.

```php
FileIni::beforeKey( string $section, string $element, string $before ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$section` | **string** | The section's key of the element to move. |
| `$element` | **string** | The element's key of the element to move. |
| `$before` | **string** | The element's key before wich we want to move our element. |




---

### afterKey

Move an entire element's key/value after another one.

```php
FileIni::afterKey( string $section, string $element, string $after ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$section` | **string** | The section's key of the element to move. |
| `$element` | **string** | The element's key of the element to move. |
| `$after` | **string** | The element's key after wich we want to move our element. |




---



--------
> This document was automatically generated from source code comments on 2016-04-27 using [phpDocumentor](http://www.phpdoc.org/) and [cvuorinen/phpdoc-markdown-public](https://github.com/cvuorinen/phpdoc-markdown-public)
