<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Slugify
{

    public function create($obj = null, $field = 'name')
    {
        if (!$obj) {
            return false;
        }
        
        $string = call_user_func(array($obj, 'get'.ucwords($field)));
        
        $slug = $this->processString($string);
        
        // Trim it if it's greater than 32 characters (minus 3 for suffix)
        if (mb_strlen($slug) > (32 - 3)) {
            $slug = mb_substr($slug, 0, (32 - 3));
        }
 
        $increment = 0;
        
        while (true)
        {
            $slug2 = $increment ? $slug . '-' . $increment : $slug;
            $slugExists = $this->check($obj, $slug2);
            if ($slugExists) {
                $increment++;
            } else {
                return $slug2;
            }
        }
        
        return null;
    }
    
    public function check($obj = null, $slug, $class = null)
    {
        if ($class) {
            $obj = new $class;
        }
        
        return ($obj->getRepository()->findOneBySlug($slug)) ? true : false;
    }
    
    public function simple($string)
    {
        $string = utf8_encode($string);
        
        return $this->processString($string);
    }
    
    protected function processString($text)
    {
        $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $text); // transliterate
        $slug = mb_strtolower($slug); // lowercase
        $slug = str_replace(array('\'', '`', '^'), '', $slug); // remove accents resulting from OSX's iconv
        $slug = preg_replace('/\W+/', '-', $slug); // replace non letter or digits with separator
        $slug = trim($slug, '-'); // trim
        $slug = $slug ? $slug : 'n-a';
        
        return $slug;
    }
    
}