<?php

class Scrapper{
    public  $sitemap;
    public  $XPath;
    
    public function __construct() {}
    
    public function setSiteMap(string $sitemap){
        $this->sitemap = $sitemap;
    }
    
    public function setXPath(string $XPath){
        $this->XPath = $XPath; 
    }
    
    public function getXPath(){
        return $this->XPath; 
    }
    public function getSitemap(){
        return $this->sitemap;
    }
    public function addPatterns(){}

    public function removePatterns(){}
    public function getPatterns(){}
    
    public function getLinks(){
        $init_time = new DateTime('now');
        $page = file_get_contents($this->sitemap);
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
        $end_of_time = new DateTime('now');
        
        echo "All file count is: " . count($all_locs) ."\n";
        echo "Total Website Get Time: ". $end_of_time->getTimestamp()- $init_time->getTimestamp() ." Seconds.\n";
        return $all_locs;
    }

    public function getContent($all_links){
        $init_time = new DateTime('now');
        $errors_404 = 0;
        $null_counter = 0;
        $healthy_content_counter = 0;
        for ($i = 1; $i < 10; $i++) {
            print_r($all_links[$i]);
            echo " Content Number: $i"."\n";
            
            $context = stream_context_create(array(
                'http' => array('ignore_errors' => true),
            ));
            
            $page = file_get_contents($all_links[$i], false, $context);
        
            $http_status = $http_response_header[0];
            preg_match('/\d{3}/', $http_status, $matches);
            $status_code = isset($matches[0]) ? (int)$matches[0] : 0;
        
            if ($status_code === 404) {
                print(" ERROR in $i content: (404 error)! \n");
                $errors_404++;
            } elseif ($page !== false) {
                $clean = preg_replace('#<a.*?>.*?</a>#i', '', $page);
        
                libxml_use_internal_errors(true);
                $doc = new DOMDocument();
                $doc->loadHTML($clean);
                libxml_use_internal_errors(false);
        
                $xpath = new \DOMXpath($doc);
                $articles = $xpath->query($this->getXPath());
                $content = [];
        
                foreach ($articles as $textNode) {
                    $content[] = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n",  $textNode->nodeValue);
        
                }
                if ($content != null) {
                    $myfile = fopen($i . ".txt", "w") or die("Unable to open file!");
                    fwrite($myfile, implode("\n", $content));
                    fclose($myfile);
                    $healthy_content_counter++;
                } else {
                    print(" ERROR in $i content: (Null content)! \n");
                    $null_counter++;
        
                }
            }
        }
        echo "Total Null Website Contents: " . $null_counter."\n" ;
        echo "Total 404 Responses: " . $errors_404 . "\n";
        echo "Total Contents: ". $healthy_content_counter . "\n";
        $end_of_time = new DateTime('now');
        
        echo "Total Get Content Time: ". $end_of_time->getTimestamp()- $init_time->getTimestamp() ." Seconds.\n";
    }
    
}
