<?php

class LemonWayService
{
    // Constants
    private $supportedLangs = array(
        'en', // English
        'fr' // French
    );
    const DEFAULT_LANG = 'en';

    private $dkUrl;
    private $wlLogin;
    private $wlPass;
    private $lang;
    private $isLogEnabled;
    private $debug_log;

    /**
     * LemonWayService constructor.
     * @param string $dkurl
     * @param string $wlLogin
     * @param string $wlPass
     * @param int $testMode
     * @param string $lang
     * @param int $isLogEnabled
     */
    public function __construct($dkurl, $wlLogin, $wlPass, $lang = self::DEFAULT_LANG, $isLogEnabled = 1)
    {
        $this->dkUrl = $dkurl;
        $this->wlLogin = $wlLogin;
        $this->wlPass = $wlPass;
        $this->lang = in_array($lang, $this->supportedLangs) ? $lang : self::DEFAULT_LANG;
        $this->isLogEnabled = $isLogEnabled; // Mode debug

        if ($this->isLogEnabled) {
            $this->debug_log = new Log('lemonway_debug.log');
        }
    }

    private function logRequest($serviceUrl, $request)
    {
        $this->debug_log->write('Service URL: ' . $serviceUrl);

        $request_debug = json_decode($request)->p;
        //unset($request_debug->wlPass); // Mask Password
        $this->debug_log->write('Request: ' . json_encode($request_debug, JSON_PRETTY_PRINT));
    }

    private function sendRequest($methodName, $params)
    {
        $ua = '';
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $ua = $_SERVER['HTTP_USER_AGENT'];
        }
        $ua = "OpenCart-" . VERSION . "/" . $ua;

        $ip = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $tmpip = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($tmpip[0]);
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $params['wlLogin'] = $this->wlLogin;
        $params['wlPass'] = $this->wlPass;
        $params['language'] = $this->lang;
        $params['version'] = '10.0';
        $params['walletIp'] = $ip;
        $params['walletUa'] = $ua;

        $serviceUrl = $this->dkUrl . '/' . $methodName;

        // Wrap in 'p'
        $request = json_encode([
            'p' => $params
        ]);

        $headers = [
            "Content-type: application/json;charset=utf-8",
            "Accept: application/json",
            "Cache-Control: no-cache",
            "Pragma: no-cache"
        ];

        if ($this->isLogEnabled) {
            $this->logRequest($serviceUrl, $request);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $serviceUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CAINFO, DIR_SYSTEM . "/library/lemonway/cacert.pem");
        $response = curl_exec($ch);

        if (curl_errno($ch)) { // Curl Error
            $error = new StdClass;
            $error->E = new StdClass;
            $error->E->Msg = "cURL error";
            $error->E->Error = curl_error($ch);

            if ($this->isLogEnabled) {
                $this->debug_log->write($error->E->Msg);
                $this->logRequest($serviceUrl, $request);
            }

            return $error;
        } else {
            $httpStatus = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpStatus != 200) { // HTTP Error
                $error = new StdClass;
                $error->E = new StdClass;
                $error->E->Msg = 'HTTP error';
                $error->E->Code = $httpStatus;

                if ($this->isLogEnabled) {
                    $this->debug_log->write($error->E->Msg);
                    $this->logRequest($serviceUrl, $request);
                }

                return $error;
            } else { // Success
                if ($this->isLogEnabled) {
                    $this->debug_log->write('Response: ' . json_encode(json_decode($response), JSON_PRETTY_PRINT));
                }

                return json_decode($response)->d;
            }
        }
    }

    public function printError($e)
    {
        $str = !empty($e->Code) ? $e->Code . ": " : "";
        $str .= $e->Msg;
        $str .= !empty($e->Error) ? " (" . $e->Error . ")" : "";

        return $str;
    }

    public function getWalletDetails($params)
    {
        return self::sendRequest('GetWalletDetails', $params);
    }

    public function moneyInWebInit($params)
    {
        return self::sendRequest('MoneyInWebInit', $params);
    }

    public function moneyInSofortInit($params)
    {
        return self::sendRequest('MoneyInSofortInit', $params);
    }

    public function moneyInWithCardId($params)
    {
        return self::sendRequest('MoneyInWithCardId', $params);
    }

    public function getMoneyInTransDetails($params)
    {
        $res = self::sendRequest('GetMoneyInTransDetails', $params);
        if (!isset($res->E)) {
            $res->operations = array();
            foreach ($res->TRANS->HPAY as $HPAY) {
                $res->operations[] = $HPAY;
            }
        }

        return $res;
    }
}
