<?php
require_once($basedir.'/include/lib/util/3rdparty.php');
require_once($basedir.'/include/lib/util/net/Http.php');
require_once($basedir.'/include/lib/util/text/Text.php');
require_once($basedir.'/include/lib/util/database/Database.php');

class Parser
{

    private $http;
    private $text;
    private $database;
    public $debug;
    public $fstcore;

    function __construct($val = null)
    {
        $this->http = new Http();
        $this->text = new Text();
        $this->database = new Database();
    }

    public function domain_parse($url)
    {
        $domain = $this->text->regex_replace_all('/\n+|\r+|\t+|\s+|https\:\/\/www\.|http\:\/\/www\.|https\:\/\/|http\:\/\/|www\.|(\/.*)|\:\d+|\:\d+/i', '', $url);
        return $domain;
    }

    public function check_subdomain($url)
    {
        $subdomain = self::domain_parse($url);
        $array_subdomain = $this->text->split('.', $subdomain);
        if (count($array_subdomain) > 3) {
            return true;
        } else {
            return false;
        }
    }

    public function url_structure($url)
    {
        $data_array = array();
        $domain = self::domain_parse($url);

        $this->http->set_url('https://' . $domain . '/');
        $this->http->set_timeout(10);
        $response = $this->http->request('get');

        $array_uri = explode('/', $response['uri']);

        $data_array['protocol'] = $array_uri[0];

        if ($this->text->is_existonstr(':', $array_uri[2])) {
            $array_domain = $this->text->split(':', $array_uri[2]);
            $data_array['domain'] = $array_domain[0];
            $data_array['port'] = $array_domain[1];
        } else {
            $data_array['domain'] = $array_uri[2];
        }

        if (count($array_uri) >= 3) {
            $type_query = false;
            for ($i = 3; $i < count($array_uri); $i++) {
                if ($this->text->regex_match('s/\?/i', $array_uri[$i]) || $type_query) {
                    $type_query = true;
                    if (!isset($data_array['query'])) {
                        $data_array['query'] = $array_uri[$i];
                    } else {
                        $data_array['query'] .= $array_uri[$i];
                    }
                } else if ($this->text->regex_match('s/\.\S+|\.php|\.html|\.asp|\.aspx/i', $array_uri[$i])) {
                    if (!isset($data_array['path'])) {
                        $data_array['path'] = $array_uri[$i];
                    } else {
                        $data_array['path'] .= $array_uri[$i];
                    }
                } else if ($type_query) {
                    if (!isset($data_array['query'])) {
                        if ($array_uri[$i] == '') {
                            $data_array['query'] = '//';
                        } else {
                            $data_array['query'] = $array_uri[$i] . '/';
                        }
                    } else {
                        if ($array_uri[$i] == '') {
                            $data_array['query'] .= '//';
                        } else {
                            $data_array['query'] .= $array_uri[$i] . '/';
                        }
                    }
                } else {
                    if (!isset($data_array['directory'])) {
                        $data_array['directory'] = $array_uri[$i] . '/';
                    } else {
                        $data_array['directory'] .= $array_uri[$i] . '/';
                    }
                }
            }
        }
        if (!isset($data_array['query'])) {
            $data_array['query'] = '';
        }

        if (isset($data_array['port'])) {
            $data_array['website_base'] = $data_array['protocol'] . '//' . $data_array['domain'] . ':' . $data_array['port'] . '/';
        } else {
            $data_array['port'] = '';
            $data_array['website_base'] = $data_array['protocol'] . '//' . $data_array['domain'] . '/';
        }

        if (isset($data_array['directory']) && isset($data_array['path']) && isset($data_array['query'])) {
            $data_array['full_directory'] = $data_array['website_base'] . $data_array['directory'];
            $data_array['full_url'] = $data_array['website_base'] . $data_array['directory'] . $data_array['path'] . $data_array['query'];
        } else if (isset($data_array['directory']) && !isset($data_array['path'])) {
            $data_array['path'] = '';
            $data_array['full_directory'] = $data_array['website_base'] . $data_array['directory'];
            $data_array['full_url'] = $data_array['website_base'] . $data_array['directory'] . $data_array['query'];
        } else if (!isset($data_array['directory']) && isset($data_array['path'])) {
            $data_array['directory'] = '';
            $data_array['full_directory'] = $data_array['website_base'];
            $data_array['full_url'] = $data_array['website_base'] . $data_array['path'] . $data_array['query'];
        } else {
            $data_array['path'] = '';
            $data_array['directory'] = '';
            $data_array['full_directory'] = $data_array['website_base'];
            $data_array['full_url'] = $data_array['website_base'] . $data_array['query'];
        }
        return $data_array;
    }



    function __destruct()
    {
    }
}
