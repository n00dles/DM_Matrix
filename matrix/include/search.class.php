<?php
# the base class for all search result items
  class TheMatrixSearchResultItem {
    private static $defaultLanguage = null;

    private $id;
    private $language;
    private $creDate;
    private $pubDate;
    private $score;
    private $data;
    private $key;

    public function __construct($data, $key, $id, $transkey=array(), $language, $creDate, $pubDate, $score) {
      if (self::$defaultLanguage === null) {
        self::$defaultLanguage = function_exists('return_i18n_default_language') ? return_i18n_default_language() : '';
      }
      $this->id = $id;
      $this->language = $language;
      $this->creDate = $creDate;
      $this->pubDate = $pubDate;
      $this->score = $score;
      $this->data = $data;
      $this->key = $key;
      $this->transkey = $transkey;
    }  
    
    public function __get($name) {
      switch ($name) {
        case 'id': return $this->id;
        case 'fullId': return $this->id . ($this->language && $this->language != self::$defaultLanguage ? '_'.$this->language : '');
        case 'language': return $this->language;
        case 'creDate': return $this->creDate;
        case 'pubDate': return $this->pubDate;
        case 'score': return $this->score;
        default: return $this->get($name);
      }
    }
    
    public function __isset($name) {
      return __get($name) != null;
    }
    
    public function get($name) {
      if (!$this->data) return null;
      switch ($name) {
        case 'title':       return $this->data[$this->transkey['title']];
        case 'description': return $this->data[$this->transkey['description']];
        case 'content':     return $this->data[$this->transkey['content']];
        case 'link':        return null; 
        default:            return $this->data[$name]; 
      }
    }

    public function getExcerpt($content, $excerptlength) {
      return new I18nSearchExcerpt($content, $excerptlength);
    }
  }
?>