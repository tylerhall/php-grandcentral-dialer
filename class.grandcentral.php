<?PHP
    class GrandCentral
    {
        private $a_t; // Authorization token

        public function __construct($username = null, $password = null)
        {
            if(!is_null($username) && !is_null($password))
                $this->login($username, $password);
        }

        // Login to GrandCentral.
        public function login($username, $password)
        {
            $this->curl('http://www.grandcentral.com');
            $headers = $this->curl('http://www.grandcentral.com/account/login_from_flash/', 'http://www.grandcentral.com', "remember_me=0&login_password=$password&login_id=$username", true);
            $this->a_t = $this->match('/a_t=([^;]+)/', $headers, 1);
        }

        // Connect $you to $them. Takes two 10 digit US phone numbers.
        public function call($you, $them)
        {
            $you = preg_replace('/[^0-9]/', '', $you);
            $them = preg_replace('/[^0-9]/', '', $them);
            $this->curl("http://grandcentral.com/calls/send_call_request?a_t={$this->a_t}&category_id=undefined&contact_id=undefined&calltype=call&destno=$them&ani=$you");
        }

        // From: http://code.google.com/p/simple-php-framework/
        private function curl($url, $referer = null, $post = null, $return_header = false)
        {
            static $tmpfile;

            if(!isset($tmpfile) || ($tmpfile == '')) $tmpfile = tempnam('/tmp', 'FOO');

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $tmpfile);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $tmpfile);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en-US; rv:1.8.1) Gecko/20061024 BonEcho/2.0");
            if($referer) curl_setopt($ch, CURLOPT_REFERER, $referer);

            if(!is_null($post))
            {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            }

            if($return_header)
            {
                curl_setopt($ch, CURLOPT_HEADER, 1);
                $html        = curl_exec($ch);
                $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                return substr($html, 0, $header_size);
            }
            else
            {
                $html = curl_exec($ch);
                return $html;
            }
        }

        // From: http://code.google.com/p/simple-php-framework/
        private function match($regex, $str, $i = 0)
        {
            return preg_match($regex, $str, $match) == 1 ? $match[$i] : false;
        }
    }
