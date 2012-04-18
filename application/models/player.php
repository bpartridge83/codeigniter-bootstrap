<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Player extends Document implements Sluggable, Seasonable {

    protected $slug;
    protected $alternateSlugs = array();
    protected $firstName;
    protected $lastName;
    protected $ncaaId;
    protected $alternateNcaaIds = array();
    protected $seasons = array();
    protected $positions = array();
    protected $throws;
    protected $bats;
    protected $height;
    protected $weight;
    protected $hometown;
    protected $birthdate;
    protected $cstvId;

    public function __construct()
    {
        parent::__construct();
    }
    
    public function __toString()
    {
        return $this->getName();
    }
    
    public function getSlug()
    {
        return $this->slug;
    }
    
    public function setSlug($slug = null, $auto = true)
    {
        if (!$slug && $auto) {
            $slug = $this->slugify->create($this);
        }
        
        if ($slug) {
            $this->slug = $slug;
        }
    }
    
    public function getAlternateSlugs()
    {
        return $this->alternateSlugs;
    }
    
    public function addAlternateSlug($slug)
    {
        if ($this->hasAlternateSlug($slug)) {
            return true;
        }
        
        $this->alternateSlugs[] = $ncaaId;
        
        return $this->getAlternateSlugs();
    }
    
    public function removeAlternateSlug($slug)
    {
        foreach ($this->alternateSlugs as $key => $alternateSlug) {
            if ($alternateSlug == $slug) {
                unset($this->alternateSlugs[$key]);
                return true;
            }
        }
        
        return null;
    }
    
    public function hasAlternateSlug($slug)
    {
        if (in_array($slug, $this->alternateSlugs)) {
            return true;
        }
        
        return false;
    }
    
    /*
     * Returns name in default FirstName, LastName format
     */
    public function getName()
    {
        return sprintf('%s %s', $this->getFirstName(), $this->getLastName());
    }
    
    /*
     * Returns name in LastName, FirstName format
     */
    public function getTableName()
    {
        if ($this->getFirstName()) {
            return sprintf('%s, %s', $this->getLastName(), substr($this->getFirstName(), 0, 1));
        }
        
        return $this->getLastName();
    }
    
    public function getFirstName()
    {
        return $this->firstName;
    }
    
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }
    
    public function getLastName()
    {
        return $this->lastName;
    }
    
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }
    
    public function getThrows()
    {
        return $this->throws;
    }
    
    public function setThrows($throws)
    {
        $this->throws = $throws;
    }
    
    public function getBats()
    {
        return $this->bats;
    }
    
    public function setBats($bats)
    {
        $this->bats = $bats;
    }
    
    public function getSides()
    {
        $sides = array();
        
        if ($this->getThrows()) {
            $sides[] = $this->getThrows();
        }
        
        if ($this->getBats()) {
            $sides[] = $this->getBats();
        }
        
        if (count($sides) > 1) {
            return sprintf('%s <small>/</small> %s', $sides[0], $sides[1]);
        } elseif (count($sides)) {
            return $sides[0];
        }
        
        return null;
    }
    
    public function getHeight()
    {
        return $this->height;
    }
    
    public function setHeight($height)
    {
        // inches
        $this->height = (int) $height;
    }
    
    public function getWeight()
    {
        return $this->weight;
    }
    
    public function setWeight($weight)
    {
        // lbs
        $this->weight = (int) $weight;
    }
    
    public function getHeightWeight()
    {
        $heightWeight = array();
        
        if ($this->getHeight()) {
            $sides[] = $this->getHeight();
        }
        
        if ($this->getWeight()) {
            $sides[] = $this->getWeight();
        }
        
        if (count($heightWeight) > 1) {
            return sprintf('%s <small>/</small> %s', $heightWeight[0], $heightWeight[1]);
        } elseif (count($heightWeight)) {
            return $heightWeight[0];
        }
        
        return null;
    }
    
    public function getHometown()
    {
        return $this->hometown;
    }
    
    public function setHometown($hometown)
    {
        $this->hometown = $hometown;
    }
    
    public function getBirthdate()
    {
        if (!is_null($this->birthdate)) {
            return date('M j, Y', $this->birthdate);
        }
        
        return null;
    }
    
    public function setBirthdate($birthdate)
    {
        $this->birthdate = strtotime($birthdate);
    }
    
    public function getCstvId()
    {
        return $this->cstvId;
    }

    public function setCstvId($cstvId)
    {
        $this->cstvId = (int) $cstvId;
    }
    
    public function getNcaaId()
    {
        return $this->ncaaId;
    }
    
    public function setNcaaId($ncaaId)
    {
        $this->ncaaId = (int) $ncaaId;
    }
    
    public function addNcaaId($ncaaId)
    {
        if (!$this->getNcaaId()) {
            return $this->setNcaaId($ncaaId);
        }
        
        if ($this->getNcaaId() != $ncaaId) {
            return $this->addAlternateNcaaId($ncaaId);
        }
        
        return null;
    }
    
    public function getAllNcaaIds()
    {
        return array_filter(array_merge(array($this->getNcaaId()), $this->getAlternateNcaaIds()));
    }
    
    public function getAlternateNcaaIds()
    {
        return $this->alternateNcaaIds;
    }
    
    public function addAlternateNcaaId($ncaaId)
    {
        if ($this->hasAlternateNcaaId($ncaaId)) {
            return true;
        }
        
        $this->alternateNcaaIds[] = (int) $ncaaId;
        
        return $this->getAlternateNcaaIds();
    }
    
    public function removeAlternateNcaaId($ncaaId)
    {
        foreach ($this->alternateNcaaIds as $key => $alternateNcaaId) {
            if ($alternateNcaaId == $ncaaId) {
                unset($this->alternateNcaaIds[$key]);
                return true;
            }
        }
        
        return null;
    }
    
    public function hasAlternateNcaaId($ncaaId)
    {
        if (in_array($ncaaId, $this->alternateNcaaIds)) {
            return true;
        }
        
        return false;
    }
        
    public function hasSeasons()
    {
        return ($this->seasons) ? true : false;
    }
    
    public function hasSeason($year)
    {
        foreach ($this->seasons as $key => $temp) {
            if ($temp['year'] == $year) {
                return true;
            }
        }
        
        return false;
    }
    
    public function getSeasons()
    {
        if ($this->seasons) {
            $temp = array();
            $this->load->model('seasonRepository', '_season');
            
            foreach ($this->seasons as $season) {
                $season = $this->_season->assign($season);
                $temp[] = $season;
            }
            
            usort($temp, array('Season', 'sortSeasons'));
            
            return $temp;
        }
        
        return array();
    }
    
    public function getSeason($year)
    {
        foreach ($this->seasons as $key => $season) {
            if ($season['year'] == $year) {
                $this->load->model('seasonRepository', '_season');
                return $this->_season->assign($season);
            }
        }
        
        return null;
    }
    
    public function addSeason($season, $overwrite = false)
    {
        if ($overwrite) {
            $this->removeSeason($season->getYear());
        } elseif ($this->hasSeason($season->getYear())) {
            return $this->seasons;
        }
        
        $season->unsetPlayer();
        $season->unsetSource();
        
        $season = $season->toArray();
        $this->seasons = array_values($this->seasons);
        array_push($this->seasons, $season);
        
        return $this->seasons;
    }
    
    public function removeSeason($year)
    {
        foreach ($this->seasons as $key => $temp) {
            if ($temp['year'] == $year) {
                unset($this->seasons[$key]);
            }
        }
        
        return true;
    }
    
    public function getSchools()
    {
        $schools = array();
        
        foreach ($this->getSeasons() as $season) {
            if ($season->getLevel() == 'College' && !array_key_exists((string) $season->getTeam()->getId(), $schools)) {
                $schools[(string) $season->getTeam()->getId()] = $season->getTeam();
            }
        }
        
        return $schools;
    }
    
    public function hasTeam($team)
    {
        // TODO
    }
    
    public function getTeams($string = false)
    {
        $teams = array();
    
        foreach ($this->getSeasons() as $season) {
            if (!in_array($season->getTeam(), $teams)) {
                $teams[] = $season->getTeam();
            }
        }
        
        if ($string) {
            $response = '';
            foreach ($teams as $team) {
                $response .= $team->getName();
            }
            
            return substr($response, 0, (strlen($response) - 2));
        }
        
        return $teams;
    }
    
    public function hasPosition($position)
    {
        return (bool) in_array($position, $this->positions);
    }
    
    public function getPositions($string = false)
    {
        if ($string) {
            $positions = '';
            foreach ($this->positions as $position) {
                $positions .= sprintf('%s, ', $position);
            }
            
            return substr($positions, 0, (strlen($positions) - 2));
        }
    
        return $this->positions;
    }
    
    public function setPositions($positions)
    {
        if (!$positions) {
            return false;
        }
        
        if (!is_array($positions)) {
            $positions = explode(',', $positions);
            foreach ($positions as $key => $value) {
                $positions[$key] = trim($value);
            }
        }
    
        $this->positions = array();
    
        foreach ($positions as $position) {
            $position = $this->cleanPosition($position);
            if (in_array($position, array_keys($this->getAvailablePositions()))) {
                $this->addPosition($position);
            }
        }
        
        return $this;
    }
    
    protected function cleanPosition($position)
    {
        if (!array_key_exists($position, $this->getAvailablePositions())) {
            foreach ($this->getAvailablePositions() as $key => $value) {
                if ($value == $position) {
                    $position = $key;
                }
            }
        }
        
        return $position;
    }
    
    public function addPosition($position)
    {
        $position = $this->cleanPosition($position);
        
        if (!array_key_exists($position, $this->getAvailablePositions())) {
            return false;
        }
        
        if (!$this->hasPosition($position)) {
            array_push($this->positions, $position);
        }
        
        return $this->getPositions();
    }
    
    public function getEarnedRunAverage()
    {
        $this->load->library('Stats/EarnedRunAverage');
        
        return $this->earnedrunaverage->getPlayer($this);
    }
    
    public function getEra()
    {
        return $this->getEarnedRunAverage();
    }
    
    public function getWalksPlusHitsPerInningsPitched()
    {
        $this->load->library('Stats/WHIP');
        
        return $this->whip->getPlayer($this);
    }
    
    public function getWHIP()
    {
        return $this->getWalksPlusHitsPerInningsPitched();
    }
    
    public function getInningsPitched()
    {
        $this->load->library('Stats/InningsPitched');
        
        return $this->inningspitched->getPlayer($this);
    }
    
    public function getBattingAverage()
    {
        $this->load->library('Stats/BattingAverage');
        
        return $this->battingaverage->getPlayer($this);
    }
    
    public function getAvg()
    {
        return $this->getBattingAverage();
    }
    
    public function getSluggingPercentage()
    {
        $this->load->library('Stats/SluggingPercentage');
        
        return $this->sluggingpercentage->getPlayer($this);
    }
    
    public function getIsolatedPower($start = null, $finish = null)
    {
        $this->load->library('Stats/IsolatedPower');
        
        return $this->isolatedpower->getPlayer($this, $start, $finish);
    }
    
    public function getISO()
    {
        return $this->getIsolatedPower();
    }
    
    public function getPowerVsSpeed($start = null, $finish = null)
    {
        $this->load->library('Stats/PowerVsSpeed');
        
        return $this->powervsspeed->getPlayer($this, $start, $finish);
    }
    
    public function getOnBasePercentage()
    {
        $this->load->library('Stats/OnBasePercentage');
        
        return $this->onbasepercentage->getPlayer($this);
    }
    
    public function getBattingAverageOnBallsInPlay()
    {
        $this->load->library('Stats/BattingAverageOnBallsInPlay');
        
        return $this->battingaverageonballsinplay->getPlayer($this);
    }
    
    public function getBABIP()
    {
        return $this->getBattingAverageOnBallsInPlay();
    }
    
    public function getContactPercentage()
    {
        $this->load->library('Stats/ContactPercentage');
        
        return $this->contactpercentage->getPlayer($this);
    }
    
    public function __call($method, $args = null)
    {
        if (method_exists('Season', $method)) {
            $seasons = $this->getSeasons();
            $total = 0;
            
            foreach ($seasons as $season) {
                $total += $season->{$method}();
            }
            
            if ($total) {
                return $total;
            }
        }
        
        return false;
    }
        
    public function getAvailablePositions()
    {
        return array(
            '1B' => 'First Base',
            '2B' => 'Second Base',
            '3B' => 'Third Base',
            'SS' => 'Shortstop',
            'IF' => 'Infield',
            'P' => 'Pitcher',
            'RP' => 'Relief Pitcher',
            'CL' => 'Closer',
            'C' => 'Catcher',
            'LF' => 'Left Field',
            'CF' => 'Center Field',
            'RF' => 'Right Field',
            'OF' => 'Outfield',
        );
    }
        
}