<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class LahmanParse {
        
    protected $CI;
    
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('PlayerRepository', '_player');
        $this->CI->load->model('SeasonRepository', '_season');
        $this->CI->load->model('TeamRepository', '_team');
    }
    
    public function importSchools($real = false, $year = 0)
    {
        if ($real) {
            $file = "./web/utilities/lahman/Schools.csv";
        } else {
            $file = "./web/utilities/lahman/Schools-test.csv";
        }
    
        $row = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
        
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            
                if ($row) {
                    
                    $team = $this->CI->_team->findOneByOfficialName(utf8_encode($data[1]));
                    
                    if (!$team) {
                        $team = $this->CI->_team->findOneByLahmanId(utf8_encode($data[0]));
                    }
                    
                    if (!$team) {
                        $team = new Team();
                        
                        $team->setOfficialName(utf8_encode($data[1]));
                        $team->setLahmanId(utf8_encode($data[0]));
                        $team->setNickname($data[4]);
                        $team->setCity($data[2]);
                        $team->setState($data[3]);
                    }
                    
                    print_r($team);
                    
                } //row
                
                $row++;
                
            }
            fclose($handle);
        }
    }
    
    public function importTeamHitting($real = false)
    {
        if ($real) {
            $file = "./web/utilities/bw-team-hitting.csv";
        } else {
            $file = "./web/utilities/bw-team-hitting-test.csv";
        }
    
        $row = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            
                if ($row) {
                    
                    $team = utf8_encode($data[1]);
                    $slug = $this->CI->slugify->simple($team);
                    $team = $this->CI->_team->findOneBySlug($slug);
                    
                    $year = $data[0];
                    
                    if ($team) {
                    
                        $season = $this->CI->_season->findOneBySourceAndTeamAndYear('Boydsworld', $team, $year);
                        $official = $this->CI->_season->findOneBySourceAndTeamAndYear('Smallball', $team, $year);
                        
                        if (!$season) {
                            $season = new Season();
                        }
                        
                        $num = count($data);
                        for ($c=0; $c < $num; $c++) {
                            $method = 'set'.ucwords($this->teamHitting[$c]);
                            if (method_exists($season, $method)) {
                                $season->{$method}($data[$c]);
                                if ($official) {
                                    $official->{$method}($data[$c]);
                                }
                            }
                        }
                        $season->setLevel('College');
                        $season->setLeague('NCAA');
                        $season->setDivision('D1');
                        
                        $season->setTeam($team);
                        $season->setSource('Boydsworld');
                        $season->save();
                            
                        if (!$official) {
                            $official = clone $season;
                            $official->setSource('Smallball');
                        }
                        
                        $official->setTeam($team);
                        $official->save();
                        
                        print_r("Adding Season: ".$team->getName()." - ".$season->getYear()."\n");
                        
                    } else {
                     
                        print_r("Team Not Found: ".$data[1]." - ".$data[0]."\n");
                        
                    }
                }
                $row++;
            }
            fclose($handle);
        }
    }
    
    public function importPitchers($real = false, $year = 0)
    {
        if ($real) {
            $file = "./web/utilities/bw-pitching.csv";
        } else {
            $file = "./web/utilities/bw-pitching-test.csv";
        }
    
        $row = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
        
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            
                if ($row) {
                    
                  if ($data[0] >= $year) {
                    
                    $player = utf8_encode($data[2]);
                    
                    $slug = $this->CI->slugify->simple($player);
                    
                    $player = $this->CI->_player->findOneBySlug($slug);
                    
                    $team = utf8_encode($data[1]);
                    $slug = $this->CI->slugify->simple($team);
                    $team = $this->CI->_team->findOneBySlug($slug);
                    
                    $year = $data[0];
                    
                    if ($team) {
                    
                        if ($player) {
                            $season = $this->CI->_season->findOneBySourceAndPlayerAndYear('Boydsworld', $player, $year);
                            $official = $this->CI->_season->findOneBySourceAndPlayerAndYear('Smallball', $player, $year);
                        }
                        
                        if (!$season) {
                            $season = new Season();
                        }
                        
                        $num = count($data);
                        for ($c=0; $c < $num; $c++) {
                            $method = 'set'.ucwords($this->pitcher[$c]);
                            if (method_exists($season, $method)) {
                                $season->{$method}($data[$c]);
                                if ($official) {
                                    $official->{$method}($data[$c]);
                                }
                            }
                        }
                        
                        //print_r($season);
                        
                        $season->setPlayer(utf8_encode($season->getPlayer()));
                        
                        $season->setLevel('College');
                        $season->setLeague('NCAA');
                        $season->setDivision('D1');
                            
                        /* Save Player */
                        if (!$player) {
                            $player = new Player();
                            
                            $name = $season->getPlayer();
                            $name = explode(' ',$name);
                            
                            if (isset($name[count($name)-1])) {
                                $player->setLastName(utf8_encode($name[count($name)-1]));
                            }
                            
                            $name = array_splice($name, 0, count($name) - 1);
                            $name = implode(' ', $name);
                            
                            if (isset($name)) {
                                $player->setFirstName(utf8_encode($name));
                            }
                            
                            print_r("Adding Player: ".$team->getName().", ".$player->getName().", ".$season->getYear()."\n");
                            
                            $player->save();
                        } else {
                            print_r("Existing Player: ".$team->getName().", ".$player->getName().", ".$season->getYear()."\n");
                        }
                        
                        $season->setPlayer($player);
                        $season->setTeam($team);
                        $season->setSource('Boydsworld');
                        $season->save();
                            
                        if (!$official) {
                            $official = clone $season;
                            $official->setSource('Smallball');
                        }
                        
                        $official->setPlayer($player);
                        $official->setTeam($team);
                        $official->save();
                        
                        $teamOfficial = $this->CI->_season->findOneOfficialByTeamAndYear($team, $season->getYear());
                        if ($teamOfficial) {
                            $teamOfficial->addPlayer($player);
                            $teamOfficial->save();
                        }
                            
                    } // end team
                    
                  } // end year
                    
                } //row
                
                $row++;
                
            }
            fclose($handle);
        }
    }
    
    public function importTeamPitching($real = false)
    {
        if ($real) {
            $file = "./web/utilities/bw-team-pitching.csv";
        } else {
            $file = "./web/utilities/bw-team-pitching-test.csv";
        }
    
        $row = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            
                if ($row) {
                    
                    $team = utf8_encode($data[1]);
                    $slug = $this->CI->slugify->simple($team);
                    $team = $this->CI->_team->findOneBySlug($slug);
                    
                    $year = $data[0];
                    
                    if ($team) {
                    
                        $season = $this->CI->_season->findOneBySourceAndTeamAndYear('Boydsworld', $team, $year);
                        $official = $this->CI->_season->findOneBySourceAndTeamAndYear('Smallball', $team, $year);
                        
                        if (!$season) {
                            $season = new Season();
                        }
                        
                        $num = count($data);
                        for ($c=0; $c < $num; $c++) {
                            $method = 'set'.ucwords($this->teamPitching[$c]);
                            if (method_exists($season, $method)) {
                                $season->{$method}($data[$c]);
                                if ($official) {
                                    $official->{$method}($data[$c]);
                                }
                            }
                        }
                        $season->setLevel('College');
                        $season->setLeague('NCAA');
                        $season->setDivision('D1');
                        
                        $season->setTeam($team);
                        $season->setSource('Boydsworld');
                        $season->save();
                            
                        if (!$official) {
                            $official = clone $season;
                            $official->setSource('Smallball');
                        }
                        
                        $official->setSource('Smallball');
                        $official->setTeam($team);
                        $official->save();
                        
                        print_r("Adding Season: ".$team->getName()." - ".$season->getYear()."\n");
                        
                    } else {
                     
                        print_r("Team Not Found: ".$data[1]." - ".$data[0]."\n");
                        
                    }
                }
                $row++;
            }
            fclose($handle);
        }
    }

    public function checkTeams()
    {
        $row = 0;
        if (($handle = fopen("./web/utilities/bw-pitching.csv", "r")) !== FALSE) {
            print_r('found file');
        
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            
                if ($row) {
                    
                    $season = new Season();
                    
                    $num = count($data);
                    for ($c=0; $c < $num; $c++) {
                        $method = 'set'.ucwords($this->pitcher[$c]);
                        if (method_exists($season, $method)) {
                            $season->{$method}($data[$c]);
                        }
                    }
                    
                    $slug = $this->CI->slugify->simple($season->getTeam());
                    $team = $this->CI->_team->findOneBySlug($slug);
                    
                    if (!$team) {
                        print_r("Team Not Found: ".$season->getTeam()." - ".$season->getYear()."\n");
                    } else {
                        //print_r("Team Found: ".$season->getTeam()."\n");
                    }
                }
                
            $row++;
                
            }
            fclose($handle);
        }
    }
    
    public function getPlayer($player)
    {
        if (is_numeric($player)) {
            $player = $this->CI->_player->findOneByNcaaId($player);
        }
        
        if (!$player->getName() || !($player->getAtBats() || $player->getInningsPitched())) {
            return false;
        }
        
        print_r($player);
        print_r("\n\n");
        
        $teams = $player->getTeams();
        $searchType = ($player->getInningsPitched()) ? 'pitchers' : 'hitters';
        
        $url = sprintf('http://www.boydsworld.com/cgi/%s.pl?player=%s&style=Contains&submit=Search&team=%s', $searchType, $player->getName(), $teams[0]->getName());
                
        $curl = new CURL();
        $curl->addSession($url);
        $curl->setOpt(CURLOPT_RETURNTRANSFER, false);
        $curl->setOpt(CURLOPT_HEADER, false);
        $curl->setOpt(CURLOPT_REFERER, 'http://www.boydsworld.com/data/hitters.html');
        $curl->setOpt(CURLOPT_VERBOSE, true);
        $page = $curl->exec();
        $curl->clear();
        
        print_r($page);
        print_r("\n\n");
    }
    
    protected function mergeSeason($official, $season)
    {
    
    }
    
    protected $hitter = array(
        'year',
        'team',
        'player',
        'battingAverageStatic',
        'gamesPlayed',
        'gamesStarted',
        'atBats',
        'runs',
        'hits',
        'doubles',
        'triples',
        'homeRuns',
        'runsBattedIn',
        'totalBases',
        'sluggingPercentageStatic',
        'walks',
        'hitByPitch',
        'strikeouts',
        'groundedIntoDoublePlays',
        'onBasePercentageStatic',
        'sacrificeFlies',
        'sacrificeHits',
        'stolenBases',
        'stolenBaseAttempts',
        'putouts',
        'assists'
    );

    // Boydsworld Team: Batting
    
    // "Year","Team","AVG","GP","GS","AB","R",
    // "H","2B","3B","HR","RBI","TB","SLG",
    // "BB","HBP","SO","GIDP","OBP",
    // "SF","SH","SB","ATT"


    protected $teamHitting = array(
        'year',
        'team',
        'battingAverageStatic',
        'gamesPlayed',
        'gamesStarted',
        'atBats',
        'runs',
        'hits',
        'doubles',
        'triples',
        'homeRuns',
        'runsBattedIn',
        'totalBases',
        'sluggingPercentageStatic',
        'walks',
        'hitByPitch',
        'strikeouts',
        'groundedIntoDoublePlays',
        'onBasePercentageStatic',
        'sacrificeFlies',
        'sacrificeHits',
        'stolenBases',
        'stolenBaseAttempts',
    );
    
    // Boydsworld: Pitching
    
    // "Year","Team","Player","ERA","W","L","APP",
    // "GS","CG","SHO","CBO","SV",
    // "IP","H","R","ER","BB","SO",
    // "2B","3B","HR","AB","BAA",
    // "WP","HBP","BK","SFA","SHA"

    protected $pitcher = array(
        'year',
        'team',
        'player',
        'earnedRunAverageStatic',
        'wins',
        'losses',
        'appearances',
        'gamesStarted',
        'completeGames',
        'shutouts',
        'combinedShutouts',
        'saves',
        'inningsPitched',
        'hitsAllowed',
        'runsAllowed',
        'earnedRuns',
        'walksAllowed',
        'strikeoutsPitched',
        'doublesAllowed',
        'triplesAllowed',
        'homeRunsAllowed',
        'battersFaced',
        'battingAverageAgainst',
        'wildPitches',
        'battersHitByPitch',
        'balks',
        'sacrificeFliesAllowed',
        'sacrificeHitsAllowed'
    );
    
    // Boydsworld: Team Pitching
    
    // "Year","Team","ERA","W","L","APP",
    // "GS","CG","SHO","CBO","SV",
    // "IP","H","R","ER","BB","SO",
    // "2B","3B","HR","AB","BAA",
    // "WP","HBP","BK","SFA","SHA"

    protected $teamPitching = array(
        'year',
        'team',
        'earnedRunAverageStatic',
        'wins',
        'losses',
        'appearances',
        'gamesStarted',
        'completeGames',
        'shutouts',
        'combinedShutouts',
        'saves',
        'inningsPitched',
        'hitsAllowed',
        'runsAllowed',
        'earnedRuns',
        'walksAllowed',
        'strikeoutsPitched',
        'doublesAllowed',
        'triplesAllowed',
        'homeRunsAllowed',
        'battersFaced',
        'battingAverageAgainst',
        'wildPitches',
        'battersHitByPitch',
        'balks',
        'sacrificeFliesAllowed',
        'sacrificeHitsAllowed'
    );
    
}
//http://www.boydsworld.com/cgi/pitchers.pl?player=Evan%20Abrecht&style=Contains&submit=Search&team=Air%20Force