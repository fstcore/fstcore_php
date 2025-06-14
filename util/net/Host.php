<?php
require_once($basedir . '/include/lib/util/3rdparty.php');
require_once($basedir . '/include/lib/util/filesystem/FileSystem.php');
require_once($basedir . '/include/lib/util/encoder/Encoder.php');
require_once($basedir . '/include/lib/util/text/Text.php');
require_once($basedir . '/include/lib/util/net/Parser.php');
require_once($basedir . '/include/lib/util/database/Database.php');

use Hostinger\DigClient;
use JJG\Ping;

class Host
{

    private $data_port = null;
    public $debug;
    public $fstcore;

    function __construct($val = null)
    {
        $this->data_port = $this->fstcore->util->encoder->jsonn_decode($this->fstcore->util->filesystem->readfile($GLOBALS['basedir'] . '/data/tool/', 'ports.json'));
    }

    function escape($string)
    {
        return $this->fstcore->util->database->escape($string);
    }

    function curl_get($host, $cookie, $proxy)
    {
        $curl = new Http();
        $curl->set_url($host);
        $curl->set_proxy($proxy);
        return $curl->request('get');
    }

    public function check_rf($web)
    {
        $return = false;
        $is_redflag = self::curl_get($web, "", "");
        if (preg_match('/\[\[\"sb\.ssr\"\,2\,0\,0/', $is_redflag)) {
            $return = true;
        }
        return $return;
    }

    function whois($type, $domain)
    {
        $connection = '';
        $request = '';
        switch ($type) {
            case 'verisign':
                $server = 'whois.verisign-grs.com';
                $connection = fsockopen($server, 43, $errno, $errstr, 30);
                $request = fputs($connection, $domain . "\r\n");
                break;
            case 'arin':
                $server = 'whois.arin.net';
                $query = 'n + ' . self::get_ip($domain);
                $connection = fsockopen($server, 43, $errno, $errstr, 30);
                $request = fputs($connection, $query . "\r\n");
                break;
            default:
                $server = 'whois.arin.net';
                $query = 'n + ' . self::get_ip($domain);
                $connection = fsockopen($server, 43, $errno, $errstr, 30);
                $request = fputs($connection, $query . "\r\n");
                break;
        }
        if (!$connection or !$request) {
            return "Error $errno: $errstr.";
        }
        $data = '';
        while (!feof($connection)) {
            $data .= fgets($connection);
        }
        fclose($connection);
        return $data;
    }

    function port_info($port)
    {
        $data_array = $this->data_port->$port;
        return $this->fstcore->util->encoder->jsonn_encode($data_array[0]);
    }

    public function dig($host, $resolver = null, $dns_type = null)
    {
        $client = new Hostinger\DigClient();
        $result = '';
        switch ($resolver) {
            case 0:
                $resolver = '8.8.8.8';
                break;
            case 1:
                $resolver = '77.88.8.8 ';
                break;
            case 2:
                $resolver = '208.67.222.222';
                break;
            case 3:
                $resolver = '1.1.1.1';
                break;
            case 4:
                $resolver = '9.9.9.9';
                break;
            default:
                $resolver = '8.8.8.8';
                break;
        }
        switch (strtoupper($dns_type)) {
            case "ALL":
                $result = $client->getRecord($host, $resolver, DNS_ALL);
                break;
            case "A":
                $result = $client->getRecord($host, $resolver, DNS_A);
                break;
            case "AAAA":
                $result = $client->getRecord($host, $resolver, DNS_AAAA);
                break;
            case "CNAME":
                $result = $client->getRecord($host, $resolver, DNS_CNAME);
                break;
            case "MX":
                $result = $client->getRecord($host, $resolver, DNS_MX);
                break;
            case "NS":
                $result = $client->getRecord($host, $resolver, DNS_NS);
                break;
            case "PTR":
                $result = $client->getRecord($host, $resolver, DNS_PTR);
                break;
            case "SRV":
                $result = $client->getRecord($host, $resolver, DNS_SRV);
                break;
            case "SOA":
                $result = $client->getRecord($host, $resolver, DNS_SOA);
                break;
            case "TXT":
                $result = $client->getRecord($host, $resolver, DNS_TXT);
                break;
            case "CAA":
                $result = $client->getRecord($host, $resolver, DNS_CAA);
                break;
            case "DS":
                $result = $client->getRecord($host, $resolver, DNS_DS);
                break;
            case "DNSKEY":
                $result = $client->getRecord($host, $resolver, DNS_KEY);
                break;
                /*case "ALL":
                $result = $client->getRecord($host, $resolver, DNS_A);
                $result .= $client->getRecord($host, $resolver, DNS_AAAA);
                $result .= $client->getRecord($host, $resolver, DNS_CNAME);
                $result .= $client->getRecord($host, $resolver, DNS_MX);
                $result .= $client->getRecord($host, $resolver, DNS_NS);
                $result .= $client->getRecord($host, $resolver, DNS_PTR);
                $result .= $client->getRecord($host, $resolver, DNS_SRV);
                $result .= $client->getRecord($host, $resolver, DNS_SOA);
                $result .= $client->getRecord($host, $resolver, DNS_TXT);
                $result .= $client->getRecord($host, $resolver, DNS_CAA);
                $result .= $client->getRecord($host, $resolver, DNS_DS);
                $result .= $client->getRecord($host, $resolver, DNS_KEY);
                break;*/
            default:
                $result = $client->getRecord($host, $resolver, DNS_A);
                $result .= $client->getRecord($host, $resolver, DNS_AAAA);
                $result .= $client->getRecord($host, $resolver, DNS_CNAME);
                $result .= $client->getRecord($host, $resolver, DNS_MX);
                $result .= $client->getRecord($host, $resolver, DNS_NS);
                $result .= $client->getRecord($host, $resolver, DNS_PTR);
                $result .= $client->getRecord($host, $resolver, DNS_SRV);
                $result .= $client->getRecord($host, $resolver, DNS_SOA);
                $result .= $client->getRecord($host, $resolver, DNS_TXT);
                $result .= $client->getRecord($host, $resolver, DNS_CAA);
                $result .= $client->getRecord($host, $resolver, DNS_DS);
                $result .= $client->getRecord($host, $resolver, DNS_KEY);
                break;
        }
        return $result;
    }

    public function get_hostname($host)
    {
        return gethostbyaddr($host);
    }

    public function get_ip($host)
    {
        $i = $this->fstcore->util->parser->domain_parse($host);
        $ip = gethostbyname($i);
        if (preg_match('/\d+\.\d+\.\d+\.\d+/', $ip)) {
            return $ip;
        } else {
            self::get_ip($host);
        }
    }

    public function check_isp($host)
    {
        $data_array = array();
        $domain = $this->fstcore->util->parser->domain_parse($host);
        $cookie = md5(time() - 3);
        $proxy = '';
        $ip = self::get_ip($domain);
        $content = self::curl_get('http://ip-api.com/json/' . $ip, $cookie, $proxy);
        $data = json_decode($content['body']);
        if (!empty($data->isp) and !empty($data->countryCode)) {
            $data_array['status'] = 'success';
            $data_array['isp'] = $data->isp;
            $data_array['country_code'] = $data->countryCode;
            $data_array['country'] = $data->country;
            $data_array['region'] = $data->region;
            $data_array['region_name'] = $data->regionName;
            $data_array['city'] = $data->city;
            if (empty($data->zip)) {
                $data_array['zip'] = '-';
            } else {
                $data_array['zip'] = $data->zip;
            }
            $data_array['lat'] = $data->lat;
            $data_array['lon'] = $data->lon;
            $data_array['timezone'] = $data->timezone;
            if (empty($data->org)) {
                $data_array['org'] = '-';
            } else {
                $data_array['org'] = $data->org;
            }
            $data_array['as'] = $data->as;
            $data_array['ip'] = $data->query;
            $data_array['hostname'] = self::get_hostname($ip);
            return $data_array;
        } else {
            if (preg_match('/invalid\s+query/', $data)) {
                array_push($data_array, '-');
                array_push($data_array, 'unknown');
            } else {
                self::check_isp($host);
            }
        }
    }

    public function check_port($host, $port)
    {
        $data_array = array();
        $connection = @fsockopen($host, $port, $errno, $errstr, 20);
        if (is_resource($connection)) {
            fclose($connection);
            $data_array['host'] = $host;
            $data_array['port'] = $port;
            $data_array['port_status'] = 'open';
            $data_array['port_info'] = self::port_info($port);
            return $data_array;
        } else {
            $data_array['host'] = $host;
            $data_array['port'] = $port;
            $data_array['port_status'] = 'close';
            $data_array['port_info'] = self::port_info($port);
            return $data_array;
        }
    }

    public function check_whois($domain)
    {
        $data_array = array();
        $arraykey_whois = array('NetRange', 'CIDR', 'NetName', 'NetHandle', 'Parent', 'NetType', 'OriginAS', 'Organization', 'RegDate Regristrant', 'Updated Regristrant', 'Ref', 'ResourceLink', 'ReferralServer', 'OrgAbuseRef', 'OrgTechRef', 'OrgName', 'OrgId', 'Address', 'City', 'StateProv', 'PostalCode', 'Country', 'OrgTechHandle', 'OrgTechName', 'OrgTechPhone', 'OrgTechMail', 'OrgAbuseHandle', 'OrgAbuseName', 'OrgAbusePhone', 'OrgAbuseEmail');
        $arraykey_verisign = array('Domain Status', 'Name Server', 'Domain Name', 'Registry Domain ID', 'Registrar WHOIS Server', 'Registrar URL', 'Updated Date', 'Creation Date', 'Registry Expiry Date', 'Registrar', 'Registrar IANA ID', 'Registrar Abuse Contact Email', 'Registrar Abuse Contact Phone', 'DNSSEC');
        $data_array_whois = array();
        $data_array_verisign = array();
        $data_arin = self::whois('arin', $domain);
        $data_verisign = self::whois('verisign', $domain);
        $data_array['verisign_raw'] = trim($data_verisign);
        $data_array['arin_raw'] = trim($data_arin);
        $array_arin = explode("\n", trim($data_arin));
        $array_verisign = explode("\n", trim($data_verisign));

        foreach ($array_verisign as $d) {
            if (strpos($d, ': ')) {
                $array_explode = explode(': ', preg_replace('/^\s+/i', '', $d));
                switch ($array_explode[0]) {
                    case "Domain Status":
                        if (isset($data_array_verisign['Domain Status'])) {
                            $data_array_verisign['Domain Status'] .= $array_explode[1] . "\n";
                        } else {
                            $data_array_verisign['Domain Status'] = $array_explode[1] . "\n";
                        }
                        break;
                    case "Name Server":
                        if (isset($data_array_verisign['Name Server'])) {
                            $data_array_verisign['Name Server'] .= $array_explode[1] . "\n";
                        } else {
                            $data_array_verisign['Name Server'] = $array_explode[1] . "\n";
                        }
                        break;
                    case "Domain Name":
                        $data_array_verisign['Domain Name'] = strtolower($array_explode[1]);
                        break;
                    case "Registry Domain ID":
                        $data_array_verisign['Registry Domain ID'] = strtolower($array_explode[1]);
                        break;
                    case "Registrar WHOIS Server":
                        $data_array_verisign['Registrar WHOIS Server'] = strtolower($array_explode[1]);
                        break;
                    case "Registrar URL":
                        $data_array_verisign['Registrar URL'] = strtolower($array_explode[1]);
                        break;
                    case "Updated Date":
                        $data_array_verisign['Updated Date'] = strtolower($array_explode[1]);
                        break;
                    case "Creation Date":
                        $data_array_verisign['Creation Date'] = strtolower($array_explode[1]);
                        break;
                    case "Registry Expiry Date":
                        $data_array_verisign['Registry Expiry Date'] = strtolower($array_explode[1]);
                        break;
                    case "Registrar":
                        $data_array_verisign['Registrar'] = strtolower($array_explode[1]);
                        break;
                    case "Registrar IANA ID":
                        $data_array_verisign['Registrar IANA ID'] = strtolower($array_explode[1]);
                        break;
                    case "Registrar Abuse Contact Email":
                        $data_array_verisign['Registrar Abuse Contact Email'] = strtolower($array_explode[1]);
                        break;
                    case "Registrar Abuse Contact Phone":
                        $data_array_verisign['Registrar Abuse Contact Phone'] = strtolower($array_explode[1]);
                        break;
                    case "DNSSEC":
                        $data_array_verisign['DNSSEC'] = strtolower($array_explode[1]);
                        break;
                    default:
                        break;
                }
            }
        }

        foreach ($array_arin as $d) {
            if (strpos($d, ': ')) {
                $array_explode = explode(': ', $d);
                switch ($array_explode[0]) {
                    case "NetRange":
                        $data_array_whois['NetRange'] = $array_explode[1];
                        break;
                    case "CIDR":
                        $data_array_whois['CIDR'] = $array_explode[1];
                        break;
                    case "NetName":
                        $data_array_whois['NetName'] = $array_explode[1];
                        break;
                    case "NetHandle":
                        $data_array_whois['NetHandle'] = $array_explode[1];
                        break;
                    case "Parent":
                        $data_array_whois['Parent'] = $array_explode[1];
                        break;
                    case "NetType":
                        $data_array_whois['NetType'] = $array_explode[1];
                        break;
                    case "OriginAS":
                        $data_array_whois['OriginAS'] = $array_explode[1];
                        break;
                    case "Organization":
                        $data_array_whois['Organization'] = $array_explode[1];
                        break;
                    case "RegDate":
                        if (isset($data_array_whois['RegDate Regristrant']) == false) {
                            $data_array_whois['RegDate Regristrant'] = $array_explode[1];
                        }
                        break;
                    case "Updated":
                        if (isset($data_array_whois['Updated Regristrant']) == false) {
                            $data_array_whois['Updated Regristrant'] = $array_explode[1];
                        }
                        break;
                    case "Ref":
                        if (isset($data_array_whois['Ref'])) {
                            $data_array_whois['Ref'] .= $array_explode[1];
                        } else {
                            $data_array_whois['Ref'] = $array_explode[1];
                        }
                        break;
                    case "ResourceLink":
                        if (isset($data_array_whois['ResourceLink'])) {
                            $data_array_whois['ResourceLink'] .= $array_explode[1];
                        } else {
                            $data_array_whois['ResourceLink'] = $array_explode[1];
                        }
                        break;
                    case "ReferralServer":
                        if (isset($data_array_whois['ReferralServer'])) {
                            $data_array_whois['ReferralServer'] .= $array_explode[1];
                        } else {
                            $data_array_whois['ReferralServer'] = $array_explode[1];
                        }
                        break;
                    case "OrgAbuseRef":
                        $data_array_whois['OrgAbuseRef'] = $array_explode[1];
                        break;
                    case "OrgTechRef":
                        $data_array_whois['OrgTechRef'] = $array_explode[1];
                        break;
                    case "OrgName":
                        $data_array_whois['OrgName'] = $array_explode[1];
                        break;
                    case "OrgId":
                        $data_array_whois['OrgId'] = $array_explode[1];
                        break;
                    case "Address":
                        $data_array_whois['Address'] = $array_explode[1];
                        break;
                    case "City":
                        $data_array_whois['City'] = $array_explode[1];
                        break;
                    case "StateProv":
                        if ($this->text->regex_match('/^(\s+?|\t+)$/', $array_explode[1]) == false) {
                            $data_array_whois['StateProv'] = $array_explode[1];
                        }
                        break;
                    case "PostalCode":
                        $data_array_whois['PostalCode'] = $array_explode[1];
                        break;
                    case "Country":
                        $data_array_whois['Country'] = $array_explode[1];
                        break;
                    case "OrgTechHandle":
                        $data_array_whois['OrgTechHandle'] = $array_explode[1];
                        break;
                    case "OrgTechName":
                        $data_array_whois['OrgTechName'] = $array_explode[1];
                        break;
                    case "OrgTechPhone":
                        $data_array_whois['OrgTechPhone'] = $array_explode[1];
                        break;
                    case "OrgTechEmail":
                        $data_array_whois['OrgTechEmail'] = $array_explode[1];
                        break;
                    case "OrgAbuseHandle":
                        $data_array_whois['OrgAbuseHandle'] = $array_explode[1];
                        break;
                    case "OrgAbuseName":
                        $data_array_whois['OrgAbuseName'] = $array_explode[1];
                        break;
                    case "OrgAbusePhone":
                        $data_array_whois['OrgAbusePhone'] = $array_explode[1];
                        break;
                    case "OrgAbuseEmail":
                        $data_array_whois['OrgAbuseEmail'] = $array_explode[1];
                        break;
                    default:
                        break;
                }
            }
        }

        foreach ($arraykey_whois as $key_whois) {
            if ($this->fstcore->util->text->is_existkeyonarray($key_whois, $data_array_whois) == false) {
                $data_array_whois[$key_whois] = '-';
            }
        }

        foreach ($arraykey_verisign as $key_verisign) {
            if ($this->fstcore->util->text->is_existkeyonarray($key_verisign, $data_array_verisign) == false) {
                $data_array_verisign[$key_verisign] = '-';
            }
        }
        $data_array['whois'] = $data_array_whois;
        $data_array['verisign'] = $data_array_verisign;
        return $data_array;
    }

    public function ping($host)
    {
        return '';
    }

    /**
     * SESSION
     */
    /*public function check_session()
    {
        $checksession = new ConfigCheckSession1996();
        $session = $checksession->check_session();
        if ($session) {
            return true;
        } else {
            return false;
        }
    }

    public function let_session()
    {
        $letsession = new ConfigLetSession1996();
        $let = $letsession->leave_session();
        return true;
    }

    public function login($username, $password, $token)
    {
        $login = new ConfigLogin1996();
        $return = $login->login($username, $password, $token);
        return $return;
    }

    public function register($username, $email, $password, $password2, $token)
    {
        $register = new ConfigRegister1996();
        $return = $register->register($username, $email, $password, $password2, $token);
        return $return;
    }

    public function change_password($currentpassword, $newpassword1, $newpassword2, $token)
    {
        $changepassword = new ConfigChangePassword1996();
        $return = $changepassword->changepassword($currentpassword, $newpassword1, $newpassword2, $token);
        return $return;
    }*/

    /**
     * DASHBOARD
     *
     *
     *
     * public function list_in_seller($type){
     * $list = new ConfigListInSeller1996();
     * $return = $list->list_in_seller($type);
     * return $return;
     * }
     *
     * public function list_to_buy($type){
     * $list = new ConfigListToBuy1996();
     * $return = $list->get_list($type);
     * return $return;
     * }
     *
     * public function my_order_list($type){
     * $list = new ConfigMyOrderList1996();
     * $return = $list->get_list($type);
     * return $return;
     * }
     *
     * public function my_order_open($pgp){
     * $open = new ConfigMyOrderList1996();
     * $return = $open->get_open($pgp);
     * return $return;
     * }
     *
     * public function report_tools($pgp, $comment){
     * $report = new ConfigMyOrderList1996();
     * $return = $report->report_tools($pgp, $comment);
     * return $return;
     * }
     *
     * public function ticket_list($type){
     * $list = new ConfigTicketList1996();
     * $return = $list->get_list($type);
     * return $return;
     * }
     *
     * public function update_ticket($pgp, $type){
     * $update = new ConfigTicketList1996();
     * $return = $update->update_ticket($pgp, $type);
     * return $return;
     * }
     *
     * public function reply_buyer_to_seller_ticket($pgp, $comment){
     * $reply_to_seller = new ConfigTicketList1996();
     * $return = $reply_to_seller->reply_buyer_to_seller_ticket($pgp, $comment);
     * return $return;
     * }
     *
     * public function reply_seller_to_buyer_ticket($pgp, $comment){
     * $reply_to_buyer = new ConfigTicketList1996();
     * $return = $reply_to_buyer->reply_seller_to_buyer_ticket($pgp, $comment);
     * return $return;
     * }
     *
     * public function create_new_ticket_to_admin($title, $comment){
     * $newticket = new ConfigTicketList1996();
     * $return = $newticket->create_new_ticket_to_admin($title, $comment);
     * return $return;
     * }
     *
     * public function check_tool_id($pgp, $type){
     * $toolid = new ConfigCheckToolId1996();
     * $return = $toolid->check_tool_id($pgp, $type);
     * return $return;
     * }
     * public function delete_tool($pgp, $type){
     * $deleteid = new ConfigDeleteToolId1996();
     * $return = $deleteid->delete_tool_id($pgp, $type);
     * return $return;
     * }
     * public function buy_tool($pgp, $type){
     * $buyid = new ConfigBuyToolId1996();
     * $return = $buyid->buy_tool_id($pgp, $type);
     * return $return;
     * }
     *
     * //CPANEL
     * public function add_cpanel($data, $price){
     * $cpanel = new ConfigCpanelAccess1996();
     * $return = $cpanel->add_cpanel($data, $price);
     * }
     * public function check_cpanel($pgp){
     * $cpanel = new ConfigCpanelAccess1996();
     * $return = $cpanel->check_cpanel($pgp);
     * return $return;
     * }
     * //SHELL
     * public function add_shell($data, $price){
     * $shell = new ConfigShellAccess1996();
     * $return = $shell->add_shell($data, $price);
     * return $return;
     * }
     * public function check_shell($pgp){
     * $shell = new ConfigShellAccess1996();
     * $return = $shell->check_shell($pgp);
     * return $return;
     * }
     * //MAILER
     * public function add_mailer($data, $price){
     * $mailer = new ConfigMailerAccess1996();
     * $return = $mailer->add_mailer($data, $price);
     * return $return;
     * }
     * public function check_mailer($pgp, $to){
     * $mailer = new ConfigMailerAccess1996();
     * $return = $mailer->check_mailer($pgp, $to);
     * return $return;
     * }
     * //SMTP
     * public function add_smtp($data, $price){
     * $smtp = new ConfigSmtpAccess1996();
     * $return = $smtp->add_smtp($data, $price);
     * return $return;
     * }
     * public function check_smtp($pgp, $to){
     * $smtp = new ConfigSmtpAccess1996();
     * $return = $smtp->check_smtp($pgp, $to);
     * return $return;
     * }
     */

    /**
     * PAYMENT MODULE
     *
     *
     *
     * //PM
     * public function create_invoice($amount){
     * $invoicepm = new ConfigPaymentPM1996();
     * $return = $invoicepm->create_invoice($amount);
     * return $return;
     * }
     * public function cancel_invoice(){
     * $cancelinvoice = new ConfigPaymentPM1996();
     * $return = $cancelinvoice->cancel_invoice();
     * return $return;
     * }
     * public function complete_invoice($payment_id){
     * $completeinvoice = new ConfigPaymentPM1996();
     * $return = $completeinvoice->complete_invoice($payment_id);
     * return $return;
     * }
     * public function history_invoice(){
     * $historyinvoice = new ConfigPaymentPM1996();
     * $return = $historyinvoice->history_invoice();
     * return $return;
     * }
     * public function update_buyer_balance($payment_id){
     * $updatebuyerbalance = new ConfigPaymentPM1996();
     * $return = $updatebuyerbalance->update_buyer_balance($payment_id);
     * return $return;
     * }
     * public function update_session(){
     * $updatesession = new ConfigUpdateSession1996();
     * $return = $updatesession->update_session();
     * return $return;
     * }
     *
     * //EARN wd
     * public function history_withdrawl(){
     * $historywd = new ConfigSellerWithdrawl1996();
     * $return = $historywd->history_withdrawl();
     * return $return;
     * }
     */
}
