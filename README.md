# Teak

Teak is a utility generate a Markdown reference documentation for PHP classes, optimized for WordPress projects.

This reference can then be used in combination with a static site generator to create a documentation site.

What it can do:

- Generate Reference pages for your classes documented with DocBlocks.
- Generate a Hooks reference for your actions and filters documented with DocBlocks. 

## Installation

You can install the package with Composer:

```bash
composer require timber/teak
```

## Usage

### Generate a class reference

**Use a folder as the input**

```bash
./vendor/timber/teak/teak generate:class-reference ../timber/lib --output ./content/reference
```

This searches all the classes in `../timber/lib` and outputs the Markdown files into `./content/reference`.

**Use a single file**

```bash
./vendor/timber/teak/teak generate:class-reference ../timber/lib/Post.php --output ./content/reference
```

### Generate a hooks reference

The Hook Reference Generator will search all the files and output one single file, with all the hooks found.

```bash
./vendor/timber/teak/teak generate:hook-reference ../timber/lib --output ./content/hooks --hook_type=filter
./vendor/timber/teak/teak generate:hook-reference ../timber/lib --output ./content/hooks --hook_type=action
```

## Ignoring Structural Elements

An element (class, method, property) is ignored when **one of the following conditions** applies:

- No DocBlock is provided
- No `@api` tag is present
- An `@ignore` tag is present
- An `@internal` tag is present
- The visibility is `private` (applies to methods only)

This means that for Markdown files to be generated at all, you’ll need at least a DocBlock on a class, with an `@api` tag.

```php
/**
 * Class MyPublicClass
 *
 * @api
 */
class MyPublicClass {

}
```

## Limitations

This Compiler is not a full implementation of phpDocumentor. Rather, it tries to make code documentation that followes the [WordPress PHP Documentation Standards](https://make.wordpress.org/core/handbook/best-practices/inline-documentation-standards/php/) readable for everyone. Not all [official tags](https://make.wordpress.org/core/handbook/best-practices/inline-documentation-standards/php/#phpdoc-tags) are considered yet. Contributions are welcome.

## Roadmap

- [ ] Implement documentation for global functions outside of classes.
- [ ] CLI: accept a list of files.
- [ ] Optimize linking between Markdown documents.
