<?php
require_once($basedir.'/include/lib/util/filesystem/FileSystem.php');
require_once($basedir.'/include/lib/util/net/UserAgent.php');

class Http
{

  private $url;
  private $timeout;
  private $temp;
  private $useragent = null;
  private $proxy = null;
  private $custom_header = null;
  private $cookie_name;
  private $data = null;
  private $content_type = null;
  private $curl = null;
  public $debug;
  public $fstcore;

  function __construct($val = null)
  {
    $this->timeout = 30;
    $this->temp = dirname(__FILE__) . '/tmp/';
    $this->fstcore->util->filesystem->makefolder($this->temp);
    $this->cookie_name = md5(time()) . '.txt';
    $this->useragent = self::random_useragent();
    $this->curl = curl_init();
  }

  function random_useragent()
  {
    return $this->fstcore->util->useragent->random_useragent();
  }

  public function set_url($url)
  {
    $this->url = $url;
  }

  public function set_timeout($timeout)
  {
    $this->timeout = $timeout;
  }

  public function set_proxy($proxy)
  {
    $this->proxy = $proxy;
  }

  public function set_header($header)
  {
    $this->custom_header = $header;
  }

  public function set_content_type($content_type)
  {
    $this->content_type = $content_type;
  }

  public function set_data($data)
  {
    $this->data = $data;
  }

  public function request($type)
  {
    $data_array = array();
    curl_setopt($this->curl, CURLOPT_URL, $this->url);
    if ($this->proxy != null) {
      curl_setopt($this->curl, CURLOPT_PROXY, $this->proxy['ip']);
      curl_setopt($this->curl, CURLOPT_PROXYPORT, $this->proxy['port']);
      switch ($this->proxy['type']) {
        case 'socks5':
          curl_setopt($this->curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
          break;
        case 'http':
          curl_setopt($this->curl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
          break;
        case 'https':
          curl_setopt($this->curl, CURLOPT_PROXYTYPE, CURLPROXY_HTTPS);
          break;
        default:
          break;
      }
    }
    if ($this->custom_header != null) {
      if ($this->content_type != null) {
        $this->custom_header['Content-Type'] = $this->content_type['Content-Type'];
      }
      curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->custom_header);
    } else {
      if ($this->content_type != null) {
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->content_type);
      } else {
        curl_setopt($this->curl, CURLOPT_HEADER, 0);
      }
    }
    curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($this->curl, CURLOPT_USERAGENT, $this->useragent);
    curl_setopt($this->curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($this->curl, CURLOPT_COOKIEJAR, $this->temp.'/'.$this->cookie_name);
    curl_setopt($this->curl, CURLOPT_COOKIEFILE, $this->temp.'/'.$this->cookie_name);
    switch (strtolower($type)) {
      case 'get':
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'GET');
        break;
      case 'head':
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'HEAD');
        break;
      case 'delete':
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        break;
      case 'post':
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->data);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'POST');
        break;
      case 'put':
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->data);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        break;
      case 'profind':
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PROFIND');
        break;
      default:
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'GET');
        break;
    }
    $body = curl_exec($this->curl);
    $uri = curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL);
    curl_close($this->curl);
    $this->fs->deletefile($this->temp, $this->cookie_name);
    $data_array['code'] = 200;
    $data_array['header'] = '';
    $data_array['cookies'] = '';
    $data_array['uri'] = $uri;
    $data_array['body'] = $body;
    return $data_array;
  }

  function __destruct()
  {
  }
  
}
