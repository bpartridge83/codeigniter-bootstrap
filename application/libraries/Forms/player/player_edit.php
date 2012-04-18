<?php

class Player_Edit extends Forms
{
    protected $form;

    public function build($player)
    {
        $form = $this->form = $this->CI->form // then we fill the form with elements
            ->open(path('player_edit', array('slug' => $player->getSlug())), null, 'class="form-horizontal"')
            ->text('firstname', 'First Name', 'required|max_length[120]', $player->getFirstName(), 'class="span6"')
            ->text('lastName', 'Last Name', 'required|max_length[120]', $player->getLastName(), 'class="span6"')
            ->text('ncaaId', 'NCAA ID', null, $player->getNcaaId(), 'class="span2"')
            ->text('positions', 'Positions', null, $player->getPositions(true), 'class="span5"')
            ->text('throws', 'Throws', null, $player->getThrows(), 'class="span2"')
            ->text('bats', 'Bats', null, $player->getBats(), 'class="span2"')
            ->text('height', 'Height', null, $player->getHeight(), 'class="span2"')
            ->text('weight', 'Weight', null, $player->getWeight(), 'class="span2"')
            ->text('Birthdate', 'Birthdate', null, $player->getBirthdate(), 'class="span6"')
            ->text('Hometown', 'Hometown', null, $player->getHometown(), 'class="span6"')
            ->html('<div class="form-actions">')
            ->submit('Save Changes', 'submit', 'class="btn btn-primary"')
            //->reset('Cancel', 'clear', 'class="btn"')
            //->button('Test', 'clear', 'button', 'class="btn"')
            ->html('</div>')
            ->model('document', 'validate', 'team/edit')
            ->onsuccess('redirect', path('player_view', array('slug'=>$player->getSlug())));
            
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
    
    public function save(&$form, $player)
    {
        $this->fetch('firstName', $player);
        $this->fetch('lastName', $player);
        $this->fetch('ncaaId', $player);
        $this->fetch('hometown', $player);
        $this->fetch('birthdate', $player);
        $this->fetch('height', $player);
        $this->fetch('weight', $player);
        $this->fetch('throws', $player);
        $this->fetch('bats', $player);
        
        if ($positions = $this->CI->input->post('positions')) {
            $player->setPositions($positions);
        }
        
        $player->save();
    }
    
}