<?php
/**
* @package SwiftNinjaProSmartSearch
*/

if(!defined('ABSPATH')){
  echo '<meta http-equiv="refresh" content="0; url=/404">';
  die('404 Page Not Found');
}

if(!class_exists('SwiftNinjaProSmartSearchMain')){

  class SwiftNinjaProSmartSearchMain{
    
    public $pluginSettingsName;
    public $pluginShortcode;
    
    function start($pluginSettingsName, $pluginShortcode){
      $this->pluginSettingsName = $pluginSettingsName;
      $this->pluginShortcode = $pluginShortcode;
      if($pluginShortcode){add_shortcode($pluginShortcode, array($this, 'add_plugin_shortcode'));}
      add_action('after_setup_theme', array($this, 'checkSmartSearchStart'));
    }
    
    function checkSmartSearchStart(){
      add_action('wp_enqueue_scripts', array($this, 'enqueue_404_check'));
    }
    
    function enqueue_404_check(){
      $permalink = wp_kses_post(strip_tags(rtrim(strtok(htmlentities($_SERVER['REQUEST_URI']), '?'), '/')));
      if(is_404() && strpos($permalink, '404') == false){
        $this->startSmartSearch($permalink);
      }
    }
    
    function startSmartSearch($permalink){
      $pages = wp_list_pages(array('echo' => false, 'sort_column' => 'post_parent,menu_order,post_name,comment_count,ID'));
      $pageList = array();
      $pageUrlList = array();
      if(strpos($pages, '</a>') !== false){
        for($i = 1; $i < substr_count($pages, '</a>'); $i++){
          $pageList[$i-1] = $this->urlSimpleName(substr(get_string_between(get_string_between($pages, '<a', '</a>', $i), 'href="', '"'), strlen(get_home_url())));
          $pageUrlList[$i-1] = get_string_between(get_string_between($pages, '<a', '</a>', $i), 'href="', '"');
        }
        $url = $this->urlSimpleName($permalink);
        
        $searchVersion = 'v3';
        $searchVersionOption = $this->settings_getOption('SearchVersion');
        if($searchVersionOption && $searchVersionOption !== ''){$searchVersion = $searchVersionOption;}
        
        if($searchVersion == 'v1'){
          $this->searchV1($url, $pageList, $pageUrlList);
        }else if($searchVersion == 'v3'){
          $this->searchV3($url, $pageList, $pageUrlList);
        }else{
          $this->searchV3($url, $pageList, $pageUrlList);
        }
        
        $ifEmpty404 = $this->settings_getOption('IfEmpty404', true);
        if($ifEmpty404){
          $this->gotoPage('/404');
        }
        
      }
    }
    
    
    function searchV1($url, $pageList, $pageUrlList){
      $urlList = explode('/', $url);
      array_shift($urlList);
      
      for($p = 0; $p < count($pageList); $p++){
        for($u = 0; $u < count($urlList); $u++){
          if(strtolower($pageList[$p]) == strtolower($urlList[$u])){
            $this->gotoPage($pageUrlList[$p]);
          }else if(strtolower($this->get_translated_text($pageList[$p])) == strtolower($this->get_translated_text($urlList[$u]))){
            $this->gotoPage($pageUrlList[$p]);
          }
        }
      }
          
      for($p = 0; $p < count($pageList); $p++){
        $pList = explode('/', $pageList[$p]);
        for($p2 = count($pList)-1; $p2 >= 0; $p2--){
          for($u = 0; $u < count($urlList); $u++){
            if(strtolower($pList[$p2]) == strtolower($urlList[$u])){
              $this->gotoPage($pageUrlList[$p]);
            }else if(metaphone(strtolower($pList[$p2])) == metaphone(strtolower($urlList[$u]))){
              $this->gotoPage($pageUrlList[$p]);
            }else if(metaphone($pList[$p2]) == metaphone($urlList[$u])){
              $this->gotoPage($pageUrlList[$p]);
            }else if(metaphone(strtolower($this->get_translated_text($pList[$p2]))) == metaphone(strtolower($this->get_translated_text($urlList[$u])))){
              $this->gotoPage($pageUrlList[$p]);
            }
          }
        }
      }
          
      $similarTextRequire = 5;
      if($this->settings_getOption('SimilarTextRequire')){
        $similarTextRequire = $this->settings_getOption('SimilarTextRequire');
      }
          
      for($p = 0; $p < count($pageList); $p++){
        $pList = explode('/', $pageList[$p]);
        for($p2 = count($pList)-1; $p2 >= 0; $p2--){
          for($u = 0; $u < count($urlList); $u++){
            $newPageList = explode(' ', $pList[$p2]);
            $newUrlList = explode(' ', $urlList[$u]);
            for($urlP = 0; $urlP < count($newPageList); $urlP++){
              for($urlM = 0; $urlM < count($newUrlList); $urlM++){
                $pUrl = strtolower($newPageList[$urlP]);
                $mUrl = strtolower($newUrlList[$urlM]);
                if($mUrl == $pUrl){
                  $this->gotoPage($pageUrlList[$p]);
                }else if(metaphone($mUrl) == metaphone($pUrl)){
                  $this->gotoPage($pageUrlList[$p]);
                }else if(similar_text($mUrl, $pUrl) >= $similarTextRequire){
                  $this->gotoPage($pageUrlList[$p]);
                }else if(similar_text($this->get_translated_text($mUrl), $this->get_translated_text($pUrl)) >= $similarTextRequire){
                  $this->gotoPage($pageUrlList[$p]);
                }
              }
            }
          }
        }
      }
          
      for($p = 0; $p < count($pageList); $p++){
        $pList = explode('/', $pageList[$p]);
        for($p2 = count($pList)-1; $p2 >= 0; $p2--){
          for($u = 0; $u < count($urlList); $u++){
            $pUrl = $pList[$p2];
            if(preg_match_all('/\b(\w)/',strtoupper($pList[$p2]),$m)){
              $pUrl = implode('',$m[1]);
            }
            $mUrl = $urlList[$u];
            if(preg_match_all('/\b(\w)/',strtoupper($urlList[$u]),$m)){
              $mUrl = implode('',$m[1]);
            }
            
            if(strtolower($pUrl) == strtolower($mUrl)){
              $this->gotoPage($pageUrlList[$p]);
            }else if(strtolower($pUrl) == strtolower($urlList[$u])){
              $this->gotoPage($pageUrlList[$p]);
            }
          }
        }
      }
          
      for($p = 0; $p < count($pageList); $p++){
        $pList = explode('/', $pageList[$p]);
        for($p2 = count($pList)-1; $p2 >= 0; $p2--){
          for($u = 0; $u < count($urlList); $u++){
            if(strpos(strtolower($pList[$p2]), strtolower($urlList[$u])) !== false){
              $this->gotoPage($pageUrlList[$p]);
            }else if(strpos(metaphone(strtolower($pList[$p2])), metaphone(strtolower($urlList[$u]))) !== false){
              $this->gotoPage($pageUrlList[$p]);
            }else if(strpos(metaphone($pList[$p2]), metaphone($urlList[$u])) !== false){
              $this->gotoPage($pageUrlList[$p]);
            }else if(strpos(metaphone(strtolower($this->get_translated_text($pList[$p2]))), metaphone(strtolower($this->get_translated_text($urlList[$u])))) !== false){
              $this->gotoPage($pageUrlList[$p]);
            }
          }
        }
      }
    }
    
    function searchV3($word, $list, $urlList){
      if(!$word || !$list){return false;}
      if(in_array($word, $list)){return $word;}
      $word = str_replace('\\', '/', $word);
      $wordS = $this->urlSimpleName($word);
      $wordS = str_replace('  ', ' ', $wordS);
      $wordEnd = explode('/', $wordS);
      $wordEnd = $wordEnd[count($wordEnd)-1];
      $results = array();
      foreach($list as $i=>$item){
        $item = str_replace('\\', '/', $item);
        $itemS = $this->urlSimpleName($item);
        $itemS = str_replace('  ', ' ', $itemS);
        if(in_array($wordEnd, explode('/', $itemS)) !== false){
          array_push($results, array('item' => $urlList[$i], 'chance' => 5));
        }else if($wordS === $itemS){
          array_push($results, array('item' => $urlList[$i], 'chance' => 4));
        }else if(preg_replace('/([^A-Za-z0-9 \/])/', '', $wordS) === preg_replace('/([^A-Za-z0-9 \/])/', '', $itemS)){
          array_push($results, array('item' => $urlList[$i], 'chance' => 3));
        }else if(preg_replace('/([^A-Za-z \/])/', '', $wordS) === preg_replace('/([^A-Za-z \/])/', '', $itemS)){
          array_push($results, array('item' => $urlList[$i], 'chance' => 2));
        }else{
          $matches = 0;
          $indexDistance = 1;
          $wordSL = preg_replace('/([^A-Za-z0-9 \/])/', '', $wordS);
          $itemSL = preg_replace('/([^A-Za-z0-9 \/])/', '', $itemS);
          $wordSL = str_split($wordSL); $itemSL = str_split($itemSL);
          foreach($wordSL as $wi=>$w){
            foreach($itemSL as $li=>$l){
              if($w === $l && !preg_match('/[0-9]/', $w) && !preg_match('/[0-9]/', $l)){
                $matches += 5;
                if($wi === 0){$matches += 5;}
                if($li === 0){$matches += 3;}
                $indexDistance += abs($wi-$li);
              }else if(preg_match('/[0-9]/', $w) && preg_match('/[0-9]/', $l) && $wi === $li){
                $matches += 10;
              }else if($w === $l){
                $matches += 3;
                if($wi === 0){$matches += 3;}
                if($li === 0){$matches += 1;}
                $indexDistance += abs($wi-$li);
              }
            }
          }
          $matchPercent = (($matches-(count($wordSL)+count($itemSL))/2)*10)-$indexDistance*2;
          if($matchPercent >= 0){
            array_push($results, array('item' => $urlList[$i], 'chance' => 1, 'percent' => $matchPercent));
          }
        }
      }
      $finalResult = array('chance' => 0);
      foreach($results as $result){
        if($result['chance'] > $finalResult['chance'] || ($result['chance'] == $finalResult['chance'] && $result['percent'] && $finalResult['percent'] && $result['percent'] > $finalResult['percent'])){
          $finalResult = $result;
        }
      }
      if($finalResult['chance'] > 0 && $finalResult['item']){
        $this->gotoPage(htmlentities(strip_tags($finalResult['item'])));
      }
    }
    
    
    function gotoPage($url){
      echo '<script>window.location.replace("'.$url.'");</script>';
      exit();
    }
    
    function urlSimpleName($url){
      $urlSimple = str_replace('+', ' ', $url);
      $urlSimple = str_replace('-', ' ', $urlSimple);
      $urlSimple = str_replace('_', ' ', $urlSimple);
      $urlSimple = str_replace('%20', ' ', $urlSimple);
      $urlSimple = preg_replace('/([A-Z])/', ' $1', $urlSimple);
      $urlSimple = str_replace('  ', ' ', $urlSimple);
      return $urlSimple;
    }
    
    function get_translated_text($text, $the_locale = 'en_US'){
      global $locale;
      $old_locale = $locale;
      $locale = $the_locale;
      $translated = __($text);
      $locale = $old_locale;
      return $translated;
    }
    
    
    function add_plugin_shortcode($atts = ''){
      $value = shortcode_atts(array('parameter' => false,), $atts);
      $parameter = wp_kses_post(htmlentities($value["parameter"]));
    
      return false;
    }
    
    
    function trueText($text){
      if($text === 'true' || $text === 'TRUE' || $text === 'True' || $text === true || $text === 1 || $text === 'on'){
        return true;
      }else{return false;}
    }
    
    function settings_getOption($name, $trueText = false){
      $sName = $this->settings_setOptionName($name);
      $option = wp_kses_post(htmlentities(get_option($sName)));
      if($trueText){$option = $this->trueText($option);}
      return $option;
    }

    function settings_setOptionName($name){
      return wp_kses_post(htmlentities('SwiftNinjaPro'.$this->pluginSettingsName.'_'.$name));
    }
    
    function htmlentitiesURL($url, $addQuots){
      $link = htmlspecialchars_decode($url);
      $link = str_replace('&quot;', '[ninja_quot]', $link);
      $link = str_replace('/', '[ninja_slash]', $link);
      $link = wp_kses_post(htmlentities($link));
      $link = str_replace('[ninja_slash]', '/', $link);
      if($addQuots){
        $link = str_replace('[ninja_quot]', '"', $link);
      }else{
        $link = str_replace('[ninja_quot]', '', $link);
      }
      
      return $link;
    }
    
    function get_string_between($string, $start, $end, $pos = 1){
      $cPos = 0;
      $ini = 0;
      $result = '';
      for($i = 0; $i < $pos; $i++){
        $ini = strpos($string, $start, $cPos);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        $result = substr($string, $ini, $len);
        $cPos = $ini + $len;
      }
      return $result;
    }
    
  }

  $swiftNinjaProSmartSearchMain = new SwiftNinjaProSmartSearchMain();

}
