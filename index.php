<?php
include_once("src/Scrapper.php");
$scrapper = new Scrapper();
$scrapper->setSiteMap("https://www.nbcnews.com/sitemap/nbcnews/sitemap-news");
$scrapper->setXPath('//*[@id="content"]/div[5]/div/article/div[1]/div/div[2]');
$links = $scrapper->getLinks();
$scrapper->getContent($links);