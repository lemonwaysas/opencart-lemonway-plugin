<?php
/**
 * 2017 Lemon way
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@lemonway.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this addon to newer
 * versions in the future. If you wish to customize this addon for your
 * needs please contact us for more information.
 *
 * @copyright  2017 Lemon way
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/



class LemonWayKit{


    private $dkUrl;
    private $wkUrl;
    private $sslVerification;
    private $wlLogin;
    private $wlPass;
    private $lang;
    private $isLogEnabled;
    private $log_error;//For Error


    /**
     * LemonWayKit constructor.
     * @param string $dkurl
     * @param string $wkUrl
     * @param string $wlLogin
     * @param string $wlPass
     * @param bool $sslVerifacation
     * @param string $lang
     * @param  LOG $log_error
     */
    public function __construct ($dkurl,  $wkUrl, $wlLogin, $wlPass, $sslVerifacation=false, $lang='en',$isLogEnabled=true,$log_error){
            $this->dkUrl = $dkurl;
            $this->wkUrl = $wkUrl;
            $this->sslVerification = $sslVerifacation;
            $this->wlLogin = $wlLogin;
            $this->wlPass = $wlPass;
            $this->lang = $lang;
            $this->isLogEnabled = $isLogEnabled; // Mode debug
            $this->log_error = $log_error;

    }


    public function registerWallet($params)
    {
        return self::sendRequest('RegisterWallet', $params, '1.1');

    }

    public function moneyIn($params)
    {
        return self::sendRequest('MoneyIn', $params, '1.1');


    }

    public function updateWalletDetails($params)
    {
        return self::sendRequest('UpdateWalletDetails', $params, '1.0');

    }

    public function getWalletDetails($params)
    {
        return  self::sendRequest('GetWalletDetails', $params, '1.5');


    }

    public function moneyIn3DInit($params)
    {
        return self::sendRequest('MoneyIn3DInit', $params, '1.1');
    }

    public function moneyIn3DConfirm($params)
    {
        return self::sendRequest('MoneyIn3DConfirm', $params, '1.1');
    }

    public function moneyInWebInit($params)
    {

        return self::sendRequest('MoneyInWebInit', $params, '1.3');
    }

    public function registerCard($params)
    {
        return self::sendRequest('RegisterCard', $params, '1.1');
    }

    public function unregisterCard($params)
    {
        return self::sendRequest('UnregisterCard', $params, '1.0');
    }

    public function moneyInWithCardId($params)
    {
        $res = self::sendRequest('MoneyInWithCardId', $params, '1.1');
              return $res;
    }

    public function moneyInValidate($params)
    {
        return self::sendRequest('MoneyInValidate', $params, '1.0');
    }

    public function sendPayment($params)
    {
        return  $res = self::sendRequest('SendPayment', $params, '1.0');

    }

    public function registerIBAN($params)
    {
         return $res = self::sendRequest('RegisterIBAN', $params, '1.1');

    }

    public function moneyOut($params)
    {
        return  self::sendRequest('MoneyOut', $params, '1.3');

    }

    public function getPaymentDetails($params)
    {
        return  self::sendRequest('GetPaymentDetails', $params, '1.0');


    }

    public function getMoneyInTransDetails($params)
    {
        $res = self::sendRequest('GetMoneyInTransDetails', $params, '1.8');
        if (!isset($res->E)) {
            $res->operations = array();
            foreach ($res->TRANS->HPAY as $HPAY) {
                $res->operations[] = $HPAY;
            }
        }

        return $res;
    }

    public function getMoneyOutTransDetails($params)
    {
        $res = self::sendRequest('GetMoneyOutTransDetails', $params, '1.4');
        if (!isset($res->E)) {
            $res->operations = array();
            foreach ($res->TRANS->HPAY as $HPAY) {
                $res->operations[] = new Operation($HPAY);
            }
        }

        return $res;
    }

    public function uploadFile($params)
    {
        $res = self::sendRequest('UploadFile', $params, '1.1');
        if (!isset($res->E)) {
            $res->kycDoc = new KycDoc($res->UPLOAD);
        }

        return $res;
    }

    public function getKycStatus($params)
    {
        return self::sendRequest('GetKycStatus', $params, '1.5');
    }

    public function getMoneyInIBANDetails($params)
    {
        return self::sendRequest('GetMoneyInIBANDetails', $params, '1.4');
    }

    public function refundMoneyIn($params)
    {
        return self::sendRequest('RefundMoneyIn', $params, '1.2');
    }

    public function getBalances($params)
    {
        return self::sendRequest('GetBalances', $params, '1.0');
    }

    public function moneyIn3DAuthenticate($params)
    {
        return self::sendRequest('MoneyIn3DAuthenticate', $params, '1.0');
    }

    public function moneyInIDealInit($params)
    {
        return self::sendRequest('MoneyInIDealInit', $params, '1.0');
    }

    public function moneyInIDealConfirm($params)
    {
        return self::sendRequest('MoneyInIDealConfirm', $params, '1.0');
    }

    public function registerSddMandate($params)
    {
        $res = self::sendRequest('RegisterSddMandate', $params, '1.0');
        if (!isset($res->E)) {
            $res->sddMandate = new SddMandate($res->SDDMANDATE);
        }
        return $res;
    }

    public function unregisterSddMandate($params)
    {
        return self::sendRequest('UnregisterSddMandate', $params, '1.0');
    }

    public function moneyInSddInit($params)
    {
        return self::sendRequest('MoneyInSddInit', $params, '1.0');
    }

    public function getMoneyInSdd($params)
    {
        return self::sendRequest('GetMoneyInSdd', $params, '1.0');
    }

    public function getMoneyInChequeDetails($params)
    {
        return self::sendRequest('GetMoneyInChequeDetails', $params, '1.4');
    }
    

    


    private function sendRequest($methodName, $params, $version){
       

        $ua = '';
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $ua = $_SERVER['HTTP_USER_AGENT'];
        }
        $ua = "OPENCART-" . VERSION . "/" . $ua;

        $ip = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $tmpip = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($tmpip[0]);
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $params['wlLogin']=$this->wlLogin;
        $params['wlPass']=$this->wlPass;
        $params['language']=$this->lang;
        $params['version']=$version;
        $params['walletIp']=$ip;
        $params['walletUa']=$ua;




        $serviceUrl = $this->dkUrl . '/' . $methodName;

        // wrap to 'p'
        $request = json_encode([
            'p' => $params
        ]);

        $headers = [
            "Content-type: application/json;charset=utf-8",
            "Accept: application/json",
            "Cache-Control: no-cache",
            "Pragma: no-cache"
        ];

        if($this->isLogEnabled) {
            $debug_log = new Log('LemonWayKit-debug.log');
            $debug_log->write('Method:' . $methodName);
            $debug_log->write('ServiceURL:' . $serviceUrl);
            $request_debug=json_decode($request)->p;
            unset($request_debug->wlPass); // Delete Password
            $debug_log->write('Request:' . print_r($request_debug,true));
        }

        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $serviceUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->sslVerification);

        $response = curl_exec($ch);


        if (curl_errno($ch)) {
            if($this->isLogEnabled) {
                $debug_log->write('Curl error: ' . curl_error($ch));
            }

            $this->log_error->write('Lemon Way :Method:' . $methodName);// ADD TO OPENCART ERROR LOG
            $this->log_error->write('Lemon Way :ServiceURL:' . $serviceUrl);// ADD TO OPENCART ERROR LOG
            $request_debug=json_decode($request)->p;
            unset($request_debug->wlPass); // Delete Password
            $this->log_error->write('Lemon Way :Request:' . print_r( $request_debug,true));// ADD TO OPENCART ERROR LOG
            $this->log_error->write('Lemon Way :Curl error: ' . curl_error($ch));// ADD TO OPENCART ERROR LOG

            $error=new StdClass;
            $error->E=new StdClass;
            $error->E->Msg="Curl Error:".curl_error($ch);
        } else {
            $httpStatus = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpStatus == 200) {
                if($this->isLogEnabled) {
                    $debug_log->write('Response: ' . print_r(json_decode($response)->d,true));
                }
                return json_decode($response)->d;
            }
            else {
                if($this->isLogEnabled) {
                    $debug_log->write('Http error : ' . $httpStatus);
                }

                $this->log_error->write('Lemon Way :Method:' . $methodName);// ADD TO OPENCART ERROR LOG
                $this->log_error->write('Lemon Way :ServiceURL:' . $serviceUrl);// ADD TO OPENCART ERROR LOG
                $request_debug=json_decode($request)->p;
                unset($request_debug->wlPass); // Delete Password
                $this->log_error->write('Lemon Way :Request:' . print_r($request_debug,true));// ADD TO OPENCART ERROR LOG
                $this->log_error->write('Lemon Way :Http error : ' . $httpStatus);// ADD TO OPENCART ERROR LOG
                $error=new StdClass;
                $error->E=new StdClass;
                $error->E->Msg="Http error:".$httpStatus;
                return $error;
            }
        }
    }


        
    public function printCardForm($moneyInToken, $cssUrl = '', $language = 'en')
    {
        $accessConfig = self::accessConfig();

        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_URL,
            $accessConfig['webkitUrl'] . "?moneyintoken=" . $moneyInToken . '&p=' . urlencode($cssUrl)
            . '&lang=' . $language
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, !$accessConfig['isTestMode']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $server_output = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        } else {
            $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            switch($returnCode)
            {
                case 200:
                    curl_close($ch);
                    $parsedUrl = parse_url($accessConfig['webkitUrl']);
                    $root = strstr($accessConfig['webkitUrl'], $parsedUrl['path'], true);
                    $server_output = preg_replace(
                        "/src=\"([a-zA-Z\/\.]*)\"/i",
                        "src=\"" . $root . "$1\"",
                        $server_output
                    );
                    
                    return $server_output;
                default:
                    throw new Exception($returnCode);
            }
        }
    }

    private function cleanRequest($str)
    {
        $str = str_replace('&', htmlentities('&'), $str);
        $str = str_replace('<', htmlentities('<'), $str);
        $str = str_replace('>', htmlentities('>'), $str);

        return $str;
    }
}