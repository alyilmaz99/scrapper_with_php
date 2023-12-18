<?php

$url2 = "https://www.nih.gov/sitemap.xml?page=1";
$page = file_get_contents($url2);
$doc = new DOMDocument();

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


echo count($all_locs);

$website = $all_locs[2];
echo "All file count is: " . count($all_locs) ."\n";


$null_counter = 0;
for ($i = 1; $i < count($all_locs)  ; $i++) {
    print_r($all_locs[$i]);
    echo"\n";
    $page = file_get_contents($all_locs[$i]);
    $clean =  preg_replace('#<a.*?>.*?</a>#i', '', $page);


    libxml_use_internal_errors(true);
    $doc->loadHTML($clean);
    libxml_use_internal_errors(false);

    $xpath = new \DOMXpath($doc);
    $articles = $xpath->query('//*[@id="page-content"]/div[3]/div/div[1]/div');
    $content = [];
    
  
    foreach ($articles as $textNode) {
        $content[] = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n",  $textNode->nodeValue);
    }
    if($content != null){
        $myfile = fopen($i. ".txt", "w") or die("Unable to open file!");
        fwrite($myfile, implode("\n", $content));
        fclose($myfile);
    }else {
        $null_counter++;
    }

}
echo "null: " . $null_counter;




