<?php

class WebScrap{
    function __construct(){
    }
    public function crap($url){
        $curl = curl_init();
        curl_setopt ($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);

        $content = curl_exec ($curl);
        if($content === false){
            echo 'Curl Error '.curl_error($curl);
        }

        $curl_info = curl_getinfo($curl);
        curl_close ($curl);
        return $content;
    }

    public function getValueByTagName($content, $start, $end){
        $position = strpos($content, $start);
        if($position === false){
            return '';
        } else{
            $s = strpos($content, $start) + strlen($start);
            $e = strlen($content);
            $content = substr($content, $s, $e);
            $s = 0;
            $e = strpos($content, $end);
            $content = substr($content, $s, $e);
            return $content;
        }
    }

    function scrapList($url){
        $content = $this->crap($url);
        $temp_alist = $this->getValueByTagName($content,'<div class="blockGroup-list layoutSingleColumn">','</body>');
        $alist_expode = explode('<article class="postArticle postArticle--short">',$temp_alist);
        $list_val = array();
        foreach($alist_expode as $lkey => $lval){
            $list_val[] = $this->getValueByTagName($lval,'<a href="','">');
        }
        $file = "links.txt";
        $list_val = json_encode($list_val);
        file_put_contents($file, $list_val);
    }

    function scrapContent($url){
        $content = $this->crap($url);
        $tmp_cont_1 = $this->getValueByTagName($content, '<div class="section-inner u-sizeFullWidth">', '</body>');
        $tmp_ttl = $this->getValueByTagName($content, '<div class="section-inner layoutSingleColumn">', '</div>');
        $tmp_header = $this->getValueByTagName($content, '<header class="container is-underMetabar u-size740">', '</header>');
        $cover_image_1 = $this->getValueByTagName($tmp_cont_1, 'src="', '"');
        $post_title = $this->getValueByTagName($tmp_ttl, 'graf-after--figure">', '</h3>');
        if(empty($post_title)){
            $post_title = $this->getValueByTagName($tmp_ttl, 'graf--first">', '</h3>');
        }
        if(empty($cover_image_1)){
            $section_image = $this->getValueByTagName($content, '<div class="section-background"', '</h3>');
            $section_image = $this->getValueByTagName($section_image, 'background-image: url(', ');');
            $cover_image_1 = $section_image;
        }
        $tmp_cont_2 = $this->getValueByTagName($content, '<div class="section-inner layoutSingleColumn">', '<footer');
        $author = $this->getValueByTagName($tmp_header, '<div class="col u-xs-size5of12">', '</div><div class="col u-xs-size7of12">');
        $remove_a_info = $this->getValueByTagName($author, '</a><span class', '</div>');
        $author = str_replace($remove_a_info,' ', $author);
        $expld_tst = explode('<div class="section-inner layoutSingleColumn">',$tmp_cont_2);
        echo '<strong>Post Title:</strong><br>'.$post_title.'<hr>';
        echo '<strong>Cover Image:</strong><br>'.$cover_image_1.'<hr>';
        echo '<strong>Post Author:</strong><br>'.$author.'<hr>';
        echo '<strong>Post Content:</strong><br>';
        foreach($expld_tst as $key => $rvalue){
            echo $rvalue;
        }
    }
}

$wpcrap = new WebScrap();
//$scrap_list= $wpcrap->scrapList('https://medium.com/top-stories/january-02-2016');
//$scrap_content = $wpcrap->scrapContent('https://medium.com/life-learning/if-you-work-from-home-or-aspire-to-you-must-read-this-8c0e3c0a14b9#.dt62nf1ny');


//
//$user = "bross";
//$first = "Bob";
//$last = "Ross";
//
//$file = "links.txt";
//$json = json_decode(file_get_contents($file), true);
//$json[$user] = array("first" => $first, "last" => $last);
//
//file_put_contents($file, json_encode($json));

//$list_file = 'links.txt';
//if (file_exists($list_file)) {
//    $json = json_decode(file_get_contents($list_file), true);
//    foreach($json as $jkey => $jval){
//        if(!empty($jval)) {
//            $scrap_content = $wpcrap->scrapContent($jval);
//        }
//    }
//}


$scrap_content = $wpcrap->scrapContent('https://medium.com/@andymboyle/what-i-learned-not-drinking-for-two-years-c94167ecd329#.k37fwu8vt');


?>



