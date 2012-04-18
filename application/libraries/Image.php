<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Image
{

    public function get()
    {
        return print_r(file_get_contents('http://grfx.cstv.com/photos/schools/xavi/sports/m-basebl/auto_headshot/4093026.jpeg'));
    }

    public function findPlayer($player)
    {
        $results = array();
    
        print_r($this->getSearchUrl($player));
    
        $curl = new CURL();
        $curl->addSession($this->getSearchUrl($player));
        $curl->setOpt(CURLOPT_RETURNTRANSFER, 1);
        $curl->setOpt(CURLOPT_REFERER, 'http://lh.beta.smallballstats.info');
        $page = $curl->exec();
        $curl->clear();
        
        $page = json_decode($page);
        
        foreach ($page->responseData->results as $result) {
            array_push($results, array(
                'url' => $result->unescapedUrl,
                'title' => $result->titleNoFormatting,
                'content' => $result->contentNoFormatting
            ));
        }
        
        sleep(1);
        
        $curl = new CURL();
        $curl->addSession($this->getSearchUrl($player, 4));
        $curl->setOpt(CURLOPT_RETURNTRANSFER, 1);
        $curl->setOpt(CURLOPT_REFERER, 'http://lh.beta.smallballstats.info');
        $page = $curl->exec();
        $curl->clear();
        
        $page = json_decode($page);
        
        foreach ($page->responseData->results as $result) {
            array_push($results, array(
                'url' => $result->unescapedUrl,
                'title' => $result->titleNoFormatting,
                'content' => $result->contentNoFormatting
            ));
        }
        
        print_r($results);
        
        die();
    }

    
    protected function getSearchUrl($player, $start = 0)
    {
        return sprintf('https://ajax.googleapis.com/ajax/services/search/images?v=1.0&q=%s&start=%s&userip=%s', urlencode(sprintf('%s baseball roster %s', $player->getTeams(true), $player->getName())), $start, '69.181.67.62');
    }

}