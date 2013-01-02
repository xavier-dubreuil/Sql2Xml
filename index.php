<?php

header('content-type: text/xml');

include 'classes/database.class.php';
include 'classes/sql2xml.class.php';

include 'cfg/database.php';
include 'cfg/parameters.php';
include 'cfg/functions.php';

$view    = !empty($_GET['view']) ? $_GET['view'] : null;
$manga   = !empty($_GET['manga']) ? $_GET['manga'] : null;
$chapter = !empty($_GET['chapter']) ? $_GET['chapter'] : null;

if ($view == 'mangalist') {
    $xml = 'xml/mangaList.xml';
} else if ($view == 'chapterlist') {
    $xml = 'xml/chapterList.xml';
} else if (!is_null($manga)) {
    $xml = 'xml/manga.xml';
} else if (!is_null($chapter)) {
    $xml = 'xml/chapter.xml';
} else {
    $xml = 'xml/site.xml';
}

$transform = new sql2xml($config);
echo $transform->transform($xml);

?>
