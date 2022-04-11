# htaccess-minifier

A PHP script to "minimize" an `.htaccess` file.

## History

I was looking into ways to improve the performance of a web server. One of the things I looked at was the `.htaccess` file. In addition to being a little long it also had a measurable amount of comments. The comments are useful to humans but the server doesn't really care.

After using an *editor macro* to effectively minimize the `.htaccess` file I decided that the task would have to be repeated for other web servers. *Ugh*

So I created a PHP script that will do the work for me... and I'm sharing it with you...

## What is Minified?

The "minification" done here is not *exactly* like what's done for JavaScript, CSS, etc. But the script will - 

* Leave *marked* blocks intact.
* Remove blank lines.
* Remove lines that begin with `#` (*comments*).
* Reduce white-space, leaves only one space character between non-space characters.
* Create output that is *human readable*.

## Running the Demonstration

There are two PHP files provided - 

* `run.php` - Calls the `htaccessMinify.php:htaccessMinify()` function.
* `htaccessMinify.php` - Contains `htaccessMinify()` and other functions for minification

To minify an `.htaccess` file:

**Note:** You will need PHP 7 or newer. Check for PHP with `php --version`.

1) Copy an `.htaccess` file into the same folder where the two PHP files are. *Or, use the sample file in this repository.*
2) Run this command - `php ./run.php`.

You *should* see something like this - `min.htaccess has been saved - lines in: 259, lines out: 171`. And you will have a new file named `min.htaccess`.

## htaccessMinify() Details

The `run.ph` script contains - 

```php
require_once('./htaccessMinify.php');
/*
    Let's Minify!
*/
// The following are the "start" and "end" of what is 
// referred to as an "exclusion block". It is a block 
// of lines that will not be minified.
$exclblocks = [
    ['BEGIN cPanel-generated handler', 'END cPanel-generated handler']
    ,['### start', '### end']
];
// minify the file... 
// provide exclusion block markers, reduce space characters, remove empty lines...
$ret = htaccessMinify('.htaccess', 'min.htaccess', $exclblocks, true, true);
// how did it go?
if($ret->r === true) {
    exit($ret->m .' - lines in: '.$ret->i.', lines out: '.$ret->o."\n");
} else {
    exit('ERROR - '.$ret->m."\n");
}
```

**Synopsis:**

```php
function htaccessMinify($in, $out, $exclblocks = null, $rmvsp = false, $rmvnl = false)
```

Where:
* `$in` - the path+name of the file to minimize
* `$out` - the path+name of the minimized file
* `$exclblocks` - a 2 dimensional array, contains *start* and *end* block markers
* `$rmvsp` - if `true` remove extra white space
* `$rmvnl` - if `true` remove blank lines

The `$in` and `$out` arguments are required, the rest are optional and have *default* values.

Minimal Usage:

```php
// minify the file... 
// NO exclusion blocks, NO space reduction, NO empty line removal
$ret = htaccessMinify('.htaccess', 'min.htaccess');
// how did it go?
if($ret->r === true) {
    exit($ret->m .' - lines in: '.$ret->i.', lines out: '.$ret->o."\n");
} else {
    exit('ERROR - '.$ret->m."\n");
}
```

Minimize the file, and additionally remove empty lines - 

```php
$ret = htaccessMinify('.htaccess', 'min.htaccess', null, false, true);
```

### Exclusion Block Markers

To *exclude* a block of lines from minification they need to be marked with *unique* start and ending lines. For example - 

```
### start
some stuff goes       here.
# and a comment
### end
```

With the following *exclusion markers* - 

```php
$exclblocks = [
    ['### start', '### end']
];
```

Any block that is "marked" will be left intact and copied into the minified file. 

### Space Reduction

*Space character reduction* is done per line. Essentially it's this - 

* Read a line
* Substitute all "  " (*2 space*) occurances with " " (*1 space*)
* Repeat the substitution until "  " (*2 spaces*) is no longer found in the line
* Write the line to the output file

See [reduceSpace()](htaccessMinify.php#L18-L25) source.

### Empty Line Removal

