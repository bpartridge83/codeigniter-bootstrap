<?php

class Player_Attach_Ncaa_Ids extends Forms
{
    protected $form;

    public function build($data)
    {
        list($team, $year) = $data;
        
        $redirect = path('player_attach_ncaa_ids', array('slug'=>$team->getSlug()));
        
        $this->setPlayerNcaaIdsForTeam($team, $year);
        
        $form = $this->form = $this->CI->form // then we fill the form with elements
            ->open(path('player_attach_ncaa_ids_for_year', array('slug' => $team->getSlug(), 'year' => $year)), null, 'class="form-horizontal"');
            
        $globalError = false;
            
        foreach ($team->getPlayers($year) as $player) {
        
            $class = '';
        
            if (count($player->getAllNcaaIds())) {
                foreach ($player->getAllNcaaIds() as $ncaaId) {
                    if (in_array($ncaaId, array_splice(array_keys($this->players), 1))) {
                        $class = 'success';
                    }
                }
                
                if (!$class) {
                    $class = 'error';
                    $globalError = true;
                }
            } else {
                $class = '';
                if (!$this->matchNameInArray($player)) {
                    $globalError = true;
                }
            }
        
            $form
                ->html(sprintf('<div class="control-group %s form-inline"><label class="control-label">%s, %s</label>', $class, $player->getLastName(), $player->getFirstName()))
                
                ->select(
                    sprintf('player[%s]', $player->getId()), 
                    $this->players, 
                    null, 
                    $this->matchNameInArray($player), 
                    null, 
                    'class="span8"')
                
                ->html('</div>');
                    
        }

        $form
            ->html('<div class="form-actions">')
            ->submit('Save Changes', 'submit', 'class="btn btn-primary"')
            //->reset('Cancel', 'clear', 'class="btn"')
            //->button('Test', 'clear', 'button', 'class="btn"')
            ->html('</div>')
            ->model('document', 'validate', 'team/edit')
            ->onsuccess('redirect', $redirect);
            
        if (!$globalError) {
            $this->save($form, $data, $redirect);
        }
            
        return $form;
    }
    
    public function validate(&$form, $data)
    {
        /*
        if ($this->CI->input->post('password') == 'test') {
            $form->add_error('password', 'You entered a bad password.');
        }
        */
    }
    
    public function save(&$form, $data, $redirect = false)
    {    
        list($team, $year) = $data;
    
        $this->CI->load->model('PlayerRepository', '_player');
        $this->CI->load->model('SeasonRepository', '_season');
    
        $players = array();
    
        if ($redirect) {
            foreach ($team->getPlayers($year) as $player) {
                $players[(string) $player->getId()] = $this->matchNameInArray($player);
            }
        } else {
            foreach ($this->CI->input->post('player') as $key => $value) {
                $players[$key] = $value[0];
            };
        }
    
        foreach ($players as $key => $value) {
            $player = $this->CI->_player->findOneById($key);
            
            $player->addNcaaId($this->playersArray[$value]['ncaaId']);
            $player->addPosition($this->playersArray[$value]['position']);
            $player->save();
            
            $season = $this->CI->_season->findOneOfficialByPlayerAndYear($player, $year);
            
            $season->setClass($this->playersArray[$value]['class']);
            $season->save();
        }
        
        if ($redirect) {
            redirect($redirect);
        }
    }
    
    protected function matchNameInArray($player)
    {
        if ($player->getNcaaId()) {
            if (in_array($player->getNcaaId(), array_keys($this->players))) {
                return $player->getNcaaId();
            }
        }
    
        foreach ($this->players as $key => $value) {
            if (strpos(strtolower($value), strtolower(sprintf('%s, %s', $player->getLastName(), substr($player->getFirstName(), 0, 1)))) > -1) {
                return $key;
            }
        }
        
        return null;
    }
    
    protected function setPlayerNcaaIdsForTeam($team, $year)
    {
        $this->CI->load->library('Parse/NcaaParse');
        $cookie = $this->CI->ncaaparse->getCookie();
    
        $curl = new CURL();
        $curl->addSession(sprintf('http://web1.ncaa.org/stats/StatsSrv/careerteam?academicYear=%s&coachId=-100&division=1&doWhat=display&idx=&orgId=%s&playerId=-100&sortOn=0&sportCode=MBA', $year, $team->getNcaaId()));
        $curl->setOpt(CURLOPT_COOKIE, $cookie);
        $curl->setOpt(CURLOPT_REFERER, 'http://web1.ncaa.org/stats/StatsSrv/careersearch');
        $page = $curl->exec();
        $curl->clear();
        
        $this->CI->load->library('domparser');
        $html = $this->CI->domparser->str_get_html($page);
        
        $players = array(''=>'');
        $playersArray = array();
        
        foreach ($html->find('table.statstable', 1)->find('tr') as $key => $row) {
            
            if ($key > 2) {
                $id = $row->find('td',0)->find('a',0)->getAttribute('href');
                preg_match('/[0-9]+/', $id, $match);
                $id = $match[0];
            
                $player = sprintf('%s (%s / %s)', trim($row->find('td',0)->plaintext), trim($row->find('td',1)->plaintext), trim($row->find('td',3)->plaintext));
                    
                $name = explode(',',trim($row->find('td',0)->plaintext));
                
                $playerArray = array(
                    'ncaaId' => $id,
                    'firstName' => trim($name[1]),
                    'lastName' => trim($name[0]),
                    'class' => trim($row->find('td',1)->plaintext),
                    'position' => trim($row->find('td',3)->plaintext)
                );
            
                $players[$id] = $player;
                $playersArray[$id] = $playerArray;
            }
        }
        
        $this->players = $players;
        $this->playersArray = $playersArray;
        
        return $players;
    }
    
}