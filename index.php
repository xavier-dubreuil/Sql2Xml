<?php

header('content-type: text/xml');

set_error_handler("myErrorHandler");
set_exception_handler("myExceptionHandler");

include 'classes/database.class.php';
include 'classes/sql2xml.class.php';

include 'cfg/database.php';
include 'cfg/parameters.php';
include 'cfg/functions.php';

$view    = !empty($_GET['view']) ? $_GET['view'] : null;
$manga   = !empty($_GET['manga']) ? $_GET['manga'] : null;
$chapter = !empty($_GET['chapter']) ? $_GET['chapter'] : null;

$transform = new sql2xml();
echo $transform->transform($config, 'xml/iManga.xml');


function myErrorHandler($errno, $errstr, $errfile, $errline)
{
   if (!(error_reporting() & $errno)) {
        // Ce code d'erreur n'est pas inclus dans error_reporting()
        return;
    }

    switch ($errno) {
    case E_USER_ERROR:
        echo '<?xml version="1.0" ?>'.chr(10);
        echo '<error>'.chr(10);
        echo '    <Code>'.$errno.'</Code>'.chr(10);
        echo '    <String>'.$errstr.'<String />'.chr(10);
        echo '    <File>'.$errstr.'<File>'.chr(10);
        echo '    <Line>'.$errstr.'<Line>'.chr(10);
        echo '    <PHPVersion>'.PHP_VERSION.'<PHPVersion>'.chr(10);
        echo '    <PHPOS>'.PHP_OS.'<PHPOS>'.chr(10);
        echo '</error>';
        exit(1);
        break;
    }

    /* Ne pas exécuter le gestionnaire interne de PHP */
    return true;
}

function myExceptionHandler($e)
{

    echo '<?xml version="1.0" ?>'.chr(10);
    echo '<exception>'.chr(10);
    echo '    <Code>'.$e->getCode().'</Code>'.chr(10);
    echo '    <String>'.$e->getMessage().'<String />'.chr(10);
    echo '    <File>'.$e->getFile().'<File>'.chr(10);
    echo '    <Line>'.$e->getLine().'<Line>'.chr(10);
    echo '    <PHPVersion>'.PHP_VERSION.'<PHPVersion>'.chr(10);
    echo '    <PHPOS>'.PHP_OS.'<PHPOS>'.chr(10);
    echo '</exception>';
}

?>
