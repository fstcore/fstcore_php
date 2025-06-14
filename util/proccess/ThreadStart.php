<?php

class ThreadGroup
{
    private $threadpool;

    function __construct()
    {
        $this->threadpool = array();
    }

    public function add($threadpool)
    {
        $this->threadpool = $threadpool;
    }

    public function start()
    {
        foreach ($this->threadpool as $thread) {
            $thread->start();
        }
    }

    public function join()
    {
        foreach ($this->threadpool as $thread) {
            $thread->join();
        }
    }

    public function active_count()
    {
        $actives = 0;
        foreach ($this->threadpool as $thread) {
            if ($thread->isRunning()) {
                $actives += 1;
            }
        }
        return $actives;
    }

    public function get_state()
    {
        $states = [];
        foreach ($this->threadpool as $thread) {
            $states[] = $thread->getState();
        }
        return $states;
    }
}

class SyncronTask
{
    function __construct($e = null)
    {
        /*if (NoSQL.INJECT_HREF.isSelected()) {
            new HrefParse(e);
        }
        if (NoSQL.INJECT_FORM.isSelected()) {
            new FormParse(e);
        }*/
    }
}

class StartThread /*extends Thread*/
{

    private $syncrontask;
    private $e;

    public function __construct(SyncronTask $syncrontask, $e)
    {
        $this->syncrontask = $syncrontask;
        $this->e = $e;
    }

    public function run()
    {
        $this->syncrontask($this->e);
    }
}

class ThreadStart
{

    //DEFAULT
    private $listdata;
    private $max;
    private $threadgroup;
    private $threadpool;
    private $stop = false;

    //THREAD INFO
    private $log = false;
    private $info = array();
    private $bar = null;
    private $data_progress = 0;
    public $debug;
    public $fstcore;

    function __construct()
    {
        $this->listdata = array();
        $this->max = 5;
        $this->threadgroup = new ThreadGroup();
        $this->threadpool = array();
        $this->log = false;
        $this->info = array();
        $this->bar = array();
        $this->data_progress = 0;
    }

    function interruptcheck()
    {
        return $this->stop;
    }

    public function set_data($listdata)
    {
        $this->listdata = $listdata;
    }

    public function stop($stop)
    {
        $this->stop = $stop;
    }

    public function set_max($max)
    {
        $this->max = $max;
    }

    private function run_()
    {
        while (!(self::interruptcheck())) {
            if ($this->threadgroup->active_count() < $this->max) {
                $this->threadgroup->start();
            } else {
                break;
            }
            if (self::interruptcheck()) {
                self::join_();
            }
            break;
        }
    }

    private function join_()
    {
        if ((($this->threadgroup != null) && ($this->threadgroup->active_count() >= 1)) || (self::interruptcheck())) {
            $this->threadgroup->join();
            unset($this->threadpool);
        }
    }

    private function prepare($d)
    {
        $e = new StartThread(new SyncronTask(), $d);
        array_push($this->threadpool, $e);
        $this->threadgroup->add($this->threadpool);
    }

    private function start()
    {
        while (!(self::interruptcheck())) {
            $left = (count($this->listdata) % $this->max);
            $init = (count($this->listdata) / $this->max);
            $count = 0;
            for ($xinit = 1; $xinit <= $init; $xinit++) {
                if ((count($this->listdata)) == $count || $count <= (count($this->listdata))) {
                    $data = array();
                    for ($i = 0; $i < $this->max; $i++) {
                        array_push($data, $this->listdata[$count]);
                        $count += 1;
                    }
                    for ($i = 0; $i < count($data); $i++) {
                        self::prepare($data[$i]);
                    }
                    self::run_();
                    self::join_();
                } else {
                    break;
                }
            }
            self::join_();
            if ($left != 0) {
                $data = array();
                for ($i = 0; $i < $left; $i++) {
                    if (!empty($this->listdata[$count])) {
                        array_push($data, $this->listdata[$count]);
                        $count += 1;
                    } else {
                        break;
                    }
                }
                for ($i = 0; $i < count($data); $i++) {
                    self::prepare($data[$i]);
                }
                self::run_();
                self::join_();
            }
            break;
        }
        self::join_();
    }

    public function run()
    {
        while (!(self::interruptcheck())) {
            self::start();
            try {
                sleep(3);
            } catch (Exception $e) {
                echo (string) $e;
            }
            break;
        }
    }
}
