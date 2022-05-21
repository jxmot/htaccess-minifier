<?php
/*
    .htaccess Minifier - A PHP script to "minimize" an htaccess file

    This was created to reduce the size of an htaccess flie that is 
    used on an Apache server where cPanel is also in use.

    As it turns out cPanel adds a block of lines to a site's htaccess 
    file. Fortunately cPanel marks the blocks with a line of comments 
    at the start and end of each block.

    Author: https://github.com/jxmot
    Repository: https://github.com/jxmot/htaccess-minifier

    License: MIT 
    Copyright 2022 jxmot
*/
// reduce white-space characters until there are no  
// more than one space between non-space characters.
function reduceSpace($line) {
    while(strstr($line, '  ') !== false) {
        $line = str_replace('  ', ' ', $line);
    }
    return $line;
}

// write a line to the output file, optionally reduce 
// the amount of white-space before writing it to the 
// output file
function writeLine($outp, $line, &$ret, $rmvsp = false) {
$r = false;

    if($rmvsp === true) $line = reduceSpace($line);
    if(fwrite($outp, $line, strlen($line)) === false) {
        $ret->m = 'error writing to '.$outfile;
        $r = $ret->r = false;
        break;
    } else {
        $ret->o = $ret->o + 1;
        $r = true;
    }
    return $r;
}
// check to see if an exclusion block has been found,
// if found then write the block to the file without 
// any minification.
function blockCheck($inp, $outp, $line, &$ret, $blockbegin, $blockend) {
$r = false;

    // yes, is it the start of an exclusion block?
    if(strstr($line, $blockbegin) !== false) {
        // do not reduce space chars inside of exclusion blocks
        if(writeLine($outp, $line, $ret) === false) {
            $r = false;
        } else {
            // continue until the end of the exclusion block
            while(($line = fgets($inp)) !== false) {
                $ret->i = $ret->i + 1;
                // do not reduce space chars inside of exclusion blocks
                if(writeLine($outp, $line, $ret) === false) break;
                // was this the end of the block?
                if(strstr($line, $blockend) !== false) break;
            }
            $r = true;
        }
    } else $r = false;
    return $r;
}
// minify the input file, optionally specify one or 
// more exclusion blocks, optionally reduce the 
// white-space, and optionally remove empty lines 
// from the output file.
function htaccessMinify($in, $out, $exclblocks = null, $rmvsp = false, $rmvnl = false) {
$ret = new stdClass();
$ret->r = false;    // true = success
$ret->m = '';       // result message
$ret->l = '';       // line text if failure
$ret->i = 0;        // quantity of input lines
$ret->o = 0;        // quantity of lines written

    if(($outp = fopen($out, 'w')) === false) {
        $ret->m = 'cannot open for output - '.$out;
        $ret->r = false;
    } else {
        // open the log file...
        if(($inp = fopen($in, 'r')) !== false) {
            // read one line at a time...
            while(($line = fgets($inp)) !== false) {
                $ret->i = $ret->i + 1;
                // is it a "\n" only line? if so, remove it
                if($rmvnl === true) if(strlen($line) === 1) continue;
                // look for a "#" in the line
                if(($firsthash = strpos($line, '#')) === false) { 
                    // no "#", write the line to the output file
                    if(writeLine($outp, $line, $ret, $rmvsp) === false) break;
                } else {
                    // are the blocks to exclude from minification AND
                    // was "#" the first character in the line?
                    if(($exclblocks !== null) && ($firsthash === 0)) {
                        // how many different blocks are there?
                        $limit = count($exclblocks);
                        for($ix = 0; $ix < $limit; $ix++) {
                            // check to see if this is an exclusion block, if so then pass it through
                            if(blockCheck($inp, $outp, $line, $ret, $exclblocks[$ix][0], $exclblocks[$ix][1]) === true)
                                break;
                        }
                    } else {
                        // the line has a "#", but it's not the first character
                        if(writeLine($outp, $line, $ret, $rmvsp) === false) break;
                    }
                }
            }
            // success!
            fflush($outp);
            fclose($outp);
            fclose($inp);
            if($ret->m === '') {
                $ret->m = $out . ' has been saved';
                $ret->r = true;
            }
        } else {
            fclose($outp);
            $ret->m = 'cannot open for input - '.$in;
            $ret->r = false;
        }
    }
    return $ret;
}
?>