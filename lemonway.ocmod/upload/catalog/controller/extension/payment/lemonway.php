<?php
require_once dirname(DIR_APPLICATION) . '/service/LemonWayKit.php'; // SEND REQUEST

class ControllerExtensionPaymentLemonWay extends Controller
{

    const LEMONWAY_WEBKIT_4ECOMMERCE_URL_PROD = 'https://webkit.lemonway.fr/mb/lwecommerce/prod/';
    const LEMONWAY_WEBKIT_4ECOMMERCE_URL_TEST = 'https://sandbox-webkit.lemonway.fr/lwecommerce/dev/';
    const LEMONWAY_DIRECTKIT_4ECOMMERCE_URL_PROD = 'https://ws.lemonway.fr/mb/lwecommerce/prod/directkitjson2/service.asmx';
    const LEMONWAY_DIRECTKIT_4ECOMMERCE_URL_TEST = 'https://sandbox-api.lemonway.fr/mb/lwecommerce/dev/directkitjson2/service.asmx';


    /**
     *
     * @var Operation
     */

    protected $moneyin_trans_details = null;


    public function index(){

        // Load language
        $this->load->language('extension/payment/lemonway');

        // Load Model
        $this->load->model('extension/payment/lemonway');


        $data['button_continue'] = $this->language->get('button_continue');
        $data['text_loading'] = $this->language->get('text_loading');

        $data['continue'] = $this->url->link('extension/payment/lemonway/checkout', '', true);
        $data['text_card'] = $this->language->get('text_card');


        /*
        *
        * CUSTOMIZE TO ADD  CREDIT CARD IF IS SAVED
        *
        */

        $data['entry_save_card'] = $this->language->get('entry_save_card');
        $data['entry_use_card'] = $this->language->get('entry_use_card');
        $data['entry_actual_card'] = $this->language->get('entry_actual_card');
        $data['entry_save_new_card'] = $this->language->get('entry_save_new_card');
        $data['entry_not_use_card'] = $this->language->get('entry_not_use_card');
        $data['entry_expiration_date'] = $this->language->get('entry_expiration_date');


        $data['lemonway_oneclick_enabled'] = $this->config->get('lemonway_oneclick_enabled');


        $data['card'] = $this->model_extension_payment_lemonway->getCustomerCard($this->customer->getId());



        return $this->load->view('extension/payment/lemonway', $data);
    }



    public function checkout(){


        $available_card = array('CB', 'VISA', 'MASTERCARD');

        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))  || !isset($this->request->post['cc_type']) || !in_array($this->request->post['cc_type'], $available_card)  ) {

            $this->response->redirect($this->url->link('checkout/cart'));
        }



        //Load Language
        $this->load->language('extension/payment/lemonway');


        $this->load->model('extension/payment/lemonway');
        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);


        $config = $this->getLemonWayConfig();

        $lang = $this->language->get('code');
        $lang = substr($lang, 0, 2);

        $kit = new LemonWayKit($config['dkURL'], $config['wkURL'], $config['login'], $config['pass'], $config['test']!=1, $lang, $this->config->get('lemonway_debug')==1, new Log($this->config->get('config_error_filename')));


        $params = array();

        $cart_id = $this->model_extension_payment_lemonway->getCardId();
        $wkToken = $this->model_extension_payment_lemonway->saveWkToken($cart_id);

        $params['wkToken'] = $wkToken;
        $params['wallet'] = $config['wallet'];
        $total = number_format((float)$order_info['total'], 2, '.', '');





        $comment = $this->config->get('config_name') . " - " .$this->session->data['order_id'] . " - " .
            $this->customer->getLastName() . " " . $this->customer->getFirstName() . " - " . $this->customer->getEmail();// Order id


        $amountComRaw = 0;
        $amountCom = number_format($amountComRaw, 2, '.', '');




        if (!$this->useCard()) {

            $params = array();

            $params['wkToken'] = $wkToken;
            $params['wallet'] = $config['wallet'];


            $registerCard = ((int)$this->registerCard());

            //$param
            $params['amountTot'] = $total;
            $params['amountCom'] = $amountCom;
            $params['comment'] = $comment;
            $paramsreturn = array(
                'registerCard' => (int)$registerCard,
                'action' => 'return',
                'customer_id' => $this->customer->getId(),
                'order_id' => $this->session->data['order_id']

            );
            $paramsreturn = http_build_query($paramsreturn);

            $paramscancel = array(
                'registerCard' => (int)$registerCard,
                'action' => 'cancel',
                'customer_id' => $this->customer->getId(),
                'order_id' => $this->session->data['order_id']

            );
            $paramscancel = http_build_query($paramscancel);


            $paramserror = array(
                'registerCard' => (int)$registerCard,
                'action' => 'error',
                'customer_id' => $this->customer->getId(),
                'order_id' => $this->session->data['order_id']

            );

            $paramserror = http_build_query($paramserror);


            $params['returnUrl'] = $this->url->link('extension/payment/lemonway/checkoutReturn&' . $paramsreturn, '', true);

            $params['cancelUrl'] = $this->url->link('extension/payment/lemonway/checkoutReturn&' . $paramscancel, '', true);

            $params['errorUrl'] = $this->url->link('extension/payment/lemonway/checkoutReturn&' . $paramserror, '', true);

            $params['autoCommission'] = '0';
            $params['registerCard'] = (string)$registerCard;



            $res = $kit->moneyInWebInit($params);



            //Oops, an error occured.
            if (isset($res->E)) {

                //Redirect to the cart and display the  error
                $this->session->data['error'] = 'Lemon Way: ' . $res->E->Msg;
                $this->response->redirect($this->url->link('checkout/cart'));

            }

            if ($this->customer->getId() && isset($res->MONEYINWEB->CARD) && $registerCard) {


                $card = $this->model_extension_payment_lemonway->getCustomerCard($this->customer->getId());
                if (!$card) {
                    $card = array();
                }


                $card['id_customer'] = $this->customer->getId();
                $card['id_card'] = (string)$res->MONEYINWEB->CARD->ID;


                $this->model_extension_payment_lemonway->insertOrUpdateCard($this->customer->getId(), $card);

            }

            $moneyInToken = (string)$res->MONEYINWEB->TOKEN;


            $lwUrl = $config['wkURL'] . '?moneyintoken=' . $moneyInToken . '&p='
                . urlencode($config['cssURL']) . '&lang=' . $lang;



            $cc_type = $this->request->post['cc_type'];


            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $lwUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $config['test']!=1);//False if  test enabled

            $response = curl_exec($ch);

            $matches = array();

            $patternFormActionAndData = '/(action="|name=data value=")([^"]*)"/i';
            if (preg_match_all($patternFormActionAndData, $response, $matches)) {
               ;

                if (isset($matches[2])) {

                    list($actionUrl, $data) = $matches[2];
                    $postFields = array(
                        'DATA' => $data,
                        $cc_type => 1
                    );

                    $text_redirect = $this->language->get('text_redirect');

                    $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
                    $html .= '<html>' . "\n";
                    $html .= '    <head>' . "\n";
                    $html .= '    </head>';
                    $html .= '    <body>';
                    $html .= '        <div align="center"><br />' . $text_redirect . '</div>' . "\n";
                    $html .= '        <div id="buttons" style="display: none;">' . "\n";
                    $html .= '            <form id="lemonway_payment_redirect" action="' . $actionUrl . '" method="post">' . "\n";
                    $html .= '                <input type="hidden" name="' . $cc_type . '_x" value="1" />' . "\n";
                    $html .= '                <input type="hidden" name="' . $cc_type . '_y" value="1" />' . "\n";
                    $html .= '                <input type="hidden" name="DATA" value="' . $data . '" />' . "\n";
                    $html .= '            </form>' . "\n";
                    $html .= '        </div>' . "\n";
                    $html .= '        <script type="text/javascript">document.getElementById("lemonway_payment_redirect").submit();</script>' . "\n";
                    $html .= '    </body>' . "\n";
                    $html .= '</html>';

                    die($html);
                }
            }


        }//Use saved card
        else {


            if (($card = $this->model_extension_payment_lemonway->getCustomerCard($this->customer->getId())) && $this->customer->isLogged()) {
                //Call directkit for MoneyInWithCardId
                $params = array(
                    'wkToken' => $wkToken,
                    'wallet' => $config['wallet'],
                    'amountTot' => $total,
                    'amountCom' => $amountCom,
                    'comment' => $comment . " (Money In with Card Id)",
                    'autoCommission' => 0,
                    'cardId' => $card['id_card']
                );




                $res = $kit->moneyInWithCardId($params);



                //Oops, an error occured.
                if (isset($res->E)) {
                    //Redirect to the cart and display the  error
                    $this->session->data['error'] = 'Lemon Way: ' . $res->E->Msg;
                    $this->response->redirect($this->url->link('checkout/cart'));

                }


                if ($res->TRANS->HPAY->STATUS == "3") {
                    /*$id_order_state = Configuration::get('PS_OS_PAYMENT');
                    if($methodInstance->isSplitPayment()){
                        $id_order_state = Configuration::get(Lemonway::LEMONWAY_SPLIT_PAYMENT_OS);
                    }*/
                    $message = $this->getValue('response_msg');

                    //$currency_id = (int)$this->context->currency->id;
                    //$amount = number_format((float)$cart->getOrderTotal(true, 3), 2, '.', '');
                    //$amount = number_format(((float)$op->CRED + (float)$op->COM), 2, '.', '');
                    $details = $this->getMoneyInTransDetails($wkToken);


                    //$debug_save_card->write('DETAILS:'.print_r($details,true));


                    $validate = $this->validateOrder($order_info, $res->TRANS->HPAY->CRED, $details);


                    if ($validate) {
                        $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 5);
                        $this->response->redirect($this->url->link('checkout/success'));
                    } else {
                        $this->session->data['error'] = 'Lemon Way: ' . "Error while saving order!";
                        $this->response->redirect($this->url->link('checkout/cart'));
                    }

                } else {
                    //Redirect to the cart and display the  error
                    $this->session->data['error'] = 'Lemon Way: ' . $res->TRANS->HPAY->MSG;
                    $this->response->redirect($this->url->link('checkout/cart'));
                }

            } else {
                //Redirect to the cart and display the  error
                $this->session->data['error'] = 'Lemon Way: ' . 'Customer not logged or card not found!';
                $this->response->redirect($this->url->link('checkout/cart'));
            }
        }

    }

    private function getLemonWayConfig(){
        /*

         */

        $config = array();
        // TEST
        if ($this->config->get('lemonway_is_test_mode') == '1') {
            //DIRECT KIT URL TE
            if (!empty($this->config->get('lemonway_directkit_url_test'))) {
                $config['dkURL'] = $this->config->get('lemonway_directkit_url_test');
            } else {
                $config['dkURL'] = self::LEMONWAY_DIRECTKIT_4ECOMMERCE_URL_PROD;

            }
            //WEB KIT URL
            if (!empty($this->config->get('lemonway_webkit_url_test'))) {
                $config['wkURL'] = $this->config->get('lemonway_webkit_url_test');
            } else {
                $config['wkURL'] = self::LEMONWAY_WEBKIT_4ECOMMERCE_URL_TEST;
            }
            $config['test'] = '1';
        } //PROD
        else {
            ///DIRECT KIT URL
            if (!empty($this->config->get('lemonway_directkit_url'))) {
                $config['dkURL'] = $this->config->get('lemonway_directkit_url');
            } else {
                $config['dkURL'] = self::LEMONWAY_DIRECTKIT_4ECOMMERCE_URL_PROD;

            }
            ///WEBKIT URL
            if (!empty($this->config->get('lemonway_webkit_url'))) {
                $config['wkURL'] = $this->config->get('lemonway_webkit_url');
            } else {
                $config['wkURL'] = self::LEMONWAY_WEBKIT_4ECOMMERCE_URL_TEST;

            }
            $config['test'] = '0';
        }

        $config['login'] = $this->config->get('lemonway_api_login');
        $config['pass'] = $this->config->get('lemonway_api_password');
        $config['wallet'] = $this->config->get('lemonway_merchant_id');
        $config['cssURL'] = $this->config->get('lemonway_css_url');
        return $config;
    }



    private function useCard(){
        return $this->getValue('lemonway_oneclic') === 'use_card' && $this->config->get('lemonway_oneclick_enabled')=='1';
    }

    private function getValue($key)
    {
        if (!isset($key) || empty($key) || !is_string($key)) {
            return false;
        }
        $value = (isset($this->request->post[$key]) ? $this->request->post[$key] : (isset($this->request->get[$key]) ? $this->request->get[$key] : null));
        return $value;

    }

    private function registerCard(){
        return $this->getValue('lemonway_oneclic') === 'register_card' && $this->config->get('lemonway_oneclick_enabled')=='1' ;
    }




    private function getMoneyInTransDetails($wkToken){
        if (is_null($this->moneyin_trans_details)) {
            // Call directkit to get Webkit Token
            $params = array('transactionMerchantToken' => $wkToken);

            // Call api to get transaction detail for this order
            /* @var $kit LemonWayKit */
            $lang = substr($this->language->get('code'), 0, 2);
            $config = $this->getLemonWayConfig();

            $kit = new LemonWayKit($config['dkURL'], $config['wkURL'], $config['login'], $config['pass'], $config['test']!=1, $lang, $this->config->get('lemonway_debug')==1, new Log($this->config->get('config_error_filename')));

            $res = $kit->getMoneyInTransDetails($params);



            if (isset($res->lwError)) {
                $this->session->data['error'] = $res->E->Msg;

            }
            $this->moneyin_trans_details = $res;

        }


        return $this->moneyin_trans_details;
    }

    private function validateOrder($order_info, $cred, $details){
        return $order_info['total'] == $cred && $order_info['total'] == $details->TRANS->HPAY[0]->CRED;
    }

    public function validation(){

        if (($this->isSubmit('cart_id') == false) || ($this->isSubmit('customer_id') == false) || $this->isSubmit('action') == false) {
            $this->response->redirect($this->url->link('checkout/cart'));
        }

        $action = $this->getValue('action');
        $cart_id = $this->getValue('cart_id');
        $customer_id = $this->getValue('customer_id');
        $this->load->model('checkout/order');

        switch ($action) {

            case 'return':

                //5 = Order status : Complete
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 5);

                $this->response->redirect($this->url->link('checkout/success'));
                break;
            case 'error':
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 10);
                $this->response->redirect($this->url->link('checkout/failure'));
            case 'cancel'://7
                $this->response->redirect($this->url->link('checkout/cart'));
                break;
            default:
                $this->session->data['error'] = 'Ooops Error !!!';
                $this->response->redirect($this->url->link('checkout/cart'));
                break;
        }
    }

    private function isSubmit($submit){
        return (isset($this->request->post[$submit]) || isset($this->request->get[$submit]));

    }


    /*
     *
     * VALIDATE ORDER
     *
     */

    public function checkoutReturn(){

        //Load Language
        $this->load->language('extension/payment/lemonway');
        //Load Model
        $this->load->model('extension/payment/lemonway');

        //Load Model
        $this->load->model('checkout/order');




        if (($this->isSubmit('response_wkToken') == false) || ($this->isSubmit('action') == false) || ($this->isSubmit('customer_id') == false)) {
            //Param missing
            $this->session->data['error'] = $this->language->get('error_param');
            $this->response->redirect($this->url->link('checkout/cart'));
        }


        $wkToken = $this->getValue('response_wkToken');
        $details = $this->getMoneyInTransDetails($wkToken);

        $action = $this->getValue('action');

        $cart_id=$this->model_extension_payment_lemonway->getCartIdFromToken($wkToken);
        //$cart_id = $this->getCartIdFromToken($wkToken);
        if (!$cart_id) {
            $this->response->redirect($this->url->link('checkout/cart'));
        }

        $redirectParams = array(
            'action' => $action,
            'customer_id' => $this->getValue('customer_id'),
            'cart_id' => $cart_id);




        if ($this->isGet()) { //Is redirection from Lemonway


            if (($this->isSubmit('customer_id') == false)) {
                $this->session->data['error'] = $this->language->get('error_param');
                $this->response->redirect($this->url->link('checkout/cart'));
            }

            //
            //  redirect to validation

            //GET WK Tokenb


            $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
            $total = $order_info['total'];
            $total = number_format((float)$total, 2, '.', '');



            if ($total == $details->TRANS->HPAY[0]->CRED) {
                $this->response->redirect($this->url->link('extension/payment/lemonway/validation', $redirectParams, true));
            } else {
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 10);
                $this->response->redirect($this->url->link('checkout/failure'));
            }
        } elseif ($this->isPost()) {
            sleep(4);
            $register_card = 0;
            if (isset($this->request->get['registerCard'])) {
                $register_card = $this->request->get['registerCard'];
            }
            $register_card = (bool)$register_card;


            //Is instant payment notification
            //wait for GET redirection finish in front


            if ($this->isSubmit('response_code') == false) {
                echo "Test response_code";
                die;
            }

            $response_code = $this->getValue('response_code');
            $amount = (float)$this->getValue('response_transactionAmount');
            $order_id = $this->getValue('order_id');
            $this->model_checkout_order->getOrder($order_id);

            //Double Check




            if ($amount != $details->TRANS->HPAY[0]->CRED) {
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 10);
                $this->response->redirect($this->url->link('checkout/failure'));

            } else {



                $amount_paid = number_format((float)$amount, 2, '.', '');;

                $customer_id = 0;
                if (isset($this->request->get['customer_id'])) {
                    $customer_id = $this->request->get['customer_id'];
                }



                //Default status to error

                //Default message;
                $message = $this->getValue('response_msg');

                if ($this->isValidOrder($action, $response_code, $wkToken) === true) {
                    switch ($action) {
                        case 'return':



                            if ($customer_id && $register_card) {
                                $card = $this->model_extension_payment_lemonway->getCustomerCard($customer_id);
                                if (count($card)== 0) {
                                    $card = array();
                                }


                                $card['id_customer'] = $customer_id;
                                $card['card_num'] = $details->TRANS->HPAY[0]->EXTRA->NUM;
                                $card['card_type'] = $details->TRANS->HPAY[0]->EXTRA->TYP;
                                $card['card_exp'] = $details->TRANS->HPAY[0]->EXTRA->EXP;


                                $this->model_extension_payment_lemonway->insertOrUpdateCard($customer_id, $card);
                                $this->response->redirect($this->url->link('checkout/success'));
                            }

                            break;

                        case 'cancel':


                            /**
                             * Add a message to explain why the order has not been validated
                             */

                            $this->response->redirect($this->url->link('checkout/failure'));

                            break;

                        case 'error':
                            $this->response->redirect($this->url->link('checkout/failure'));

                        default:
                    }
                }

            }
        } else {
            //@TODO throw error for not http method supported
            die();
        }


    }



    protected function isGet()
    {
        return strtoupper($this->request->server['REQUEST_METHOD']) == 'GET';
    }

    protected function isPost()
    {
        return strtoupper($this->request->server['REQUEST_METHOD']) == 'POST';
    }

    protected function isValidOrder($action, $response_code, $wkToken)
    {
        if ($response_code != "0000") {
            return false;
        }

        $actionToStatus = array(
            "return" => "3",
            "error" => "0",
            "cancel" => "0"
        );

        if (!isset($actionToStatus[$action])) {
            return false;
        }

        /* @var $operation Operation */
        $operation = $this->getMoneyInTransDetails($wkToken)->operations[0];

        if ($operation) {
            if ($operation->STATUS == $actionToStatus[$action]) {
                return true;
            }
        }

        return false;
    }


}