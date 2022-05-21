<?php
/*
    Demonstration of htaccessMinify(), also a handy 
    utility application.

    Author: https://github.com/jxmot
    Repository: https://github.com/jxmot/htaccess-minifier

    License: MIT 
    Copyright 2022 jxmot
*/
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
?>