<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Elastic extends MY_Controller {

    protected $indexes = array();

    public function __construct() {
        parent::__construct();
        
        $this->indexes = array(
            'team',
            'conference',
            'player'
        );
    }
    
    public function ensure($index = null)
    {
        if ($index) {
            return $this->{'ensure'.ucfirst($index)}();
        }
    
        foreach ($this->indexes as $index) {
            $this->{'ensure'.ucfirst($index)}();
        }
    }
    
    public function ensureTeam()
    {
        $this->load->model('teamRepository', '_team');
        
        $documents = $this->_team->findAll();
        
        $this->drop('team');
        
        foreach ($documents as $document) {
        
            $team = array(
                'slug' => $document->getSlug(),
                'name' => $document->getName(),
            );
            
            if ($document->getAlternateSlugs()) {
                $team['alternateSlug'] = $document->getAlternateSlugs();
            }
            
            if ($document->getNickname()) {
                $team['nickname'] = $document->getNickname();
            }
            
            if ($document->getOfficialName()) {
                $team['officialName'] = $document->getOfficialName();
            }
                
            if ($document->getConference()) {
                $team['conference'] = $document->getConference()->getName();
            }
        
            $result = $this->elasticsearch->add('team', $document->getId(), json_encode($team));
            
            print_r($result);
            print_r("\n\n");
        }
        
        return true;
    }
        
    public function drop($index)
    {
        return $this->elasticsearch->drop($index);
    }
    
}