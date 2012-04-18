<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class BoydsworldParse {
        
    protected $CI;
    protected $output = true;
    
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('PlayerRepository', '_player');
        $this->CI->load->model('SeasonRepository', '_season');
        $this->CI->load->model('TeamRepository', '_team');
    }

	public function importAll($start = null, $real = false)
    {
        $this->importTeamHitting($start, $real);
        $this->importTeamPitching($start, $real);
        $this->importHitters($start, $real);
        $this->importPitchers($start, $real);
    }

    public function importTeamHitting($start = 0, $real = false)
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
                
                  if ($data[0] >= $start) {
                    
                    $season = null;
                    $official = null;
                    
                    $team = utf8_encode($data[1]);
                    $slug = $this->CI->slugify->simple($team);
                    $team = $this->CI->_team->findOneBySlug($slug);
                    
                    $year = $data[0];
                    
                    if ($team) {
                    
                        $season = $this->CI->_season->findOneBySourceAndTeamAndYear('Boydsworld', $team, $year);
                        $official = $this->CI->_season->findOneBySourceAndTeamAndYear('Smallball', $team, $year);
                        
                        if (!$season) {
                            $season = new Season();
                            $official = false;
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
                        $season->setIsTeam(true);
                        $season->setSource('Boydsworld');
                        $season->save();

                        if (!$official) {
                            $official = clone $season;
                            $official->setSource('Smallball');
                        }
                        
                        $official->setConference($team->getConference());
                        $official->setTeam($team);
                        $official->setIsTeam(true);
                        $official->save();
                                                
                        print_r("Adding Team Season (Hitting): ".$team->getName()." - ".$season->getYear()."\n");
                        
                    } else {
                     
                        print_r("Team Not Found: ".$data[1]." - ".$data[0]."\n");
                        
                    }
                    
                  } // year
                  
                } // row
                $row++;
            }
            fclose($handle);
        }
    }

    public function importTeamPitching($start = 0, $real = false)
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
                
                  if ($data[0] >= $start) {
                  
                    $season = null;
                    $official = null;
                    
                    $team = utf8_encode($data[1]);
                    $slug = $this->CI->slugify->simple($team);
                    $team = $this->CI->_team->findOneBySlug($slug);
                    
                    $year = $data[0];
                    
                    if ($team) {
                    
                        $season = $this->CI->_season->findOneBySourceAndTeamAndYear('Boydsworld', $team, $year);
                        $official = $this->CI->_season->findOneBySourceAndTeamAndYear('Smallball', $team, $year);
                        
                        if (!$season) {
                            $season = new Season();
                            $official = false;
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
                        $season->setIsTeam(true);
                        $season->setSource('Boydsworld');
                        $season->save();
                            
                        if (!$official) {
                            $official = clone $season;
                            $official->setSource('Smallball');
                        }
                        
                        $official->setTeam($team);
                        $official->setConference($team->getConference());
                        $official->setIsTeam(true);
                        $official->save();
                        
                        print_r("Adding Team Season (Pitching): ".$team->getName()." - ".$season->getYear()."\n");
                        
                    } else {
                     
                        print_r("Team Not Found: ".$data[1]." - ".$data[0]."\n");
                        
                    }
                    
                  }
                    
                }
                $row++;
            }
            fclose($handle);
        }
    }

    public function importHitters($start = 0, $real = false)
    {
        if ($real) {
            $file = "./web/utilities/bw-hitting.csv";
        } else {
            $file = "./web/utilities/bw-hitting-test.csv";
        }
    
        $row = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
        
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            
                if ($row) {
                    
                  if ($data[0] >= $start) {
                  
                    $season = null;
                    $official = null;
                    
                    $player = utf8_encode($data[2]);
                    $slug = $this->CI->slugify->simple($player);
                    $player = $this->CI->_player->findOneBySlug($slug);
                    
                    $team = utf8_encode($data[1]);
                    $slug = $this->CI->slugify->simple($team);
                    $team = $this->CI->_team->findOneBySlug($slug);
                    
                    $year = $data[0];
                    
                    //print_r($team);
                    
                    if ($team) {
                    
                        if ($player) {
                            $season = $this->CI->_season->findOneBySourceAndPlayerAndYear('Boydsworld', $player, $year);
                            $official = $this->CI->_season->findOneBySourceAndPlayerAndYear('Smallball', $player, $year);
                        } else {
                            $season = new Season();
                        }
                        
                        if (!$season) {
                            $season = new Season();
                            $official = false;
                        }
                    
                        $num = count($data);
                        for ($c=0; $c < $num; $c++) {
                            $method = 'set'.ucwords($this->hitter[$c]);
                            if (method_exists($season, $method)) {
                                $season->{$method}($data[$c]);
                                if ($official) {
                                    $official->{$method}($data[$c]);
                                }
                            }
                        }
                        
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
                            //print_r("Existing Player: ".$team->getName().", ".$player->getName().", ".$season->getYear()."\n");
                        }
                        
                        $season->setPlayer($player);
                        $season->setIsPlayer(true);
                        $season->setTeam($team);
                        $season->setSource('Boydsworld');
                        $season->save();
                            
                        if (!$official) {
                            $official = clone $season;
                            $official->setSource('Smallball');
                        }
                        
                        $official->setPlayer($player);
                        $official->setIsPlayer(true);
                        $official->setTeam($team);
                        $official->setConference($team->getConference());
                        $official->save();
                        
						print_r("Adding Player Season (Hitting): ".$team->getName().", ".$player->getName().", ".$season->getYear()."\n");

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

    public function importPitchers($start = 0, $real = false)
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
                    
                  if ($data[0] >= $start) {
                  
                    $season = null;
                    $official = null;
                    
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
                        } else {
                            $season = new Season();
                        }
                        
                        if (!$season) {
                            $season = new Season();
                            $official = false;
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
                            //print_r("Existing Player: ".$team->getName().", ".$player->getName().", ".$season->getYear()."\n");
                        }
                        
                        $season->setPlayer($player);
                        $season->setIsPlayer(true);
                        $season->setTeam($team);
                        $season->setSource('Boydsworld');
                        $season->save();
                            
                        if (!$official) {
                            $official = clone $season;
                            $official->setSource('Smallball');
                        }
                        
                        $official->setPlayer($player);
                        $official->setIsPlayer(true);
                        $official->setTeam($team);
                        $official->setConference($team->getConference());
                        $official->save();

						print_r("Adding Player Season (Pitching): ".$team->getName().", ".$player->getName().", ".$season->getYear()."\n");
                        
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