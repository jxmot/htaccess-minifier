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
$htaminfile = './htaccessmin.json';
$ret = new stdClass();
// if the file exists then it will contain 
// the argument values, otherwise use defaults
if(file_exists($htaminfile) === false) {
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
} else {
    $htamin = json_decode(file_get_contents($htaminfile));
    $ret = htaccessMinify($htamin->fileroot . $htamin->in, 
                          $htamin->fileroot . $htamin->out, 
                          $htamin->exclblocks, 
                          $htamin->rmvsp, $htamin->rmvnl);
}

// how did it go?
if(isset($ret->r) && ($ret->r === true)) {
    exit($ret->m .' - lines in: '.$ret->i.', lines out: '.$ret->o."\n");
} else {
    exit('ERROR - '.$ret->m."\n");
}
?>