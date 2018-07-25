<?php
require_once DIR_SYSTEM . '/library/lemonway/LemonWayService.php'; // SEND REQUEST

class ControllerExtensionPaymentLemonWaySofort extends Controller
{
    // Constants
    private $supportedLangs = array(
        'da' => 'da', // Danish
        'de' => 'ge', // German
        'en' => 'en', // English
        'es' => 'sp', // Spanish
        'fi' => 'fi', // Finnish
        'fr' => 'fr', // French
        'it' => 'it', // Italian
        'ko' => 'ko', // Korean
        'no' => 'no', // Norwegian
        'pt' => 'po', // Portuguese
        'sv' => 'sw' // Swedish
    );
    const DEFAULT_LANG = 'en';

    private function prefix()
    {
        return (version_compare(VERSION, '3.0', '>=')) ? 'payment_' : '';
    }

    /*
    Check if there are GET params
    */
    private function isGet()
    {
        return (strtoupper($this->request->server['REQUEST_METHOD']) == 'GET');
    }

    /*
    Get GET param with $key
    */
    private function getValue($key)
    {
        return (isset($this->request->get[$key]) ? $this->request->get[$key] : null);
    }

    /*
    Check if there are POST params
    */
    private function isPost()
    {
        return (strtoupper($this->request->server['REQUEST_METHOD']) == 'POST');
    }

    /*
    Get POST param with $key
    */
    private function postValue($key)
    {
        return (isset($this->request->post[$key]) ? $this->request->post[$key] : null);
    }

    /*
    Get the config
    */
    private function getLemonWayConfig()
    {
        $config = array();

        if ($this->config->get($this->prefix() . 'lemonway_is_test_mode')) {
            // TEST
            $config['dkURL'] = $this->config->get($this->prefix() . 'lemonway_directkit_url_test'); //DIRECT KIT URL TEST
            $config['wkURL'] = $this->config->get($this->prefix() . 'lemonway_webkit_url_test'); //WEB KIT URL TEST
        } else {
            // PROD
            $config['dkURL'] = $this->config->get($this->prefix() . 'lemonway_directkit_url'); // DIRECT KIT URL PROD
            $config['wkURL'] = $this->config->get($this->prefix() . 'lemonway_webkit_url'); // WEBKIT URL PROD
        }

        $config['login'] = $this->config->get($this->prefix() . 'lemonway_api_login');
        $config['pass'] = $this->config->get($this->prefix() . 'lemonway_api_password');
        $config['wallet'] = empty($this->config->get($this->prefix() . 'lemonway_environment_name')) ? $this->config->get($this->prefix() . 'lemonway_wallet') : $this->config->get($this->prefix() . 'lemonway_custom_wallet');
        $config['cssURL'] = $this->config->get($this->prefix() . 'lemonway_css_url');

        $config['autoCommission'] = (int)!empty($this->config->get($this->prefix() . 'lemonway_environment_name'));
        // Autocom = 0 if lwecommerce, 1 if custom environment

        return $config;
    }

    /*
    Check the real paid amount
    */
    private function checkAmount($amount, $realAmount)
    {
        return ($amount == $realAmount);
    }

    /*
   Call the API with a wkToken to check the details of a payment
   */
    private function getMoneyInTransDetails($wkToken)
    {
        if (is_null($this->money_in_trans_details)) {
            // Call API to get transaction detail for this wkToken
            $config = $this->getLemonWayConfig();

            $lemonwayService = new LemonWayService(
                $config['dkURL'],
                $config['login'],
                $config['pass'],
                substr($this->language->get('code'), 0, 2),
                $this->config->get($this->prefix() . 'lemonway_debug')
            );

            $params = array(
                'transactionMerchantToken' => $wkToken
            );
            $res = $lemonwayService->getMoneyInTransDetails($params);

            $this->money_in_trans_details = $res;
        }

        return $this->money_in_trans_details;
    }

    /*
       Double check
       */
    private function doublecheckAmount($amount, $wkToken)
    {
        $details = $this->getMoneyInTransDetails($wkToken);

        // CREDIT + COMMISSION
        $realAmountDoublecheck = $details->TRANS->HPAY[0]->CRED + $details->TRANS->HPAY[0]->COM;

        // Status 3 means success
        return (($details->TRANS->HPAY[0]->STATUS == '3') && ($amount == $realAmountDoublecheck));
    }

    /*
     Retrieve and update saved card info
    */
    private function updateSavedCardInfo($customerId, $wkToken)
    {
        $card = $this->model_extension_payment_lemonway->getCustomerCard($customerId);
        if (!$card) {
            $card = array();
        }

        $card['customer_id'] = $customerId;
        $details = $this->getMoneyInTransDetails($wkToken);
        $card['card_num'] = $details->TRANS->HPAY[0]->EXTRA->NUM;
        $card['card_type'] = $details->TRANS->HPAY[0]->EXTRA->TYP;
        $card['card_exp'] = $details->TRANS->HPAY[0]->EXTRA->EXP;

        $this->model_extension_payment_lemonway->insertOrUpdateCard($card);
    }

    /*
    The view in the checkout page with the card choosing.
    By clicking on "Continue" a payment will be created in checkout()
    */
    public function index()
    {
        // Load language
        $data = $this->load->language('extension/payment/lemonway');

        // Load Model
        $this->load->model('extension/payment/lemonway_sofort');

        $data['link_checkout'] = $this->url->link('extension/payment/lemonway_sofort/checkout', '', true);
        $data['lemonway_is_test_mode'] = $this->config->get($this->prefix() . 'lemonway_is_test_mode');
        $data['lemonway_oneclick_enabled'] = $this->config->get($this->prefix() . 'lemonway_oneclick_enabled');
        $data['customerId'] = empty($this->customer->getId()) ? 0 : $this->customer->getId();

        return $this->load->view('extension/payment/lemonway_sofort', $data);
    }

    /*
    A payment is created here, the user is redirected to the payment page.
    After putting the card information, the user will be redirected to checkoutReturn()
    */
    public function checkout()
    {

        //Load Language
        $this->load->language('extension/payment/lemonway');

        // Load Model
        $this->load->model('extension/payment/lemonway_sofort');
        $this->load->model('checkout/order');

        // Order info
        $order_id = $this->session->data['order_id'];
        $order_info = $this->model_checkout_order->getOrder($order_id);

        if (!$order_info) {
            // Redirect to the cart and display error
            $this->session->data['error'] = $this->language->get('error_order_not_found');
            $this->response->redirect($this->url->link('checkout/cart', '', true));
        }

        // Lemon Way config
        $config = $this->getLemonWayConfig();

        $lemonwayService = new LemonWayService(
            $config['dkURL'],
            $config['login'],
            $config['pass'],
            substr($this->language->get('code'), 0, 2),
            $this->config->get($this->prefix() . 'lemonway_debug')
        );

        $params = array();

        $params['wallet'] = $config['wallet'];
        $total = number_format((float)$order_info['total'], 2, '.', '');
        $params['amountTot'] = $total;
        $params['comment'] = $this->config->get('config_name') . " - " . $order_id . " - " .
            $this->customer->getLastName() . " " . $this->customer->getFirstName() . " - " .
            $this->customer->getEmail();
        $params['autoCommission'] = $config['autoCommission'];

        $customerId = empty($this->customer->getId()) ? 0 : $this->customer->getId(); // A guest customer has no Id, we consider it 0

        $useCard = (
            $this->config->get($this->prefix() . 'lemonway_oneclick_enabled') &&
            $customerId &&
            $this->postValue('lemonway_oneclick') === 'use_card'
        );

        if (!$useCard) {// If the client use a new card => MoneyInWebInit
            // Whether the client save a card
            $registerCard = (int)(
                $this->config->get($this->prefix() . 'lemonway_oneclick_enabled') &&
                $customerId &&
                $this->postValue('lemonway_oneclick') === 'register_card'
            );
            $params['registerCard'] = $registerCard;

            // Associate order id with a wkToken
            $wkToken = $this->model_extension_payment_lemonway_sofort->saveWkToken($order_id, $registerCard);
            $params['wkToken'] = $wkToken;

            // GET Params
            // returnUrl
            $returnParams = array(
                'action' => 'return'
            );
            $returnParams = http_build_query($returnParams);

            $params['returnUrl'] = $this->url->link('extension/payment/lemonway_sofort/checkoutReturn&' . $returnParams, '', true);
            $res = $lemonwayService->moneyInSofortInit($params);

            // Error
            if (isset($res->E)) {
                // Redirect to the cart and display error
                $this->session->data['error'] = $lemonwayService->printError($res->E);
                $this->response->redirect($this->url->link('checkout/cart', '', true));
            }
            $this->response->redirect(utf8_decode(urldecode($res->SOFORTINIT->actionUrl)));
        }
    }


    /*
    This page receive the response of Lemon Way about the payment then redirect the user to the appropriate order page
    (success, failure or back to checkout)
    */
    public function checkoutReturn()
    {
        //Load Language
        $this->load->language('extension/payment/lemonway');

        //Load Model
        $this->load->model('extension/payment/lemonway_sofort');
        $this->load->model('checkout/order');

        if ($this->isGet()) { // If redirection
            // GET params
            if ($this->config->get($this->prefix() . 'lemonway_debug')) {
                $debug_log = new Log('lemonway_debug.log');
                $debug_log->write('GET params: ' . print_r($this->request->get, true));
            }

            $wkToken = $this->getValue('response_wkToken');
            $action = $this->getValue('action');


            if (!isset($wkToken)) {
                // Redirect to the cart and display error
                $this->session->data['error'] = $this->language->get('error_get');
                $this->response->redirect($this->url->link('checkout/cart', '', true));
            }

            // Get order info
            $order_id = $this->model_extension_payment_lemonway_sofort->getOrderIdFromToken($wkToken);
            $order_info = $this->model_checkout_order->getOrder($order_id);

            if (!$order_info) {
                // Redirect to the cart and display error
                $this->session->data['error'] = $this->language->get('error_order_not_found');
                $this->response->redirect($this->url->link('checkout/cart', '', true));
            }
            $total = number_format((float)$order_info['total'], 2, '.', '');

            switch ($action) {
                case 'return':
                    // Success => Order status 5 : Complete
                    $this->model_checkout_order->addOrderHistory($order_id, 5);
                    $customerId = $order_info['customer_id'];
                    $this->response->redirect($this->url->link('checkout/success', '', true));

                default:
                    $this->session->data['error'] = $this->language->get('error_action');
                    $this->response->redirect($this->url->link('checkout/cart', '', true));
            }
        } elseif ($this->isPost()) { // If IPN
            // Get response by IPN
            if ($this->config->get($this->prefix() . 'lemonway_debug')) {
                $debug_log = new Log('lemonway_debug.log');
                $debug_log->write('IPN: ' . print_r($this->request->post, true));
            }

            $response_code = $this->postValue('response_code');
            $wkToken = $this->postValue('response_wkToken');
            $realAmount = $this->postValue('response_transactionAmount');

            // Get order info
            $order_id = $this->model_extension_payment_lemonway_sofort->getOrderIdFromToken($wkToken);
            $order_info = $this->model_checkout_order->getOrder($order_id);
            $total = number_format((float)$order_info['total'], 2, '.', '');

            if (($response_code == "0000") && $this->checkAmount($total, $realAmount) && $this->doublecheckAmount($total, $wkToken)) {
                // Success => Order status 5 : Complete
                $this->model_checkout_order->addOrderHistory($order_id, 5);
            } else {
                // Error => Order status 10 : Failed
                $this->model_checkout_order->addOrderHistory($order_id, 10);
            }
        } else {
            // Redirect to the cart and display error
            $this->session->data['error'] = $this->language->get('error_param');
            $this->response->redirect($this->url->link('checkout/cart', '', true));
        }
    }
}
