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
* `example_htaccessmin.json` - Contains argument values to be used when calling `htaccessMinify()`. See []().

To minify an `.htaccess` file:

**Note:** You will need PHP 5.6 or newer. Check for PHP with `php --version`.

1) Copy an `.htaccess` file into the same folder where the two PHP files are. *Or, use the sample file in this repository.*
2) Run this command - `php ./run.php`.

You *should* see something like this - `min.htaccess has been saved - lines in: 259, lines out: 171`. And you will have a new file named `min.htaccess`.

## Running as a Utility

This is how I use htaccess-minifier. I add the 3 files to a folder in a website project and edit the `example_htaccessmin.json` file and save it as **`htaccessmin.json`**.

The JSON file contains argument values to be used when calling `htaccessMinify()`. For example:

```
{
    "in":".htaccess",
    "fileroot":"path/to/your/files/",
    "out": "min.htaccess",
    "exclblocks": [
        ["BEGIN cPanel-generated handler", "END cPanel-generated handler"]
        ,["### start", "### end"]
    ],
    "rmvsp": true,
    "rmvnl": true
}
```

Edit the value of **`"fileroot"`** to point to where your `.htaccess` file is located. And edit the rest of the values as needed. See the following section for explanations of the other values' use.

## htaccessMinify() Details

See [htaccessMinify()](htaccessMinify.php#L69-L128) source.

**Synopsis:**

```php
function htaccessMinify($in, $out, $exclblocks = null, $rmvsp = false, $rmvnl = false)
```

Where:
* `$in` - the path+name of the file to minimize, **required**, EOL must be newline only, no CRLF pairs
* `$out` - the path+name of the minimized file, **required**
* `$exclblocks` - a 2 dimensional array, contains *start* and *end* block markers, **optional**
* `$rmvsp` - if `true` remove extra white space, **optional**
* `$rmvnl` - if `true` remove blank lines, **optional**

The `$in` and `$out` arguments are required, the rest are optional and have *default* values.

**Example Usage:**

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

Minimize the file, and additionally remove empty lines: 

```php
$ret = htaccessMinify('.htaccess', 'min.htaccess', null, false, true);
```

Minimize the file, set exclusion blocks, reduce spaces, and remove empty lines: 

```
// The following are the "start" and "end" of what is 
// referred to as an "exclusion block". It is a block 
// of lines that will not be minified.
$exclblocks = [
    ['BEGIN cPanel-generated handler', 'END cPanel-generated handler']
    ,['### start', '### end']
];
// minify the file, assuming it's in the same location as this script...
// provide exclusion block markers, reduce space characters, remove empty lines...
$ret = htaccessMinify('.htaccess', 'min.htaccess', $exclblocks, true, true);
```

The return value `$ret` should be checked, for example:

```
// how did it go?
if(isset($ret->r) && ($ret->r === true)) {
    exit($ret->m .' - lines in: '.$ret->i.', lines out: '.$ret->o."\n");
} else {
    exit('ERROR - '.$ret->m."\n");
}
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

See [blockCheck()](htaccessMinify.php#L44-L68) source.

### Space Reduction

*Space character reduction* is done per line. Essentially it's this - 

* Read a line
* Substitute all "  " (*2 space*) occurrences with " " (*1 space*)
* Repeat the substitution until "  " (*2 spaces*) is no longer found in the line
* Write the line to the output file

See [reduceSpace()](htaccessMinify.php#L18-L25) source.

### Empty Line Removal

If a line contains **only** a `\n` (*newline*) do not write it to the output file.

## What Else Can It Do?

Well, probably with some *minor modifications* this script could do the same type of *minification* for shell script files. Just watch out for the first line!

---
<img src="http://webexperiment.info/extcounter/mdcount.php?id=htaccess-minifier">
