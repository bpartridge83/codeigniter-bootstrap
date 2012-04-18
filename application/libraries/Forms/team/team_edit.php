<?php

class Team_Edit extends Forms
{
    protected $form;

    public function build($team)
    {
        list($conferences, $currentConferenceKey) = $this->getConferences($team);
        list($lahman_teams, $currentLahmanId) = $this->getLahmanIds($team);
        list($boydsworld_teams, $currentBoydsworldTeam) = $this->getBoydsworldTeams($team);
        list($ncaa_teams, $currentNcaaId) = $this->getNcaaIds($team);
    
        $form = $this->form = $this->CI->form // then we fill the form with elements
            ->open(path('team_edit', array('slug' => $team->getSlug())), null, 'class="form-horizontal"')
            ->text('name', 'Short Name', 'required|max_length[120]', $team->getName(), 'class="span6"')
            ->text('slug', 'Slug', 'required|min_length[3]|max_length[120]', $team->getSlug(), 'class="span6"')
            ->text('alternateSlugs', 'Alternate Slugs', 'trim|max_length[200]', implode(', ',$team->getAlternateSlugs()), 'class="span6"')
            ->text('officialName', 'Official Name', 'max_length[150]', $team->getOfficialName(), 'class="span6"')
            ->text('shortName', 'Short Name', 'max_length[150]', $team->getShortName(), 'class="span6"')
            ->text('nickname', 'Nickname / Mascot', 'max_length[100]', $team->getNickname(), 'class="span6"')
            ->text('cityState', 'City, State', 'min_length[5]|max_length[150]', $team->getCityState(), 'class="span6"')
            //->select('conference', $conferences, 'Conference', $currentConferenceKey, null, 'class="span6"')
            ->select('ncaaId', $ncaa_teams, 'NCAA ID', $currentNcaaId, null, 'class="span6"')
            ->select('lahmanId', $lahman_teams, 'Lahman ID', $currentLahmanId, null, 'class="span8"')
            ->select('boydsworldId', $boydsworld_teams, 'Boydsworld Team', $currentBoydsworldTeam, null, 'class="span8"')
            //->indent(200)
            //->checkbox('loggedin', 'yes', 'I want to stay logged-in')
            ->html('<div class="form-actions">')
            ->submit('Save Changes', 'submit', 'class="btn btn-primary"')
            //->reset('Cancel', 'clear', 'class="btn"')
            //->button('Test', 'clear', 'button', 'class="btn"')
            ->html('</div>')
            ->model('document', 'validate', 'team/edit')
            ->onsuccess('redirect', path('teams_index'));
            
        return $form;
    }
    
    public function validate(&$form, $data)
    {
        if ($this->CI->input->post('password') == 'test') {
            $form->add_error('password', 'You entered a bad password.');
        }
    }
    
    public function save(&$form, $team)
    {
        $this->fetch('name', $team);
        $this->fetch('slug', $team);
        $this->fetch('nickname', $team);
        $this->fetch('officialName', $team);
        $this->fetch('shortName', $team);
        $this->fetch('cityState', $team);
        $this->fetch('ncaaId', $team);
        
        $this->fetch('boydsworldId', $team);
    
        if ($lahmanId = $this->CI->input->post('lahmanid')) {
            $team->setLahmanId($lahmanId[0]);
            
            $lahmanTeam = $this->getLahmanTeam($lahmanId[0]);
            
            if (!$team->getOfficialName()) {
                $team->setOfficialName($lahmanTeam['name']);
            }
            
            if (!$team->getCity()) {
                $team->setCity($lahmanTeam['city']);
            }
            
            if (!$team->getState()) {
                $team->setState($lahmanTeam['state']);
            }
            
            if (!$team->getNickname()) {
                $team->setNickname($lahmanTeam['nickname']);
            }
        }
        
        if ($alternateSlugs = $this->CI->input->post('alternateslugs')) {
            $alternateSlugs = explode(', ', $alternateSlugs);
            
            foreach ($alternateSlugs as $key => $alternate) {
                $alternateSlugs[$key] = trim($alternate);
            }
            
            $team->setAlternateSlugs($alternateSlugs);
        }
        
        $team->save();
    }
    
    protected function getConferences($team)
    {
        $this->CI->load->model('ConferenceRepository', '_conference');
        
        $conference_objects = $this->CI->_conference->findAll();
        
        $conferences = array(''=>'');
        $selected = '';
        
        foreach ($conference_objects as $conference) {
            $conferences[(string) $conference->getId()] = $conference->getName();
            if ($team->getConference() == $conference) {
                $selected = $conference->getId();
            }
        }
        
        return array($conferences, $selected);
    }
    
    protected function getLahmanIds($team)
    {
        $selected = '';
        $response = array(''=>'');
        
        $file = "./web/utilities/lahman/Schools.csv";
        
        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $response[$data[0]] = sprintf('%s | %s', $data[0], $data[1]);
                    if ($team->getLahmanId() == $data[0]) {
                        $selected = $data[0];
                    }
            }
        }
        
        return array($response, $selected);
    }
    
    protected function getBoydsworldTeams($team)
    {
        include(APPPATH.'../web/utilities/boydsworld-teams.php');
        
        $selected = '';
        $response = array(''=>'');
        
        foreach ($teams as $boydsworld_team) {
            $response[$boydsworld_team] = $boydsworld_team;
            if ($team->getBoydsworldId() == $boydsworld_team) {
                $selected = $boydsworld_team;
            }
        }
        
        return array($response, $selected);
    }
    
    protected function getLahmanTeam($id)
    {        
        $file = "./web/utilities/lahman/Schools.csv";
        
        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if ($id == $data[0]) {
                        return array(
                            "name" => $data[1], "city" => $data[2], "state" => $data[3], "nickname" => $data[4]
                        );
                    }
            }
        }
    }
    
    protected function getNcaaIds($team)
    {
        include(APPPATH.'../web/utilities/ncaa-team-codes.v2.php');
        
        $selected = '';
        $response = array(''=>'');
        
        foreach ($teams as $key => $ncaa_team) {
            $response[(string) $key] = sprintf('%s | %s', $ncaa_team, $key);
            if ((int) $team->getNcaaId() == (int) $key) {
                $selected = $key;
            }
        }
        
        return array($response, $selected);
    }
}