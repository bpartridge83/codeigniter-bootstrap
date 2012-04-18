<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class PointstreakParse {
        
    protected $CI;
    protected $html;
    protected $game;
    protected $innings;
    
    public function __construct()
    {
        $this->CI =& get_instance();
        
        $this->CI->load->model('gameplayerbatting', '_batter');
        $this->CI->load->model('gameplayerpitching', '_pitcher');
        $this->CI->load->model('playerrepository', '_player');
    }
 
    public function process($html)
    {
        $this->game = new Game();
    
        $this->html = $html;
        
        $this->getPointstreakId();
        $this->getUrl();
        
        $this->getLeague();
        $this->getTeams();
        
        $this->getScore();
        $this->getGameInfo();
        
        $this->getBattingLineup();
        $this->getPitchers();
        
        $this->getInnings();
        
        print_r($this->game);
    }
    
    protected function getPointstreakId()
    {
        preg_match('/gameid=[0-9]+/', $this->html->innertext, $matches);
        $id = str_replace('gameid=', '', $matches[0]);
        
        $this->game->setPointstreakId($id);
    }
    
    protected function getUrl()
    {
        $url = sprintf('http://www.pointstreak.com/baseball/boxscore.html?gameid=%s', $this->game->getPointstreakId());
        
        $this->game->setUrl($url);
    }
    
    protected function getLeague()
    {
        $league_name = trim($this->html->find('.psbb_league_name', 0)->plaintext);
        
        $slug = $this->CI->slugify->simple($league_name);
        
        $this->CI->load->model('LeagueRepository', '_league');
        $league = $this->CI->_league->findOneBySlug($slug);
        
        if (!$league) {
            $league = $this->CI->_league->createByName($league_name);
        }
        
        $this->game->setLeague($league);
        
        if ($this->game->getLeague()->hasLevel()) {
            $this->game->setLevel($this->game->getLeague()->getLevel());
        }
    }
    
    protected function getTeams()
    {
        $awayTeamNickname = $this->html->find('#psbb_box_score_away', 0)->find('th.psbb_nobg', 0)->plaintext;
        $awayTeamName = $this->html->find('td.psbb_box_score_team', 0)->plaintext;
        
        $awayTeam = $this->getOrCreateTeam($awayTeamName, $awayTeamNickname);
        $this->game->setAwayTeam($awayTeam);
        
        $homeTeamNickname = $this->html->find('#psbb_box_score_home', 0)->find('th.psbb_nobg', 0)->plaintext;
        $homeTeamName = $this->html->find('td.psbb_box_score_team', 1)->plaintext;
        
        $homeTeam = $this->getOrCreateTeam($homeTeamName, $homeTeamNickname);
        $this->game->setHomeTeam($homeTeam);
    }
    
    protected function getOrCreateTeam($name, $nickname)
    {
        $nickname = trim($nickname);
        
        $name = trim(str_replace($nickname, '', $name));
        $slug = $this->CI->slugify->simple($name);
        
        $team = $this->CI->_team->findOneBySlug($slug);
    
        if (!$team) {
            $team = new Team();
            $team->setName($name);
            $team->setNickname($nickname);
            $team->save();
        }
        
        return $team;
    }
    
    protected function getScore()
    {
        $awayScore = $this->html->find('.psbb_game_score', 0)->plaintext;
        $this->game->setAwayScore($awayScore);
        
        $homeScore = $this->html->find('.psbb_game_score', 1)->plaintext;
        $this->game->setHomeScore($homeScore);
    }
    
    protected function getBattingLineup()
    {
        $awayRows = $this->html->find('#psbb_box_score_away', 0)->find('tr');
        
        $this->order = 1;
        
        foreach ($awayRows as $key => $row) {
            if ($key > 1 && $key < (count($awayRows)-1)) {
                $batter = $this->getBatterFromRow($row);
                
                $this->getOrCreatePlayerForTeam($batter, $this->game->getAwayTeam());
                
                $this->game->addAwayBatter($player);
                //print_r($player);
            }
        }
        
        $homeRows = $this->html->find('#psbb_box_score_home', 0)->find('tr');
        
        $this->order = 1;
        
        foreach ($homeRows as $key => $row) {
            if ($key > 1 && $key < (count($homeRows)-1)) {
                $batter = $this->getBatterFromRow($row);
                
                $this->game->addHomeBatter($player);
                //print_r($player);
            }
        }
        
        print_r($this->game);
    }
    
    protected function getBatterFromRow($row)
    {
        $batter = new GamePlayerBatting();
        $batter->setNumber($row->find('td', 0)->plaintext);
        $batter->setPosition($row->find('td', 2)->plaintext);
        
        if (strpos($row->find('td', 1)->innertext, '&nbsp;') > -1) {
            $batter->setStarted(false);
        } else {
            $batter->setStarted(true);
            $batter->setOrder($this->order);
            $this->order += 1;
        }
        
        $name = explode(', ', $row->find('td', 1)->plaintext);
        
        $batter->setFirstName($name[1]);
        $batter->setLastName(str_replace('&nbsp;', '', $name[0]));
                        
        $batter->setNumber($row->find('td', 0)->plaintext);
        $batter->setAtBats($row->find('td', 3)->plaintext);
        $batter->setRuns($row->find('td', 4)->plaintext);
        $batter->setHits($row->find('td', 5)->plaintext);
        $batter->setRunsBattedIn($row->find('td', 6)->plaintext);
        $batter->setWalks($row->find('td', 7)->plaintext);
        $batter->setStrikeouts($row->find('td', 8)->plaintext);
        
        return $batter;
    }
    
    protected function getOrCreatePlayerForTeam($batter, $team)
    {
        $player = $this->CI->_player->findOneWithNameAndTeam($batter->getFirstName(), $batter->getLastName(), $team);
        
        print_r('trying to find a player here');
        print_r($player);
        die();
    }
    
    protected function getPitchers()
    {
        $section = $this->html->find('#psbb_pitchingStats', 0);
        
        $awayPitchingRows = $section->find('.psbb_stats_table', 0)->find('tr');
        
        foreach ($awayPitchingRows as $key => $row) {
            if ($key > 1 && $key < (count($awayPitchingRows)-1)) {
                $player = $this->getPitcherFromRow($row);
                
                $this->game->addAwayPitcher($player);
                //print_r($player);
            }
        }
        
        $homePitchingRows = $section->find('.psbb_stats_table', 1)->find('tr');
        
        foreach ($homePitchingRows as $key => $row) {
            if ($key > 1 && $key < (count($homePitchingRows)-1)) {
                $player = $this->getPitcherFromRow($row);
                
                $this->game->addHomePitcher($player);
                //print_r($player);
            }
        }
    }
    
    protected function getPitcherFromRow($row)
    {
        $player = new GamePlayerPitching();
        $player->setNumber($row->find('td', 0)->plaintext);
        
        $name = explode(', ', $row->find('td', 1)->plaintext);
        
        $player->setFirstName(trim($name[1]));
        $player->setLastName(trim(str_replace('&nbsp;', '', $name[0])));
        
        $player->setInningsPitched($row->find('td', 2)->plaintext);
        
        return $player;
    }
    
    protected function getGameInfo()
    {
        //$this->game->setWinningPitcher($this->html->find('#psbb_gameInfo li', 0)->plaintext);
        
        $date = $this->getGameInfoPiece(3, 'Date');
        $this->game->setDatetime(strtotime($date));
        
        $startTime = $this->getGameInfoPiece(4, 'Start Time');
        $this->game->setStartTime($startTime);
        
        $duration = $this->getGameInfoPiece(5, 'Duration');
        $this->game->setDuration($duration);
        
        $endTime = $this->getGameInfoPiece(6, 'End Time');
        $this->game->setEndTime($endTime);
        
        $location = $this->getGameInfoPiece(11, 'Location');
        $this->game->setLocation($location);
        
        $attendance = $this->getGameInfoPiece(12, 'Attendance');
        $this->game->setAttendance($attendance);
    }
    
    protected function getGameInfoPiece($index, $string)
    {
        $info = $this->html->find('#psbb_gameInfo li', $index)->plaintext;
        $info = trim(str_replace($string.':', '', $info));
        
        return $info;
    }
    
    protected function getInnings()
    {
        $this->getInningNumbers();
        
        foreach ($this->innings as $key => $inn) {
            foreach ($inn as $side => $value) {
                $inning = new Inning();
                $inning->setNumber($key);
                $inning->setSide($side);
                $inning->setDatetime($this->game->getDatetime());
                switch ($side) {
                    case 'home':
                        $inning->setTeam($this->game->getHomeTeam());
                        break;
                    case 'away':
                        $inning->setTeam($this->game->getAwayTeam());
                        break;
                }
                
                $inning = $this->getInningStats($inning);
                
                $inning = $this->getPlays($inning);
                
                $inn[$side] = $inning;
            }
            $this->innings[$key] = $inn;
        }
        
        $this->game->setInnings($this->innings);
    }
    
    protected function getInningStats($inning)
    {
        //Runs: 2, Hits: 2, Errors: 0, LOB: 1
    
        $half = ($inning->getSide() == 'away') ? 'top' : 'bottom';
        $table = $this->html->find(sprintf('tr#%s%sinning td', $half, $inning->getNumber()));
        
        $stats = $table[count($table)-1]->plaintext;
        
        $stats = explode(',', $stats);
        
        $runs = preg_replace("/\D/", "", $stats[0]); 
        $inning->setRuns($runs);
        
        $hits = preg_replace("/\D/", "", $stats[1]); 
        $inning->setHits($hits);
        
        $errors = preg_replace("/\D/", "", $stats[2]); 
        $inning->setErrors($errors);
        
        $leftOnBase = preg_replace("/\D/", "", $stats[3]); 
        $inning->setLeftOnBase($leftOnBase);
        
        return $inning;
    }
    
    protected function getInningNumbers()
    {
        $this->innings = array();
    
        $tabs = $this->html->find('#psbb_playbyplay .tabs a');
        
        foreach ($tabs as $tab) {
            $num = $tab->plaintext;
            
            $this->innings[$num] = array();
            
            if ($this->html->find(sprintf('tr#top%sinning', $num))) {
                $this->innings[$num]['away'] = array();
            }
            
            if ($this->html->find(sprintf('tr#bottom%sinning', $num))) {
                $this->innings[$num]['home'] = array();
            }
        }
    }
    
    protected function getPlays($inning)
    {
        $this->plays = $this->html->find(sprintf('tr#%s%sinning', $inning->getHalf(), $inning->getNumber()), 0)->find('tr');
        
        $play = null;
        
        foreach ($this->plays as $key => $playRow) {
            if ($key > 0 && $key < count($this->plays) - 1) {
                foreach ($playRow->find('td') as $key => $value) {
                    if (!$key) {
                        // start new play
                        $play = new Play();
                        
                        // batter information
                        $batter = $this->getBatterForPlay($value, $inning);
                        $play->setBatter($batter);
                    } else {
                        $play->setRaw($value->plaintext);
                        print_r($play);
                        
                        print_r($value->plaintext);
                        print_r("\n");
                    }
                }
                print_r("\n\n");
            }
        }
        
        die();
    }
    
    protected function getBatterForPlay($play, $inning)
    {
        $batter = $play->plaintext;
        
        preg_match('/#[0-9+]/', $batter, $number);
        $number = str_replace('#', '', $number[0]);
        
        $batter = trim(preg_replace('/#[0-9+]/', '', $batter));
        list($firstName, $lastName) = explode(' ', $batter);
        
        $lineup = $this->game->{sprintf('get%sLineup', $inning->getSide())}();
        
        foreach ($lineup as $key => $player) {
            if ($player->getLastName() == $lastName) {
                if (strpos($player->getFirstName(), $firstName) == 0) {
                    
                    // Found Player Here
                    if (!$player->getNumber() && $number) {
                        $lineup[$key]->setNumber($number);
                    }
                    
                    print_r($player);
                }
            }
        }
        
        //die();
    }
    
    protected function parsePlay($play)
    {
        // 6 Austin Nola advances to 1st (single to shortstop) 
        
        
    }
}