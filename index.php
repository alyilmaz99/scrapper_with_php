<?php

$url2 = "https://www.nih.gov/sitemap.xml?page=1";
$page = file_get_contents($url2);

$html = utf8_decode($page);

$start_index = 0;
$start_tag = "<loc>";
$end_tag = "</loc>";
$all_locs = [];

while (true) {
    $start_index = strpos($html, $start_tag, $start_index);
    if ($start_index === false) {
        break;
    }

    $start_index += strlen($start_tag);
    $end_index = strpos($html, $end_tag, $start_index);
    if ($end_index === false) {
        break;
    }

    $loc = substr($html, $start_index, $end_index - $start_index);
    $all_locs[] = $loc;

    $start_index = $end_index + strlen($end_tag);
}
$pattern = "/<loc.*?>.*?<\/loc.*?>/i";
preg_match_all($pattern, $html, $matches);

$all_locs = array_map(function ($match) {
    return strip_tags($match);
}, $matches[0]);


print_r($all_locs);
echo count($all_locs);
$pattern2 = "/<h1.*?>.*?<\/h1.*?>/i";
$pattern3 = "/<p.*?>.*?<\/p.*?>/i";
$pattern4 = "/<ul.*?>.*?<\/ul.*?>/i";
$pattern5 = "/<li.*?>.*?<\/li.*?>/i";
$pattern5 = "/<h3.*?>.*?<\/h3.*?>/i";


$website = $all_locs[2];
echo "All file count is: " . count($all_locs);


for ($i = 1; $i < 3; $i++) {
    $page = file_get_contents($website);

    $websiteHtml = utf8_decode($page);
    $pattern3 = "/<p.*?>.*?<\/p.*?>/i";

    preg_match_all($pattern3, $websiteHtml, $matches);

    $ps = array_map(function ($match) {
        return strip_tags($match);
    }, $matches[0]);

    $myfile = fopen($i . ".txt", "w") or die("Unable to open file!");
    fwrite($myfile, implode("\n", $ps));
    fclose($myfile);
}




?>