<?php

class Team_merge extends Forms
{
    protected $form;

    public function build($team1, $team2 = null)
    {
        $this->teams = $this->CI->_team->findAll('name', null, null, array('name', 'id'));
    
        list($team_1, $team_1_selected) = $this->getAllTeams($team1);
        list($team_2, $team_2_selected) = $this->getAllTeams($team2);
    
        $form = $this->form = $this->CI->form // then we fill the form with elements
            ->open(path('team_merge', array('id' => $team1->getId())), null, 'class="form-horizontal"')
            ->select('team_1', $team_1, 'Merge This Team', $team_1_selected, null, 'class="span6"')
            ->select('team_2', $team_2, '… Into This Team', $team_2_selected, null, 'class="span6"')
            ->html('<div class="form-actions">')
            ->submit('Save Changes', 'submit', 'class="btn btn-primary"')
            //->reset('Cancel', 'clear', 'class="btn"')
            //->button('Test', 'clear', 'button', 'class="btn"')
            ->html('</div>')
            ->model('document', 'validate', 'team/merge')
            ->onsuccess('redirect', path('team_view', array('slug' => $team1->getSlug())));
            
        return $form;
    }
    
    public function validate(&$form, $data)
    {
        if ($this->CI->input->post('password') == 'test') {
            $form->add_error('password', 'You entered a bad password.');
        }
    }
    
    public function save(&$form)
    {
        $team_1 = $this->CI->input->post('team_1');
        $team_1 = $this->CI->_team->findOneById($team_1[0]);
        
        $team_2 = $this->CI->input->post('team_2');
        $team_2 = $this->CI->_team->findOneById($team_2[0]);
        
        $team_2->addAlternateSlug($team_1->getSlug());
        
        if (!$team_2->getNcaaId() && $team_1->getNcaaId()) {
            $team_2->setNcaaId($team_1->getNcaaId());
        }
        
        if (!$team_2->getLahmanId() && $team_1->getLahmanId()) {
            $team_2->setLahmanId($team_1->getLahmanId());
        }
        
        if (!$team_2->getOfficialName() && $team_1->getOfficialName()) {
            $team_2->setOfficialName($team_1->getOfficialName());
        }
        
        if (!$team_2->getCity() && $team_1->getCity()) {
            $team_2->setCity($team_1->getCity());
        }
        
        if (!$team_2->getState() && $team_1->getState()) {
            $team_2->setState($team_1->getState());
        }
        
        if (!$team_2->getNickname() && $team_1->getNickname()) {
            $team_2->setNickname($team_1->getNickname());
        }
        
        if (!$team_2->getConference() && $team_1->getConference()) {
            $team_2->setConference($team_1->getConference());
        }
        
        $team_2->addAlternateSlug($team_1->getSlug());
        
        $this->CI->load->model('GameRepository', '_game');
        $games = $this->CI->_game->findAllWithTeam($team_1);
        
        foreach ($games as $game) {
            if ($game->getHomeTeam() == $team_1) {
                $game->setHomeTeam($team_2);
            }
            if ($game->getAwayTeam() == $team_1) {
                $game->setAwayTeam($team_2);
            }
            
            $game->save();
        }
        
        foreach ($team_1->getSeasons() as $season) {
            $team_2->addSeason($season);
        }
        
        $this->CI->load->model('SeasonRepository', '_season');
        $seasons = $this->CI->_season->findAllWithTeam($team_1);
        
        foreach ($seasons as $season) {
            $season->setTeam($team_2);
            $season->save();
        }
        
        $team_1->remove();
        $team_2->save();
    }
    
    protected function getAllTeams($selected = null)
    {
        $response = array();
        $selected_key = '';
        
        foreach ($this->teams as $key => $team) {
            $response[(string) $team->getId()] = sprintf('%s (%s Games)',$team->getName(), $team->getNumberOfGames());
            if ($selected) {
                if ($team->getId() == $selected->getId()) {
                    $selected_key = $team->getId();
                }
            }
        }
        
        return array($response, $selected_key);
    }
}