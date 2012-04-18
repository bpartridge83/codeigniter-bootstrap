<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class NcaaParse {
        
    protected $CI;
    protected $output = true;
        
    public function __construct()
    {
        $this->CI =& get_instance();
    }
        
    public function getSeasonsForTeam($slug, $year, $output = true)
    {
        $this->output = $output;
    
        if (strpos($year, '-')) {
            $years = explode('-', $year);
            
            $year = $years[0];
            
            while ($year < $years[1] + 1) {
                if ($this->output) { print_r('Getting '.$year.'...'); }
                $this->getSeasonForTeam($slug, $year, $output);
                $year++;
            }
        
            return false;
        }
        
        return $this->getSeasonForTeam($slug, $year, $output);
    }
    
    
    
    public function getSeasonForTeam($slug, $year = 2011, $output = true)
    {
        $this->output = $output;
    
        //http://web1.ncaa.org/stats/StatsSrv/careerteam?academicYear=2011&doWhat=display&orgId=8530&sortOn=0&sportCode=MBA
        $this->CI->load->model('teamrepository','_team');
        
        if (is_numeric($slug)) {
            $team = $this->CI->_team->findOneByNcaaId($slug);
        } else {
            $team = $this->CI->_team->findOneBySlug($slug);
        }
        
        if ($team && $team->getNcaaId()) {
        
            //print_r($team);
            
            $curl = new CURL();
            $curl->addSession('http://web1.ncaa.org/stats/StatsSrv/careersearch');
            $curl->setOpt(CURLOPT_RETURNTRANSFER, 1);
            $curl->setOpt(CURLOPT_HEADER, 1);
            $page = $curl->exec();
            $curl->clear();
            
            preg_match('|Set-Cookie: (.*);|U', $page, $cookies);
            
            //print_r($cookies);
            
            //$url = sprintf('http://web1.ncaa.org/stats/StatsSrv/careerteam?academicYear=%s&doWhat=display&orgId=%s&sortOn=0&sportCode=MBA', $year, $team->getNcaaId());
            
            $url = sprintf('http://web1.ncaa.org/stats/StatsSrv/careerteam?academicYear=%s&coachId=-100&division=1&doWhat=display&idx=&orgId=%s&playerId=-100&sortOn=0&sportCode=MBA', $year, $team->getNcaaId());
            
            //$url = 'http://lh.beta.smallballstats.info/web/teams/mcmurray.2011.html';
            
            if (!isset($cookies[1])) {
                if ($this->output) { print_r("No Cookie Set\n"); }
                return false;
            }
            
            $curl = new CURL();
            $curl->addSession( $url );
            $curl->setOpt(CURLOPT_COOKIE, $cookies[1]);
            $curl->setOpt(CURLOPT_REFERER, 'http://web1.ncaa.org/stats/StatsSrv/careersearch');
            $page = $curl->exec();
            $curl->clear();
            
            //print_r($page);
            //print_r($url);
            
            $page = trim($page);
            
            //print_r($page);

            $startCoach = strpos($page, 'Head Coach');
            //$startCoach = stripos($page, '<table', $startCoach);
            $endCoach = strpos($page, '</TABLE>', $startCoach) + 8;
            $coach = substr($page, $startCoach, $endCoach - $startCoach);
            
            $coachId = $this->getCoachId($coach);
            $coach = $this->cleanRow($coach);
            $coach = $this->statsFromCoach($coach, $coachId);
            
            if ($this->output) { print_r($coach); }
            
            $startTeam = strpos($page, '<TABLE class="statstable" width="100%" align="CENTER" cellspacing="0" border="1" cellpadding="0">');
            $finishTeam = strpos($page, '</TABLE>', $startTeam) + 8;
            $teamStats = substr($page, $startTeam, $finishTeam - $startTeam);
            $startRow = strpos($teamStats, '<TR bgcolor=WHITE>');
            $endRow = strpos($teamStats, '</TR>', $startRow) + 5;
            $teamStats = substr($teamStats, $startRow, $endRow - $startRow);
            
            // Get Possible Existing Season for Team
            $existing = $team->getSeason($year);
            
            $season = $this->seasonFromRow($teamStats, $existing);
            
            $season->setCoach($coach);
            
            if ($this->output) { print_r("Season with Coach:\n"); }
            if ($this->output) { print_r($season); }

            $startPlayers = strpos($page, '<TABLE class="statstable" width="100%" align="CENTER" cellspacing="0" border="1" cellpadding="0">', $finishTeam);
            $finishPlayers = strpos($page, '</TABLE>', $startPlayers) + 8;
            $playerStats = substr($page, $startPlayers, $finishPlayers - $startPlayers);
            
            $playerRows = $this->playerRowsFromTable($playerStats);
            $players = $this->playersFromRows($playerRows);
            
            //if ($this->output) { print_r("All Players, Not Saved:\n"); }
            //if ($this->output) { print_r($players); } 
            
            $players = $this->saveTeamSeasonForPlayers($players, $team);
            $season = $this->savePlayersForTeamSeason($season, $players);
            
            //if ($this->output) { print_r($season); }

            $team->addSeason($season);
            
            if ($this->output) { print_r($team); }
            
            $team->save();
        }
        
        if ($this->output) { print_r("\n"); }
        
        return true;
    }
    
    protected function getCoachId($str)
    {
        preg_match('/showCareer\((.*?)\)/', $str, $matches);
        
        return $matches[1];
    }
    
    protected function getPlayerId($str)
    {
        preg_match('/getYearStats\((.*?)\)/', $str, $matches);
        
        return $matches[1];
    }
    
    protected function statsFromCoach($list, $id)
    {    
        foreach ($list as $key => $entry) {
            switch ($entry) {
                case 'Head Coach':
                case 'Name:':
                case 'Alma Mater':
                case 'Date Of Birth':
                case 'Yrs Coaching':
                case 'Record':
                    unset($list[$key]);
                    break;
                default:
                    break; 
            }
        }
        
        $temp = $list;
        
        unset($list);
        $list = array();
                    
        foreach ($temp as $key => $value) {
            if ($value != '') {
                $list[] = $value;
            }
        }
        
        $name = $list[0];
        $slug = $this->CI->slugify->simple($name);
        
        $name = explode(' ', $name);
        
        $this->CI->load->model('coachRepository', '_coach');
        $coach = $this->CI->_coach->findOneByNcaaId($id);
                
        if (!$coach) {
            $coach = new Coach();
            $coach->setFirstName($name[0]);
            $coach->setLastName($name[1]);
            $coach->setSlug($slug);
            $coach->setNcaaId($id);
            $coach->save();
        }
        
        return $coach;
    }
    
    protected function cleanRow($str)
    {
        $str = nl2br(trim(strip_tags($str)));
        $list = explode('<br />', $str);
        
        foreach ($list as $key => $entry) {
            $entry = str_replace('&nbsp;', ' ', $entry);
            if (!trim($entry) && trim($entry) != 0) {
                unset($list[$key]);
            } else {
                $list[$key] = trim($entry);
            }
        }
        
        $temp = $list;
        
        unset($list);
        $list = array();
                    
        foreach ($temp as $key => $value) {
            if ($value != '') {
                if ($value != '-') {
                    $list[] = utf8_decode($value);
                } else {
                    $list[] = '';
                }
            }
        }
        
        $list = array_values($list);
        
        return $list;
    }
    
    protected function playerRowsFromTable($table)
    {
        $start = strpos($table, '<TR') + 20;
        $start = strpos($table, '<TR', $start + 20);
        $start = strpos($table, '<TR', $start + 20);
        $start = strpos($table, '<TR', $start + 20);
        
        $playerRows = array();
        
        $finish = $start;
        
        while (strpos($table, '<TR', $start)) {
            $start = strpos($table, '<TR', $finish);
            $finish = strpos($table, '</TR>', $start);
            $playerRows[] = substr($table, $start, $finish - $start);
            $start = $finish;
        }
        
        return $playerRows;
    }
    
    protected function playersFromRows($rows)
    {
        $players = array();
    
        foreach ($rows as $row) {
            $ncaaId = $this->getPlayerId($row);
            
            $stats = $this->cleanRow($row);
            $players[$ncaaId] = $stats;
        }
        
        return $players;
    }
    
    protected function saveTeamSeasonForPlayers($players, $team)
    {
        $list = array();
    
        $this->CI->load->model('playerRepository', '_player');
        
        foreach ($players as $ncaaId => $stats) {
            
            $player = $this->CI->_player->findOneByNcaaId($ncaaId);
            
            $name = explode(',', $stats[0]);
            
            if (!$player) {
                $player = new Player();
                $player->setNcaaId($ncaaId);
                
                $player->setFirstName(trim($name[1]));
                $player->setLastName(trim($name[0]));
                
                $player->save();
            }
            
            if (!$player->getFirstName()) {
                $player->setFirstName(trim($name[1]));
            }
            
            if (!$player->getLastName()) {
                $player->setLastName(trim($name[0]));
            }
            
            $year = explode('-',$stats[2]);
            $year = date('Y', strtotime("january 1, ".$year[1]));
            
            if ($this->output) { print_r("Checking for Player Season with Year: ".$year."\n"); }
            
            $season = $player->getSeason($year);
            
            if (!$season) {
                if ($this->output) { print_r("No Season for Player, Creating New Season\n"); }
                $season = new Season();
            }
            
            $season->setYear($year);
            $season->setClass($stats[1]);
            $season->setPosition($stats[3]);
            $season->setTeam($team);
            $season->setGames($stats[4]);
            $season->setAtBats($stats[5]);
            $season->setRuns($stats[6]);
            $season->setHits($stats[7]);
            $season->setDoubles($stats[9]);
            $season->setTriples($stats[10]);
            $season->setHomeRuns($stats[11]);
            $season->setTotalBases($stats[12]);
            $season->setRunsBattedIn($stats[14]);
            $season->setStolenBases($stats[15]);
            $season->setStolenBaseAttempts($stats[16]);
            $season->setWalks($stats[17]);
            $season->setStrikeouts($stats[18]);
            $season->setHitByPitch($stats[19]);
            $season->setSacrificeHits($stats[20]);
            $season->setSacrificeFlies($stats[21]);
            $season->setAppearances($stats[22]);
            $season->setGamesStarted($stats[23]);
            $season->setCompleteGames($stats[24]);
            $season->setWins($stats[25]);
            $season->setLosses($stats[26]);
            $season->setSaves($stats[27]);
            $season->setShutouts($stats[28]);
            $season->setInningsPitched($stats[29]);
            $season->setHitsAllowed($stats[30]);
            $season->setRunsAllowed($stats[31]);
            $season->setEarnedRuns($stats[32]);
            $season->setWalksAllowed($stats[33]);
            $season->setStrikeoutsPitched($stats[34]);
            
            $player->addSeason($season);       
            
            //print_r($player);
            //print_r('<br /><br />');
                 
            $player->save();
            
            if ($this->output) { print_r("Player with Season:\n"); }
            if ($this->output) { print_r($player); }

            $list[] = $player;
        }   
        
        if ($this->output) { print_r("List of Players:\n"); }
        if ($this->output) { print_r($list); }
        
        return $list;
    }
    
    protected function savePlayersForTeamSeason($season, $players)
    {
        foreach ($players as $player) {
            $season->addPlayer($player);
        }
        
        return $season;
    }
    
    protected function seasonFromRow($row, $season = null)
    {
        $stats = $this->cleanRow($row);
        
        if (!$season) {
            if ($this->output) { print_r("No Existing Season, Creating New Season\n"); }
            $season = new Season();
        }
        
        $season->setGames($stats[4]);
        $season->setAtBats($stats[5]);
        $season->setRuns($stats[6]);
        $season->setHits($stats[7]);
        $season->setDoubles($stats[9]);
        $season->setTriples($stats[10]);
        $season->setHomeRuns($stats[11]);
        $season->setTotalBases($stats[12]);
        $season->setRunsBattedIn($stats[14]);
        $season->setStolenBases($stats[15]);
        $season->setStolenBaseAttempts($stats[16]);
        $season->setWalks($stats[17]);
        $season->setStrikeouts($stats[18]);
        $season->setHitByPitch($stats[19]);
        $season->setSacrificeHits($stats[20]);
        $season->setSacrificeFlies($stats[21]);
        $season->setAppearances($stats[22]);
        $season->setGamesStarted($stats[23]);
        $season->setCompleteGames($stats[24]);
        $season->setWins($stats[25]);
        $season->setLosses($stats[26]);
        $season->setSaves($stats[27]);
        $season->setShutouts($stats[28]);
        $season->setInningsPitched($stats[29]);
        $season->setHitsAllowed($stats[30]);
        $season->setRunsAllowed($stats[31]);
        $season->setEarnedRuns($stats[32]);
        $season->setWalksAllowed($stats[33]);
        $season->setStrikeoutsPitched($stats[34]);
        
        $year = explode('-',$stats[2]);
        $year = date('Y', strtotime("january 1, ".$year[1]));
            
        $season->setYear($year);

        return $season;
    }

    public function getCookie()
    {
        $curl = new CURL();
        $curl->addSession('http://web1.ncaa.org/stats/StatsSrv/careersearch');
        $curl->setOpt(CURLOPT_RETURNTRANSFER, 1);
        $curl->setOpt(CURLOPT_HEADER, 1);
        $page = $curl->exec();
        $curl->clear();
        
        preg_match('|Set-Cookie: (.*);|U', $page, $cookies);
        
        return $cookies[1];
    }
    
}
