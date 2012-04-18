<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Season extends Document {

    /**
     * Level
     *
     * Options: MLB, COLL
     */
    protected $level;
    
    /**
     * League
     *
     * Options: American, National, NCAA
     */
    protected $league;
    
    /**
     * Division
     *
     * Options: East, West, Central, Division 1
     */
    protected $division;
    
    /**
     * Division
     *
     * Options: ACC, Big East, Pac 12
     */
    protected $conference;
    
    /**
     * Source
     *
     * Options: NCAA, Boydsworld, Smallball
     */
    protected $source;
    
    protected $year;
    
    protected $team;
    protected $player;
    protected $players = array();
    protected $coach;
    
    protected $isTeam;
    protected $isPlayer;
    
    protected $class;
    protected $position;
    
    protected $games;
    protected $gamesPlayed;
    protected $gamesStarted;
    protected $atBats;
    protected $runs;
    protected $hits;
    protected $doubles;
    protected $triples;
    protected $homeRuns;
    protected $totalBases;
    protected $runsBattedIn;
    protected $stolenBases;
    protected $stolenBaseAttempts;
    protected $walks;
    protected $strikeouts;
    protected $groundedIntoDoublePlays;
    protected $putouts;
    protected $assists;
    protected $hitByPitch;
    protected $sacrificeHits;
    protected $sacrificeFlies;
    
    protected $battingAverageStatic;
    protected $sluggingPercentageStatic;
    protected $onBasePercentageStatic;
    
    protected $appearances;
    protected $completeGames;
    protected $wins;
    protected $losses;
    protected $saves;
    protected $shutouts;
    protected $combinedShutouts;
    protected $inningsPitched;
    protected $hitsAllowed;
    protected $runsAllowed;
    protected $earnedRuns;
    protected $walksAllowed;
    protected $strikeoutsPitched;
    protected $doublesAllowed;
    protected $triplesAllowed;
    protected $homeRunsAllowed;
    protected $battersFaced;
    protected $battersHitByPitch;
    protected $balks;
    protected $battingAverageAgainst;
    protected $wildPitches;
    protected $sacrificeFliesAgainst;
    protected $sacrificeHitsAgainst;
    
    protected $earnedRunAverageStatic;
    
    protected $teamRosterUrl;
    protected $teamScheduleUrl;
    protected $teamStatsUrl;
    
    protected $isConferenceChampion;

    // Boydsworld Players: Pitching

    // "Year","Team","Player","ERA","W","L",
    // "APP","GS","CG","SHO","CBO","SV",
    // "IP","H","R","ER","BB","SO","2B","3B","HR","AB",
    // "BAA","WP","HBP","BK","SFA","SHA"
    
    // 2011,"Delaware","Brandon Hinkle",1.59,1,0,
    // 13,0,0,0,0,0,
    // 5.2,6,2,1,5,4,1,0,0,23,
    // .261,2,0,0,1,0
    
    // Boydsworld Players: Batting
    
    // "Year","Team","Player","AVG","GP","GS","AB","R","H",
    // "2B","3B","HR","RBI","TB","SLG",
    // "BB","HBP","SO","GIDP","OBP"
    // "SF","SH","SB","ATT","PO","A"
    
    // 2010,"Florida State","Justin Gonzalez",.287,46,21,94,24,27
    // ,4,2,2,21,41,.436,
    // 19,4,31,1,.424,
    // 1,2,2,2,37,66
    
    // Boydsworld Team: Batting
    
    // "Year","Team","AVG","GP","GS","AB","R",
    // "H","2B","3B","HR","RBI","TB","SLG",
    // "BB","HBP","SO","GIDP","OBP",
    // "SF","SH","SB","ATT"

    public function __construct($year = null)
    {
        parent::__construct();
        
        if ($year) {
            $this->setYear($year);
        }
    }
    
    public function getLevel()
    {
        return (string) $this->level;
    }
    
    public function setLevel($level)
    {
        $this->level = (string) $level;
    }
    
    public function getLeague()
    {
        return (string) $this->league;
    }
    
    public function setLeague($league)
    {
        $this->league = (string) $league;
    }
    
    public function getDivision()
    {
        return (string) $this->division;
    }
    
    public function setDivision($division)
    {
        $this->division = (string) $division;
    }
    
    public function hasConference()
    {
        return (bool) $this->conference;
    }
    
    public function getConference()
    {
        if (!$this->conference) {
            return null;
        }
    
        $results = $this->mongo
            ->get_dbref($this->conference);
            
        $this->load->model('conferenceRepository', '_conference');
            
        return $this->_conference->assign($results);
    }
    
    public function setConference($conference)
    {
        if (is_object($conference)) {
            $conference = $this->mongo
                ->create_dbref('conference', $conference->getId());
        }
    
        $this->conference = $conference;
    }
    
    public function getIsConferenceChampion()
    {
        return (bool) $this->isConferenceChampion;
    }
    
    public function setIsConferenceChampion($isConferenceChampion)
    {
        $this->isConferenceChampion = (bool) $isConferenceChampion;
    }
    
    public function getSource()
    {
        return (string) $this->source;
    }
    
    public function setSource($source)
    {
        $this->source = (string) $source;
    }
    
    public function unsetSource()
    {
        unset($this->source);
    }
    
    public function isOfficial()
    {
        $official = ($this->source == 'Smallball') ? true : false;
       
        return (bool) $official;
    }
    
    public function getTeamRosterUrl()
    {
        return (string) $this->teamRosterUrl;
    }
    
    public function setTeamRosterUrl($teamRosterUrl)
    {
        $this->teamRosterUrl = (string) $teamRosterUrl;
    }
    
    public function getTeamScheduleUrl()
    {
        return (string) $this->teamScheduleUrl;
    }
    
    public function setTeamScheduleUrl($teamScheduleUrl)
    {
        $this->teamScheduleUrl = (string) $teamScheduleUrl;
    }
    
    public function getTeamStatsUrl()
    {
        return (string) $this->teamStatsUrl;
    }
    
    public function setTeamStatsUrl($teamStatsUrl)
    {
        $this->teamStatsUrl = (string) $teamStatsUrl;
    }
        
    public function getYear()
    {
        return (int) $this->year;
    }
    
    public function setYear($year)
    {
        $this->year = (int) $year;
    }
    
    public function getTeam()
    {
        if (!$this->team) {
            return null;
        }
    
        if (gettype($this->team) == 'string') {
            return $this->team;
        }
    
        $results = $this->mongo
            ->get_dbref($this->team);
            
        $this->load->model('teamRepository', '_team');
            
        return $this->_team->assign($results);
    }
    
    public function setTeam($team)
    {
        if (is_object($team)) {
            $team = $this->mongo
                ->create_dbref('team', $team->getId());
        }
    
        $this->team = $team;
    }
    
    public function unsetTeam()
    {
        unset($this->team);
    }
    
    public function getCoach()
    {
        if (!$this->coach) {
            return null;
        }
        
        if (gettype($this->coach) == 'string') {
            return $this->coach;
        }
    
        $results = $this->mongo
            ->get_dbref($this->coach);
            
        $this->load->model('coachRepository', '_coach');
            
        return $this->_coach->assign($results);
    }
    
    public function setCoach($coach)
    {
        if (is_object($coach)) {
            $coach = $this->mongo
                ->create_dbref('coach', $coach->getId());
        }
    
        $this->coach = $coach;
    }
    
    public function getPlayer()
    {
        if (!$this->player) {
            return null;
        }
        
        if (gettype($this->player) == 'string') {
            return $this->player;
        }
        
        $results = $this->mongo
            ->get_dbref($this->player);
            
        $this->load->model('playerRepository', '_player');
            
        return $this->_player->assign($results);
    }
    
    public function setPlayer($player)
    {
        if (is_object($player)) {
            $player = $this->mongo
                ->create_dbref('player', $player->getId());
        }
    
        $this->player = $player;
    }
    
    public function getPlayers($sort = null)
    {
        if (!$this->players) {
            return null;
        }
    
        $players = array();
        
        $this->load->model('playerRepository', '_player');
    
        foreach ($this->players as $player) {
        
            $player = $this->mongo
                ->get_dbref($player);
        
            $player = $this->_player->assign($player);
            $players[] = $player;
        }
        
        if ($sort) {
            foreach ($players as $player) {
                
            }
        }
    
        return $players;
    }
    
    public function setPlayers($players)
    {
        foreach ($players as $player) {
            if ($player->getId()) {
                $this->addPlayer($player);
            }
        }
        
        return $this->getPlayers();
    }
    
    public function addPlayer($player)
    {
        if ($this->hasPlayer($player)) {
            return true;
        }
    
        if (is_object($player) and $player->getId()) {
            $player = $this->mongo
                ->create_dbref('player', $player->getId());
        } else {
            return false;
        }
        
        array_push($this->players, $player);
    
        return $this->getPlayers();
    }
    
    public function unsetPlayer()
    {
        unset($this->player);
    }
    
    public function removePlayers($seasonFromPlayer = false)
    {
        foreach ($this->players as $player) {
            $this->removePlayer($player, $seasonFromPlayer);
        }
        
        return $this->getPlayers();
    }
    
    public function removePlayer($player, $seasonFromPlayer = true)
    {
        if (!$this->hasPlayer($player)) {
            return true;
        }
    
        if (is_object($player)) {
            $playerRef = $this->mongo
                ->create_dbref('player', $player->getId());
            $playerObj = $player;
        } else {
            $playerRef = $player;
            $playerObj = $this->mongo
                ->get_dbref($playerRef);
            $this->load->model('playerrepository', '_player');
            $playerObj = $this->_player->assign($playerObj);
        }
        
        foreach ($this->players as $key => $existing) {
            if ($existing == $playerRef) {
                unset($this->players[$key]);
            }
        }
        
        if ($seasonFromPlayer) {
            $playerObj->removeSeason($this->getYear());
            $playerObj->save();
        }
    
        return $this->getPlayers();
    }
    
    public function hasPlayer($player)
    {
        if (is_object($player)) {
            $player = $this->mongo
                ->create_dbref('player', $player->getId());
        }
    
        foreach ($this->players as $existing) {
            if ($existing == $player) {
                return true;
            }
        }
        
        return false;
    }
    
    public function getPlayersNcaaIdPercentage()
    {
        $i = 0;
        $players = $this->getPlayers();
        
        foreach ($players as $player) {
            if ($player->getNcaaId()) {
                $i++;
            };
        }
        
        return number_format(($i/count($players))* 100);
    }
    
    public function getIsTeam()
    {
        return (bool) $this->isTeam;
    }
    
    public function setIsTeam($isTeam)
    {
        $this->isTeam = (bool) $isTeam;
    }
    
    public function getIsPlayer()
    {
        return (bool) $this->isPlayer;
    }
    
    public function setIsPlayer($isPlayer)
    {
        $this->isPlayer = (bool) $isPlayer;
    }
    
    public function hasTeam()
    {
        return ($this->team) ? true : false;
    }
    
    public function getClass()
    {
        return $this->class;
    }
    
    public function setClass($class)
    {
        $class = str_replace('.', '', $class);
    
        $this->class = trim($class);
    }
    
    public function getPosition()
    {
        return $this->position;
    }
    
    public function setPosition($position)
    {
        $this->position = $position;
    }
    
    public function getGames()
    {
        if ($this->games) {
            return (int) $this->games;
        }
        
        return $this->getGamesPlayed();
    }
    
    public function setGames($games)
    {
        $this->games = (int) $games;
    }
    
    public function hasGames()
    {
        $this->load->model('GameRepository', '_game');
    
        return (bool) $this->_game->findAllForTeamAndYear($this->getTeam(), $this->getYear());
    }
    
    public function getGamesPlayed()
    {
        return (int) $this->gamesPlayed;
    }
    
    public function setGamesPlayed($gamesPlayed)
    {
        $this->gamesPlayed = (int) $gamesPlayed;
    }
    
    public function getGamesStarted()
    {
        return (int) $this->gamesStarted;
    }
    
    public function setGamesStarted($gamesStarted)
    {
        $this->gamesStarted = (int) $gamesStarted;
    }
    
    public function getAtBats()
    {
        return (int) $this->atBats;
    }
    
    public function setAtBats($atBats)
    {
        $this->atBats = (int) $atBats;
    }
    
    public function getBattingAverage()
    {
        $this->load->library('Stats/BattingAverage');
        
        return $this->battingaverage->getSeason($this);
    }

    public function getAvg()
    {
        return $this->getBattingAverage();
    }
    
    public function getRuns()
    {
        return (int) $this->runs;
    }
    
    public function setRuns($runs)
    {
        $this->runs = (int) $runs;
    }
    
    public function getHits()
    {
        return (int) $this->hits;
    }
    
    public function setHits($hits)
    {
        $this->hits = (int) $hits;
    }
    
    public function getSingles()
    {
        return $this->getHits() - $this->getDoubles() - $this->getTriples() - $this->getHomeRuns();
    }
    
    public function getDoubles()
    {
        return (int) $this->doubles;
    }
    
    public function setDoubles($doubles)
    {
        $this->doubles = (int) $doubles;
    }
    
    public function getTriples()
    {
        return (int) $this->triples;
    }
    
    public function setTriples($triples)
    {
        $this->triples = (int) $triples;
    }
    
    public function getHomeRuns()
    {
        return (int) $this->homeRuns;
    }
    
    public function setHomeRuns($homeRuns)
    {
        $this->homeRuns = (int) $homeRuns;
    }
    
    public function getSluggingPercentage()
    {
        $this->load->library('Stats/SluggingPercentage');
        
        return $this->sluggingpercentage->getSeason($this);
    }
    
    public function getTotalBases()
    {
        return (int) $this->totalBases;
    }
    
    public function setTotalBases($totalBases)
    {
        $this->totalBases = (int) $totalBases;
    }
    
    public function getRunsBattedIn()
    {
        return (int) $this->runsBattedIn;
    }
    
    public function setRunsBattedIn($runsBattedIn)
    {
        $this->runsBattedIn = (int) $runsBattedIn;
    }
    
    public function getStolenBases()
    {
        return (int) $this->stolenBases;
    }
    
    public function setStolenBases($stolenBases)
    {
        $this->stolenBases = (int) $stolenBases;
    }
    
    public function getStolenBaseAttempts()
    {
        return (int) $this->stolenBaseAttempts;
    }
    
    public function setStolenBaseAttempts($stolenBaseAttempts)
    {
        $this->stolenBaseAttempts = (int) $stolenBaseAttempts;
    }
    
    public function getCaughtStealing()
    {
        return $this->getStolenBaseAttemps() - $this->getStolenBases();
    }
    
    public function getWalks()
    {
        return (int) $this->walks;
    }
    
    public function setWalks($walks)
    {
        $this->walks = (int) $walks;
    }
    
    public function getWalksPercentage()
    {
        // (BB/(AB+BB))
        if ($this->getAtBats() || $this->getWalks()) {
            return number_format($this->getWalks() / ($this->getAtBats() + $this->getWalks()), 3);
        }
        
        return null;
    }
    
    public function getStrikeouts()
    {
        return (int) $this->strikeouts;
    }
    
    public function setStrikeouts($strikeouts)
    {
        $this->strikeouts = (int) $strikeouts;
    }
    
    public function getGroundedIntoDoublePlays()
    {
        return (int) $this->groundedIntoDoublePlays;
    }
    
    public function setGroundedIntoDoublePlays($groundedIntoDoublePlays)
    {
        $this->groundedIntoDoublePlays = (int) $groundedIntoDoublePlays;
    }
    
    public function getPutouts()
    {
        return (int) $this->putouts;
    }
    
    public function setPutouts($putouts)
    {
        $this->putouts = (int) $putouts;
    }
    
    public function getAssists()
    {
        return (int) $this->assists;
    }
    
    public function setAssists($assists)
    {
        $this->assists = (int) $assists;
    }
    
    public function getStrikeoutPercentage()
    {
        // SO/PA
    
        if ($this->getPlateAppearences()) {
            return number_format(($this->getStrikeouts() / $this->getPlateAppearances()), 3);
        }
        
        return null;
    }
    
    public function getHitByPitch()
    {
        return (int) $this->hitByPitch;
    }
    
    public function setHitByPitch($hitByPitch)
    {
        $this->hitByPitch = (int) $hitByPitch;
    }
    
    public function getSacrificeHits()
    {
        return (int) $this->sacrificeHits;
    }
    
    public function setSacrificeHits($sacrificeHits)
    {
        $this->sacrificeHits = (int) $sacrificeHits;
    }
    
    public function getSacrificeFlies()
    {
        return (int) $this->sacrificeFlies;
    }
    
    public function setSacrificeFlies($sacrificeFlies)
    {
        $this->sacrificeFlies = (int) $sacrificeFlies;
    }
    
    public function getBatterPlateAppearences()
    {
        // AB + BB + HBP + SH + SF
        return $this->getAtBats() + $this->getWalks() + $this->getHitByPitch() + $this->getSacrificeHits() + $this->getSacrificeFlies();
    }
    
    public function getBattedBallsInPlay()
    {
        // (TBF or PA) - SO - BB - HBP - HR
        return $this->getBatterPlateAppearances() - $this->getStrikeouts() - $this->getWalks() - $this->getHitByPitch() - $this->getHomeRuns();
    }
    
    public function getContactPercentage()
    {
        $this->load->library('Stats/ContactPercentage');
        
        return $this->contactpercentage->getSeason($this);
    }
    
    public function getOnBasePercentage()
    {
        $this->load->library('Stats/OnBasePercentage');
        
        return $this->onbasepercentage->getSeason($this);
    }
    
    public function getOBPA()
    {
        return $this->getOnBasePercentage();
    }
    
    public function getBattingAverageOnBallsInPlay()
    {
        $this->load->library('Stats/BattingAverageOnBallsInPlay');
        
        return $this->battingaverageonballsinplay->getSeason($this);
    }
    
    public function getBABIP()
    {
        return $this->getBattingAverageOnBallsInPlay();
    }
    
    public function getOnBasePlusSlugging()
    {
        return $this->getOnBasePercentage() + $this->getSluggingPercentage();
    }
    
    public function getIsolatedPower()
    {
        $this->load->library('Stats/IsolatedPower');
        
        return $this->isolatedpower->getSeason($this);
    }
    
    public function getISO()
    {
        return $this->getIsolatedPower();
    }
    
    public function getPowerVsSpeed()
    {
        $this->load->library('Stats/PowerVsSpeed');
        
        return $this->powervsspeed->getSeason($this);
    }
    
    public function getAppearances()
    {
        return (int) $this->appearances;
    }
    
    public function setAppearances($appearances)
    {
        $this->appearances = (int) $appearances;
    }
    
    public function getCompleteGames()
    {
        return (int) $this->completeGames;
    }
    
    public function setCompleteGames($completeGames)
    {
        $this->completeGames = (int) $completeGames;
    }
    
    public function getWins()
    {
        if ($this->getIsTeam() && !isset($this->wins)) {
            return $this->updateWinsAndLosses();
        }
    
        return (int) $this->wins;
    }
    
    public function setWins($wins)
    {
        $this->wins = (int) $wins;
    }
    
    public function updateWinsAndLosses($wins = null)
    {
        if ($wins) {
            $this->setWins($wins);
        }
        
        $this->load->model('gameRepository', '_game');
        $games = $this->_game->findAllForTeamAndYear($this->getTeam(), $this->getYear());
        
        if ($games) {
            $wins = 0;
            $losses = 0;
            
            foreach ($games as $game) {
                if ($game->isWin($this->getTeam())) {
                    $wins++;
                } else {
                    $losses++;
                }
            }
            
            $season = $this->getRepository()->findOneBySourceAndTeamAndYear('Smallball', $this->getTeam(), $this->getYear());
            
            $season->setWins($wins);
            $season->setLosses($losses);
            $season->save();
        }
    }
    
    public function getLosses()
    {
        return (int) $this->losses;
    }
    
    public function setLosses($losses)
    {
        $this->losses = (int) $losses;
    }
    
    public function getSaves()
    {
        return (int) $this->saves;
    }
    
    public function setSaves($saves)
    {
        $this->saves = (int) $saves;
    }
    
    public function getShutouts()
    {
        return (int) $this->shutouts;
    }
    
    public function setShutouts($shutouts)
    {
        $this->shutouts = (int) $shutouts;
    }
    
    public function getCombinedShutouts()
    {
        return (int) $this->combinedShutouts;
    }
    
    public function setCombinedShutouts($combinedShutouts)
    {
        $this->combinedShutouts = (int) $combinedShutouts;
    }
    
    public function getInningsPitched()
    {
        return (float) $this->inningsPitched;
    }
    
    public function setInningsPitched($inningsPitched)
    {
        $this->inningsPitched = (int) $inningsPitched;
    }
    
    public function getHitsAllowed()
    {
        return (int) $this->hitsAllowed;
    }
    
    public function setHitsAllowed($hitsAllowed)
    {
        $this->hitsAllowed = (int) $hitsAllowed;
    }
    
    public function getRunsAllowed()
    {
        return (int) $this->runsAllowed;
    }
    
    public function setRunsAllowed($runsAllowed)
    {
        $this->runsAllowed = (int) $runsAllowed;
    }
    
    public function getEarnedRuns()
    {
        return (int) $this->earnedRuns;
    }
    
    public function setEarnedRuns($earnedRuns)
    {
        $this->earnedRuns = (int) $earnedRuns;
    }
    
    public function getWalksAllowed()
    {
        return (int) $this->walksAllowed;
    }
    
    public function setWalksAllowed($walksAllowed)
    {
        $this->walksAllowed = (int) $walksAllowed;
    }
    
    public function getStrikeoutsPitched()
    {
        return (int) $this->strikeoutsPitched;
    }
    
    public function setStrikeoutsPitched($strikeoutsPitched)
    {
        $this->strikeoutsPitched = (int) $strikeoutsPitched;
    }
    
    public function getDoublesAllowed()
    {
        return (int) $this->doublesAllowed;
    }
    
    public function setDoublesAllowed($doublesAllowed)
    {
        $this->doublesAllowed = (int) $doublesAllowed;
    }
    
    public function getTriplesAllowed()
    {
        return (int) $this->triplesAllowed;
    }
    
    public function setTriplesAllowed($triplesAllowed)
    {
        $this->triplesAllowed = (int) $triplesAllowed;
    }
    
    public function getHomeRunesAllowed()
    {
        return (int) $this->homeRunsAllowed;
    }
    
    public function setHomeRunsAllowed($homeRunsAllowed)
    {
        $this->homeRunsAllowed = (int) $homeRunsAllowed;
    }
    
    public function getBattersFaced()
    {
        return (int) $this->battersFaced;
    }
    
    public function setBattersFaced($battersFaced)
    {
        $this->battersFaced = (int) $battersFaced;
    }
    
    public function getBattersHitByPitch()
    {
        return (int) $this->battersHitByPitch;
    }
    
    public function setBattersHitByPitch($battersHitByPitch)
    {
        $this->battersHitByPitch = (int) $battersHitByPitch;
    }
    
    public function getBalks()
    {
        return (int) $this->balks;
    }
    
    public function setBalks($balks)
    {
        $this->balks = (int) $balks;
    }
    
    public function getWildPitches()
    {
        return (int) $this->wildPitches;
    }
    
    public function setWildPitches($wildPitches)
    {
        $this->wildPitches = (int) $wildPitches;
    }
    
    public function getBattingAverageAgainst()
    {
        return (float) $this->battingAverageAgainst;
    }
    
    public function getBAA()
    {
        return $this->getBattingAverageAgainst();
    }
    
    public function setBattingAverageAgainst($battingAverageAgainst)
    {
        $this->battingAverageAgainst = (float) $battingAverageAgainst;
    }
    
    public function getSacrificeFliesAgainst()
    {
        return (int) $this->sacrificeFliesAgainst;
    }
    
    public function setSacrificeFliesAgainst($sacrificeFliesAgainst)
    {
        $this->sacrifiesFliesAgainst = (int) $sacrificeFliesAgainst;
    }
    
    public function getSacrificeHitsAgainst()
    {
        return (int) $this->sacrificeHitsAgainst;
    }
    
    public function setSacrificeHitsAgainst($sacrificeHitsAgainst)
    {
        $this->sacrificeHitsAgainst = (int) $sacrificeHitsAgainst;
    }

    
    public function getWalksAllowedPerStrikeouts()
    {
        if ($this->getStrikoutsPitched()) {
            return $this->getWalksAllowed() / $this->getStrikeoutsPitched();
        }
        
        return null;
    }
    
    public function getEarnedRunAverage()
    {
        $this->load->library('Stats/EarnedRunAverage');
        
        return $this->earnedrunaverage->getSeason($this);
    }
    
    public function getERA()
    {
        return $this->getEarnedRunAverage();
    }
    
    public function getWalksPlusHitsPerInningsPitched()
    {
        $this->load->library('Stats/WHIP');
        
        return $this->whip->getSeason($this);
    }
    
    public function getWHIP()
    {
        return $this->getWalksPlusHitsPerInningsPitched();
    }
    
    public function getLeftOnBasePercentage()
    {
        // http://www.hardballtimes.com/main/article/ten-things-i-didnt-know-a-couple-of-weeks-ago/
        // (H+BB+HBP-R)/(H+BB+HBP-(1.4*HR))
        
        // Missing pitching statistic for HBP
        
        return null;
    }
    
    public function getLOBPercentage()
    {
        return $this->getLeftOnBasePercentage();
    }
    
    public function setBattingAverageStatic($battingAverageStatic)
    {
        $this->battingAverageStatic = (float) $battingAverageStatic;
    }
    
    public function getBattingAverageStatic()
    {
        return (float) $this->battingAverageStatic;
    }
    
    public function getSluggingPercentageStatic()
    {
        return (float) $this->sluggingPercentageStatic;
    }
    
    public function setSluggingPercentageStatic($sluggingPercentageStatic)
    {
        $this->sluggingPercentageStatic = (float) $sluggingPercentageStatic;
    }
    
    public function getOnBasePercentageStatic()
    {
        return (float) $this->onBasePercentageStatic;
    }
    
    public function setOnBasePercentageStatic($onBasePercentageStatic)
    {
        $this->onBasePercentageStatic = (float) $onBasePercentageStatic;
    }
    
    public function getEarnedRunAverageStatic()
    {
        return (float) $this->earnedRunAverageStatic;
    }
    
    public function setEarnedRunAverageStatic($earnedRunAverageStatic)
    {
        $this->earnedRunAverageStatic = (float) $earnedRunAverageStatic;
    }
    
    public function sortSeasons($m, $n) {
        if ($m->getYear() == $n->getYear()) {
            return 0;
        }
    
        return ($m->getYear() > $n->getYear()) ? -1 : 1;
    }
    
    public function save($set = null)
    {
        parent::save($set);
        
        if ($this->isOfficial()) {
        
            $this->load->model('SeasonRepository', '_season');
            $season = $this->_season->findOneById($this->getId());
            
            $season->unsetSource();
            $season->unsetId();
            
            if ($season->getIsTeam()) {
                $team = $season->getTeam();
                $season->unsetTeam();
                $team->addSeason($season, true);
                $team->save();
            }
            
            if ($season->getIsPlayer()) {
                $player = $season->getPlayer();
                $season->unsetPlayer();
                $player->addSeason($season, true);
                $player->save();
            }

        }
        
        return $this;
    }
    
    /*
    static function sortSeasonsByGames($m, $n)
    {
        if ($m->getGames() == $n->getGames()) {
            return 0;
        }
    
        return ($m->getGames() > $n->getGames()) ? -1 : 1;
    }
    */
    
}