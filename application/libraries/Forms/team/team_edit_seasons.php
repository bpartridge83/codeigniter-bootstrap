<?php

class Team_Edit_Seasons extends Forms
{
    protected $form;

    public function build($team)
    {
    
        $form = $this->CI->form // then we fill the form with elements
            ->open(path('team_edit_seasons', array('slug' => $team->getSlug())), null, 'class="form-horizontal"');
        
        foreach ($team->getSeasons() as $season) {
            $form
                ->html(sprintf('<div class="control-group form-inline"><label class="control-label">%s</label>', $season->getYear()))
                
                ->select(
                    sprintf('level[%s]', $season->getYear()), 
                    $this->getLevels(), 
                    null, 
                    $season->getLevel(), 
                    null, 
                    'class="span4"')
                    
                ->select(
                    sprintf('league[%s]', $season->getYear()), 
                    $this->getLeagues(), 
                    null, 
                    $season->getLeague(), 
                    null, 
                    'class="span4"')
                    
                ->select(
                    sprintf('division[%s]',
                    $season->getYear()), 
                    $this->getDivisions(), 
                    null, 
                    $season->getDivision(), 
                    null, 
                    'class="span4"')
                    
                ->select(
                    sprintf('conference[%s]', 
                    $season->getYear()), 
                    $this->getConferences(), 
                    null, 
                    $season->getConference()->getId(), 
                    null, 
                    'class="span4"')
                
                ->text(sprintf('teamRosterUrl[%s]', $season->getYear()), 'Team Roster URL', null, $season->getTeamRosterUrl(), 'class="span8"')
                ->text(sprintf('teamScheduleUrl[%s]', $season->getYear()), 'Team Schedule URL', null, $season->getTeamScheduleUrl(), 'class="span8"')
                ->text(sprintf('teamStatsUrl[%s]', $season->getYear()), 'Team Stats URL', null, $season->getTeamStatsUrl(), 'class="span8"')
                    
                ->text(sprintf('wins[%s]', $season->getYear()), 'Wins', null, $season->getWins(), 'class="span3"')
                ->text(sprintf('losses[%s]', $season->getYear()), 'Losses', null, $season->getLosses(), 'class="span3"')
                
                ->html('</div>');
        }
        
        $form
            ->html('<div class="form-actions">')
            ->submit('Save Changes', 'submit', 'class="btn btn-primary"')
            //->reset('Cancel', 'clear', 'class="btn"')
            //->button('Test', 'clear', 'button', 'class="btn"')
            ->html('</div>')
            ->model('document', 'validate', 'team/edit_seasons')
            ->onsuccess('redirect', path('team_view', array('slug' => $team->getSlug())));

        
        $this->form = $form;
            
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
    
    public function save(&$form, $team)
    {
        $form_seasons = array();
        
        $levels = $this->getLevels();
        $leagues = $this->getLeagues();
        $divisions = $this->getDivisions();
        $conferences = $this->getConferences();
        
        foreach ($this->CI->input->post('level') as $key => $value) {
            $form_seasons[$key]['level'] = $levels[$value[0]];
        }
        
        foreach ($this->CI->input->post('league') as $key => $value) {
            $form_seasons[$key]['league'] = $leagues[$value[0]];
        }
        
        foreach ($this->CI->input->post('division') as $key => $value) {
            $form_seasons[$key]['division'] = $divisions[$value[0]];
        }
        
        foreach ($this->CI->input->post('conference') as $key => $value) {
            $form_seasons[$key]['conference'] = $value[0];
        }
        
        foreach ($this->CI->input->post('teamrosterurl') as $key => $value) {
            $form_seasons[$key]['teamRosterUrl'] = $value;
        }
        
        foreach ($this->CI->input->post('teamscheduleurl') as $key => $value) {
            $form_seasons[$key]['teamScheduleUrl'] = $value;
        }
        
        foreach ($this->CI->input->post('teamstatsurl') as $key => $value) {
            $form_seasons[$key]['teamStatsUrl'] = $value;
        }
        
        foreach ($this->CI->input->post('wins') as $key => $value) {
            $form_seasons[$key]['wins'] = $value;
        }
        
        foreach ($this->CI->input->post('losses') as $key => $value) {
            $form_seasons[$key]['losses'] = $value;
        }
        
        $this->CI->load->model('SeasonRepository', '_season');
        $this->CI->load->model('ConferenceReposiory', '_conference');
        
        foreach ($form_seasons as $key => $form_season) {
            $season = $this->CI->_season->findOneOfficialByTeamAndYear($team, $key);
            
            if ($season) {
                $season->setLevel($form_season['level']);
                $season->setLeague($form_season['league']);
                $season->setDivision($form_season['division']);
            
                $season->setTeamRosterUrl($form_season['teamRosterUrl']);
                $season->setTeamScheduleUrl($form_season['teamScheduleUrl']);
                $season->setTeamStatsUrl($form_season['teamStatsUrl']);
                
                $season->setWins($form_season['wins']);
                $season->setLosses($form_season['losses']);
            
                $conference = $this->CI->_conference->findOneById($form_season['conference']);
            
                $season->setConference($conference);
            
                $season->save();
            } else {
                print_r(sprintf('No season for %s, %s', $team->getName(), $key));
            }
        }
    }
    
    protected function getLevels()
    {
        return array('College');
    }
    
    protected function getLeagues()
    {
        return array('NCAA');
    }
    
    protected function getDivisions()
    {
        return array('D1');
    }
    
    protected function getConferences()
    {
        $this->CI->load->model('ConferenceRepository', '_conference');
        
        $conference_objects = $this->CI->_conference->findAll();
        
        $conferences = array();
        
        foreach ($conference_objects as $conference) {
            $conferences[(string) $conference->getId()] = $conference->getName();
        }
                
        return $conferences;
    }
}