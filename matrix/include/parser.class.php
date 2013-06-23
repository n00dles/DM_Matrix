<?php
  /*
   * Matrix Parser Class (used for parsing the various field types for output)
   *
   */
   
class TheMatrixParser {
  /*
   * parse bbcode markup (e.g. [b], [i], [u]...)
   */
  public function bbcode($content) {
    if (is_string($content)) {
      // code replacements edited from 'http://thesinkfiles.hubpages.com/hub/Regex-for-BBCode-in-PHP'
      
      // encoding special characters
      $content = htmlentities($content);
      
      // bold
      $content = preg_replace('#\[b\](.+)\[\/b\]#iUs', '<strong>$1</strong>', $content);

      // italic
      $content = preg_replace('#\[i\](.+)\[\/i\]#iUs', '<span style="font-style: italic;">$1</span>', $content);

      // underline
      $content = preg_replace('#\[u\](.+)\[\/u\]#iUs', '<span style="text-decoration: underline;">$1</span>', $content);

      // links
      $content = preg_replace('#\[url\=(.+)\](.+)\[\/url\]#iUs', '<a href="$1">$2</a>', $content);

      // images
      $content = preg_replace('#\[img\](.+)\[\/img\]#iUs', '<img src="$1" alt="Image" />', $content); 

      // lists
      $content = preg_replace('#\[list\]#iUs', '<ul>', $content);
      $content = preg_replace('#\[list\=(.+)\](.+)\[\/list\]#iUs', '<ol start="$1">$2</ol>', $content);
      $content = preg_replace('#\[/list]#iUs', '</ul>', $content);
      $content = preg_replace('#\[\*\]#iUs', '<li>', $content);
      $content = preg_replace('#\[/\*]#iUs', '</li>', $content);

      // headings
      $content = preg_replace('#\[heading\=(.+)\](.+)\[\/heading\]#iUs', '<h$1>$2</h$1>', $content); 

      // span & color
      $content = preg_replace('#\[span\](.+)\[\/span\]#iUs', '<span>$1</span>', $content); 
      $content = preg_replace('#\[span\=(.+)\](.+)\[\/span\]#iUs', '<span class="$1">$2</span>', $content); 
      $content = preg_replace('#\[color\=(.+)\](.+)\[\/color\]#iUs', '<span style="color: $1">$2</span>', $content); 

      // quotes
      $content = preg_replace('#\[quote\=(.+)\]#iUs', '<blockquote class="quote"><strong>$1 said:</strong><br />', $content);
      $content = preg_replace('#\[/quote]#iUs', '</blockquote>', $content);

      // font size
      $content = preg_replace('#\[size\=(.+)\](.+)\[\/size\]#iUs', '<span style="font-size:$1%;">$2</span>', $content); 

      // codes
      $content = preg_replace('#\[code\](.+)\[\/code\]#iUs', '<div class="code"><span><strong>Code</strong>:</span><pre><code>$1</code></pre></div>', $content); 

      // returns
      $content = preg_replace('#(\s*\n)+#iUs', '<br />', $content);
      return $content;
    }
    else return false;
  }
  
  /*
   * parse wiki markup
   */
  public function wiki($content) {
    if (is_string($content)) {
      // encoding special characters
      $content = htmlentities($content);
      
      // headings
      $content = preg_replace('/======(.*?)======/',   '<h5>$1</h5>', $content); // h5
      $content = preg_replace('/=====(.*?)=====/',     '<h4>$1</h4>', $content); // h4
      $content = preg_replace('/====(.*?)====/',       '<h3>$1</h3>', $content); // h3
      $content = preg_replace('/===(.*?)===/',         '<h2>$1</h2>', $content); // h2
      $content = preg_replace('/==(.*?)==/',           '<h1>$1</h1>', $content); // h1
      
      // bold/italic
      $content = preg_replace('/\'\'\'(.*?)\'\'\'/',   '<b>$1</b>', $content); // bold
      $content = preg_replace('/\'\'(.*?)\'\'/',       '<i>$1</i>', $content); // italic
      
      // bullets
      $content = preg_replace('/\* (.*?)/',            '<li>$1</li>', $content); // italic
      #$content = preg_replace('/\[(.*?) (.*?)\]/',     '<a href="$1" title="$2">$2</a>', $content); // link
      
      // image/links
      $content = preg_replace('/\[\[Image:(.*?)\]\]/', '<img src="$1" alt=""/>', $content); // image
      $content = preg_replace('/\[(.*?) (.*?)\]/',     '<a href="$1" title="$2">$2</a>', $content); // link
      
      // quote
      #$content = preg_replace('/> ()/', '<a href="$1" title="$2">$2</a>', $content); // image
      
      return $content;
    }
    else return false;
  }
  
  /*
   * parse markdown markup
   */
  public function markdown($content) {
     if (is_string($content)) {
      // encoding special characters
      $content = htmlentities($content);
      
      $content = preg_replace('/(.*?)\r\n==================/',            '<h1>$1</h1>', $content); // h1
      $content = preg_replace('/######(.*?)\r\n/',           '<h6>$1</h6>', $content); // h6
      $content = preg_replace('/#####(.*?)\r\n/',            '<h5>$1</h5>', $content); // h5

      $content = preg_replace('/\*\*(.*?)\*\*/',            '<b>$1</b>', $content); // bold
      $content = preg_replace('/_(.*?)_/',            '<i>$1</i>', $content); // italic
      
      return $content;
    }
    else return false;
  }

  // creates an excerpt
  public function getExcerpt($data, $length=100) {
      
  }
  
  /*
   *
   */
  public function returnTags($string, $url='') {
    if (is_string($string)) {
      $tags = array();
      $tagsRaw = explode(',', $string);
      $tagsRaw = array_map('trim', $tagsRaw);
      foreach ($tagsRaw as $tag) {
        $tags[$tag] = str_replace('$tag', $tag, $url);
      }
      return $tags;
    }
    else return false;
  }
    
  /*
   *
   */
  public function getTags($string, $separator=', ', $url='') {
    $tags = $this->returnTags($string, $url);
    $end = array_slice($tags, -1, 1);
    $output = '';
    foreach ($tags as $tag=>$link) {
      $output .= '<a class="tag" href="'.$link.'">'.$tag.'</a>';
      if ($tag!=key($end)) $output .= $separator;
    }
    return $output;
  }
}

?>