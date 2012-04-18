<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class TeamRepository extends DocumentRepository {
    
    public function __construct()
    {
        parent::__construct();
        $this->collection = 'team';
    }
    
    public function findAll($sortBy = 'name', $limit = null, $offset = null, $select = null)
    {
        $this->benchmark->query('FindAllTeams:');
        $documents = parent::findAll($sortBy, $limit, $offset, $select);
        $this->benchmark->query('FindAllTeams:');
        
        return $documents;
    }
    
    public function count()
    {
        $this->benchmark->query('CountAllTeams:');
        $count = parent::count();
        $this->benchmark->query('CountAllTeams:');
        
        return $count;
    }
    
    public function findOneById($id, $select = null)
    {
        $this->benchmark->query('FindOneById: Team: '.$id);
        $document = parent::findOneById($id, $select);
        $this->benchmark->query('FindOneById: Team: '.$id);
        
        if (!$document) {
            return null;
        }
        
        return $document;        
    }
    
    public function findOneBySlug($slug)
    {
        $this->benchmark->query('FindOneBySlug: Team: '.$slug);
        $document = parent::findOneBySlug($slug);
        $this->benchmark->query('FindOneBySlug: Team: '.$slug);
        
        if ($document) {
            return $document;
        }
        
        $results = $this->mongo
            ->where(array(
                'alternateSlugs' => $slug
            ))
            ->get($this->collection);
            
        if ($results) {
            return $this->assign($results[0]);
        }
        
        return null;        
    }
    
    public function findOneByOfficialName($officialName)
    {        
        $results = $this->mongo
            ->where(array(
                'officialName' => $officialName
            ))
            ->get($this->collection);
            
        if ($results) {
            return $this->assign($results[0]);
        }
        
        return null;        
    }
    
    public function findOneByLahmanId($lahmanId)
    {        
        $results = $this->mongo
            ->where(array(
                'lahmanId' => $lahmanId
            ))
            ->get($this->collection);
            
        if ($results) {
            return $this->assign($results[0]);
        }
        
        return null;        
    }
    
    public function findOneByNcaaId($ncaaId)
    {
        $this->benchmark->query('FindOneBySlug: Team: '.$ncaaId);
        $results = $this->mongo
            ->where(array(
                'ncaaId' => $ncaaId
            ))
            ->get($this->collection);
        $this->benchmark->query('FindOneBySlug: Team: '.$ncaaId);
        
        if ($results) {
            return $this->assign($results[0]);
        }
        
        return null;        
    }
    
    public function findAllByConference($id)
    {
        $ref = $this->mongo
                ->create_dbref('conference', $id);
    
        $this->benchmark->query('FindAllByConference: '.$id);
        
        $documents = $this->mongo
            ->where(array(
                'conference' => $this->mongo
                    ->create_dbref('conference', $id)
            ))
            ->get($this->collection);
        
        /*
        $documents = $this->collection->find(array(
            'conference' => $ref
        ));
        */
        
        $this->benchmark->query('FindAllByConference: '.$id);
        
        $response = array();
        
        foreach ($documents as $document) {
            $response[] = $this->assign($document);
        }
        
        return $response;
    }
    
    public function findAllInDivision($level, $league, $division, $year = null)
    {
        $year = ($year) ? $year : date('Y');
        
        $documents = $this->mongo
            ->order_by(array('name' => 1))
            ->where(array(
                'seasons.level' => $level,
                'seasons.league' => $league, 
                'seasons.division' => $division,
                'seasons.year' => $year
            ))
            ->get($this->collection);
        
        $response = array();
        
        foreach ($documents as $document) {
            $response[] = $this->assign($document);
        }
        
        return $response;
    }
    
    public function findAllForHomepage()
    {
        $documents = $this->mongo
            ->order_by(array('name' => 1))
            ->where(array(
                'seasons.level' => 'College',
                'seasons.league' => 'NCAA', 
                'seasons.division' => 'D1',
                'seasons.year' => 2011
            ))
            ->select(array('name'))
            ->get($this->collection);
        
        $response = array();
        
        foreach ($documents as $document) {
            $response[] = $this->assign($document);
        }
        
        return $response;
    }
    
    public function saveTeamsForConference($teams, $conference)
    {
        $documents = array();
    
        foreach ($teams as $team) {
            $slug = $this->slugify->simple($team['name']);
            
            $document = $this->findOneBySlug($slug);
            
            if (!$document) {
                $document = new Team();
                $document->setSlug($slug);
                $document->setName($team['name']);
                $document->setNickname($team['nickname']);
                $document->setConference($conference);
                $document->save();
            }
            
            $documents[] = $document;
        }
        
        return $documents;
    }
    
    public function createByName($name)
    {
        print_r('['.$name.']');
        
        $document = new Team();
        $document->setName($name);
        $document->setSlug();
        $document->save();
        
        $document = $this->findOneBySlug($document->getSlug());
                
        return $document;
    }
    
    public function getSeasonForTeamWithSlug($slug, $year)
    {
        $this->benchmark->query('getSeasonForTeamWithSlug: '.$slug.', '.$year);
        
        $results = $this->mongo
            ->where(array(
                'slug' => $slug,
                'seasons.year' => $year
            ))
            ->get($this->collection);
        
        $this->benchmark->query('getSeasonForTeamWithSlug: '.$slug.', '.$year);
        
        if ($results) {
            $team = $this->assign($results[0]);
            
            return $team->getSeasonByYear($year);
        }
        
        return null;
    }
    
    public function findSeasonsWithCoach($coach)
    {
        $this->benchmark->query('getSeasonsWithCoach');
        
        $ref = $this->mongo
            ->create_dbref('coach', $coach->getId());
            
        $documents = $this->mongo
            ->where(array(
                'seasons.coach' => $ref,
            ))
            ->get($this->collection);
        
        $this->benchmark->query('getSeasonsWithCoach');
        
        $response = array();
        
        foreach ($documents as $document) {
            $team = $this->assign($document);
            foreach ($team->getSeasons() as $season) {
                if ($season->getCoach() == $coach) {
                    $season->setTeam($team);
                    $response[] = $season;
                }
            }
        }
        
        return $response;
    }
    
    public function assign($document)
    {
        return parent::assign($document, new Team());
    }
    
}