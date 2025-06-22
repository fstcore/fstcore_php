<?php

/*abstract class SocketCallback {
    /*abstract public function onOpen(string $client_ip, string $data): void;
    abstract public function onClose(string $client_ip, string $data): void;
    abstract public function onError(string $client_ip, string $data): void;
    abstract public function onRead(string $client_ip, string $data_byte): void;*/
}*/

class Socket
{

    private $server;
    private $clients = array();
    private $callback;
    public $run = true;
    private $buffer_size = 32;
    public $debug;
    public $fstcore;

    function __construct($val=null)
    {
    }

    private function set_callback($callback) {
        $this->callback = $callback;
    }

    private function get_info($client_){
        $data_array = array();
        socket_getpeername($client_, $remote_ip, $remote_port);
        $data_array['ip'] = $remote_ip;
        $data_array['port'] = $remote_port;

        $this->clients[$remote_ip] = array('ip' => $remote_ip, 'port' => $remote_port, 'socket' => $client_);
        return $remote_ip;
    }

    public function connect($host, $port){
        socket_connect($socket, $host, $port);
        $client_ip_ = self::get_info($socket);
        return $client_ip_;
    }

    public function listen($host, $port)
    {
        $this->server = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_set_option($this->server, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_bind($this->server, $host, $port);
        socket_listen($this->server, 5);
        self::accept();
    }

    private function accept($server)
    {
        while ($this->run) {
            $client_ = socket_accept($server);
            $client_ip_ = self::get_info($client_);
            self::socket_handle($client_ip_);
        }
        $server.close();
        foreach ($this->clients as $client_){
            self::close($client_);
        }
    }

    private function read_data($client_ip_)
    {
        $socket = $this->clients[$client_ip_]['socket'];
        //return socket_read($client, $buffer);
        $buffer_data = "";
        while(true) {
            $read = socket_recv($socket, $buf, $this->buffer_size, MSG_PEEK);
            if($read === false) {
                die("Could not read from socket\n");
            }
            if($read > 0) {
                $buffer_data .= $buf;
                if(strpos($buffer_data, "::") !== false) {
                    break;
                }
            }
        }
        return $buffer_data;
    }

    private function write_data($client_ip_, $data)
    {
        $socket = $this->clients[$client_ip_]['socket'];
        return socket_write($socket, $data, strlen($data));
    }

    private function close($client_ip_){
        $this->clients[$client_ip_]['socket'].close();
    }

}

?>