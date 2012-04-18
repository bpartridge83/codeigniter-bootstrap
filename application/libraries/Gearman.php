<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Gearman
{
    protected $CI;
    protected $_client;
    protected $_worker;
        
    public function __construct()
    {
        //$this->CI =& get_instance();
    }
        
    public function client($function, $params = null)
    {
        $this->_client = new GearmanClient();
        $this->_client->addServer();
        
        if (is_array($params)) {
            $params = json_encode($params);
        }

        $job_handle = $this->_client->doBackground($function, $params, md5($params));
        
        if ($this->_client->returnCode() != GEARMAN_SUCCESS)
        {
            //echo "bad return code\n";
        } else {
            //echo "success!";
        }
    }

    public function worker()
    {
        $this->_worker = new GearmanWorker();
        $this->_worker->addServer();
        $this->_worker->addFunction('mongo', array('Gearman', 'saveQuery'));
        
        print "Waiting for job...\n";
        while($this->_worker->work())
        {
            if ($this->_worker->returnCode() != GEARMAN_SUCCESS)
            {
                echo "return_code: " . $this->_worker->returnCode() . "\n";
                break;
            }
        }
    }
    
    public function saveQuery($job)
    {
        list($method, $query, $cache_file, $expires) = json_decode($job->workload(), true);

        $CI =& get_instance();
        $result = $CI->mongo->{$method}($query);
        
        print_r($query);
        
        print_r($result);
        print_r(sprintf("\n\n%s: Saving for %s minutes\n\n", date('h:i:s'), ($expires / 60)));
        
        $CI->cache->delete($cache_file);
        $CI->cache->write($result, $cache_file, $expires);
        
        return 'saved query';
    }
    
}