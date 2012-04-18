<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class InningRepository extends DocumentRepository {
    
    public function __construct()
    {
        parent::__construct();
        $this->collection = 'inning';
    }
    
    public function findAll($sort = 'datetime', $limit = null, $offset = null)
    {
        return parent::findAll($sort, $limit, $offset);
    }
    
    public function findAllUnverifiedByYear($year = null)
    {
        $range = $this->date->season($year);   
    
        $this->benchmark->query('findAllUnverifiedByYear: '.$year);
        $results = $this->mongo
            ->where(array(
                'urlVerified' => null,
            ))
            ->where_between('datetime', $range['start'], $range['finish'])
            ->order_by(array('datetime'=>1))
            ->get($this->collection);
        $this->benchmark->query('findAllUnverifiedByYear: '.$year);
            
        $games = array();
        
        foreach ($results as $game) {
            $game = $this->assign($game);
            array_push($games, $game);
        }
            
        return $games;
    }
    
    public function findAllUnverifiedForTeamByYear($team, $year = null)
    {
        $range = $this->date->season($year);   
    
        $ref = $this->mongo
            ->create_dbref('team', $team->getId());
    
        $this->benchmark->query('findAllUnverifiedForTeamByYear: '.$team->getName().' '.$year);
        $results = $this->mongo
            ->where(array(
                'urlVerified' => null,
            ))
            ->or_where(array(
                'homeTeam' => $ref,
                'awayTeam' => $ref,
            ))
            ->where_between('datetime', $range['start'], $range['finish'])
            ->order_by(array('datetime'=>1))
            ->get($this->collection);
        $this->benchmark->query('findAllUnverifiedForTeamByYear: '.$team->getName().' '.$year);
            
        $games = array();
        
        foreach ($results as $game) {
            $game = $this->assign($game);
            array_push($games, $game);
        }
            
        return $games;
    }
    
    public function findAllWithTeam($team, $sortBy = 'datetime')
    {
        $ref = $this->mongo
            ->create_dbref('team', $team->getId());
    
        $this->benchmark->query('FindAllWithTeam: '.$team->getName());
        $results = $this->mongo
            ->or_where(array(
                'homeTeam' => $ref,
                'awayTeam' => $ref,
            ))
            ->order_by(array('datetime'=>1))
            ->get($this->collection);
        $this->benchmark->query('FindAllWithTeam: '.$team->getName());
        
        $games = array();
        
        foreach ($results as $game) {
            $game = $this->assign($game);
            array_push($games, $game);
        }
    
        return $games;        
    }
    
    public function getCountAllWithTeam($team)
    {
        $ref = $this->mongo
            ->create_dbref('team', $team->getId());
    
        $this->benchmark->query('getCountAllWithTeam: '.$team->getName());
        $results = $this->mongo
            ->select('')
            ->or_where(array(
                'homeTeam' => $ref,
                'awayTeam' => $ref,
            ))
            ->get($this->collection);
        $this->benchmark->query('getCountAllWithTeam: '.$team->getName());
        
        return count($results);
    }
    
    public function findAllForTeamAndYear($team, $year)
    {
        $range = $this->date->season($year);
        
        return $this->findAllForTeamBetweenDates($team, $range['start'], $range['finish']);
    }
    
    public function findAllForTeamBetweenDates($team, $start, $finish, $sortBy = 'datetime')
    {
        $ref = $this->mongo
            ->create_dbref('team', $team->getId());

        $this->benchmark->query('FindAllForTeamBetweenDates: '.$team->getName());
        $results = $this->mongo
            ->where_between('datetime', $start, $finish)
            ->or_where(array(
                'homeTeam' => $ref,
                'awayTeam' => $ref,
            ))
            ->order_by(array('datetime'=>1))
            ->get($this->collection);
        $this->benchmark->query('FindAllForTeamBetweenDates: '.$team->getName());
        
        $games = array();
        
        foreach ($results as $game) {
            $game = $this->assign($game);
            array_push($games, $game);
        }
    
        return $games;        
    }
    
    public function findOneByDateWithTeamsAndScores($datetime, $homeTeam, $awayTeam, $homeScore, $awayScore)
    {
        /*
        print_r($datetime."\n");
        print_r("hometeam: \n");
        print_r($homeTeam->getTitle()."\n");
        print_r("awayteam: \n");
        print_r($awayTeam->getTitle()."\n");
        */
        
        $results = $this->mongo
            ->where(array(
                'datetime' => $datetime,
                'homeTeam' => $homeTeam,
                'awayTeam' => $awayTeam,
                'homeScore' => $homeScore,
                'awayScore' => $awayScore
            ))
            ->limit(1)
            ->get($this->collection);
        
        if (!$results) {
            return false;
        }
        
        return $this->assign($results[0]);        
    }
    
    public function saveGames($games)
    {
        $documents = array();
        $new = array();
    
        print_r("Saving ".count($games)." Game Listings\n");

        foreach ($games as $game) {
        
            $homeSlug = $this->slugify->simple($game['homeTeam']);
            $homeTeam = $this->_team->findOneBySlug($homeSlug);
            
            if (!$homeTeam) {
                $homeTeam = $this->_team->createByName($game['homeTeam']);
            }
            
            $homeTeam = $this->mongo
                ->create_dbref('team', $homeTeam->getId());
            
            $awaySlug = $this->slugify->simple($game['awayTeam']);
            $awayTeam = $this->_team->findOneBySlug($awaySlug);
            
            if (!$awayTeam) {
                $awayTeam = $this->_team->createByName($game['awayTeam']);
            }
            
            $awayTeam = $this->mongo
                ->create_dbref('team', $awayTeam->getId());
            
            //print_r($homeTeam);
            //print_r($awayTeam);
            
            $datetime = new MongoDate(strtotime($game['date']));
            
            $document = $this->findOneByDateWithTeamsAndScores($datetime, $homeTeam, $awayTeam, $game['homeScore'], $game['awayScore']);

            if (!$document) {
                $document = new Game();
                $document->setDatetime($datetime);
                $document->setHomeTeam($homeTeam);
                $document->setAwayTeam($awayTeam);
                $document->setHomeScore($game['homeScore']);
                $document->setAwayScore($game['awayScore']);
                $document->setUrl($game['url']);
                $document->save();
                
                array_push($new, $document);
            }
            
            if (!in_array($document, $documents)) {
                array_push($documents, $document);
            }
            
            print_r('.');
            
            //print_r(count($documents));
        }
        
        if (count($games)) {
            print_r("\n");
        }
        
        print_r("Added ".count($new)." New Games\n");
        
        return $documents;
    }
    
    public function assign($document)
    {
        return parent::assign($document, new Game());
    }
    
}