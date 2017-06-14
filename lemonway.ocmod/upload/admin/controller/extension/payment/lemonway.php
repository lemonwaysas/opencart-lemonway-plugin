<?php

/**
 * Created by PhpStorm.
 * User: Nabil CHARAF
 * Date: 15/05/2017
 * Time: 15:51
 */
class ControllerExtensionPaymentLemonway extends Controller
{
    //const LOG_FILENAME='LemonWayAdmin.log';


    /*
     *
     * LINK
     *
     *
     */


    const LEMONWAY_WEBKIT_4ECOMMERCE_URL_PROD = 'https://webkit.lemonway.fr/mb/lwecommerce/prod/';
    const LEMONWAY_WEBKIT_4ECOMMERCE_URL_TEST = 'https://sandbox-webkit.lemonway.fr/lwecommerce/dev/';
    const LEMONWAY_DIRECTKIT_4ECOMMERCE_URL_PROD = 'https://ws.lemonway.fr/mb/lwecommerce/prod/directkitjson2/service.asmx';
    const LEMONWAY_DIRECTKIT_4ECOMMERCE_URL_TEST = 'https://sandbox-api.lemonway.fr/mb/lwecommerce/dev/directkitjson2/service.asmx';


    //DEFINE URL TEST VALID


    private $error = array();


    public function index(){

        // Load language
        $this->load->language('extension/payment/lemonway');



        // Load language variables

        // Heading
        $data['heading_title'] = $this->language->get('heading_title');

        $data['button_save']   = $this->language->get('button_save');

        $data['button_cancel']   = $this->language->get('button_cancel');


        // Text
        $data['text_payment'] = $this->language->get('text_payment');
        $data['text_breadcrumbs'] = $this->language->get('text_breadcrumbs');
        $data['text_success'] = $this->language->get('text_success');
        $data['text_lemonway'] = $this->language->get('text_lemonway');
        $data['text_test'] = $this->language->get('text_test');
        $data['text_live'] = $this->language->get('text_live');
        $data['text_authorization'] = $this->language->get('text_authorization');
        $data['text_charge'] = $this->language->get('text_charge');
        $data['text_help'] = $this->language->get('text_help');
        $data['text_edit_config'] = $this->language->get('text_edit_config');


        //NAV TABS TITLE
        $data['text_about_us'] = $this->language->get('text_about_us');
        $data['text_configuration'] = $this->language->get('text_configuration');
        $data['text_one_click'] = $this->language->get('text_one_click');

        $data['text_advanced_configuration'] = $this->language->get('text_advanced_configuration');


        //About US
        $data['text_sign_in'] = $this->language->get('text_sign_in');
        $data['text_sign_up'] = $this->language->get('text_sign_up');
        $data['text_create_account'] = $this->language->get('text_create_account');
        $data['text_complete_form'] = $this->language->get('text_complete_form');
        $data['text_complete_your_profile'] = $this->language->get('text_complete_your_profile');
        $data['text_test_card'] = $this->language->get('text_test_card');
        $data['text_help_desk'] = $this->language->get('text_help_desk');
        $data['text_turnkey_solution'] = $this->language->get('text_turnkey_solution');
        $data['text_secured_solution'] = $this->language->get('text_secured_solution');
        $data['text_commission'] = $this->language->get('text_commission');
        $data['text_secured_payment'] = $this->language->get('text_secured_payment');
        $data['text_increase_turnover'] = $this->language->get('text_increase_turnover');
        $data['text_acpr'] = $this->language->get('text_acpr');
        $data['text_manage_transaction'] = $this->language->get('text_manage_transaction');
        $data['text_one_click_refund'] = $this->language->get('text_one_click_refund');
        $data['text_move_money'] = $this->language->get('text_move_money');
        $data['text_more_information'] = $this->language->get('text_more_information');
        $data['text_link_ecommerce'] = $this->language->get('text_link_ecommerce');
        $data['text_link_support'] = $this->language->get('text_link_support');
        $data['text_follow_turnover'] = $this->language->get('text_follow_turnover');
        $data['text_or'] = $this->language->get('text_or');
        $data['text_save'] = $this->language->get('text_save');

        //One click
        $data['text_configration_one_click'] = $this->language->get('text_configration_one_click');

        //HELP
        $data['help_login_prod'] = $this->language->get('help_login_prod');
        $data['help_wallet'] = $this->language->get('help_wallet');
        $data['help_test'] = $this->language->get('help_test');
        $data['help_leave_empty'] = $this->language->get('help_leave_empty');
        $data['help_oneclick'] = $this->language->get('help_oneclick');

        // Entry
        $data['entry_login'] = $this->language->get('entry_login');
        $data['entry_password'] = $this->language->get('entry_password');
        $data['entry_wallet'] = $this->language->get('entry_wallet');
        $data['entry_test'] = $this->language->get('entry_test');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_debug'] = $this->language->get('entry_debug');
        $data['entry_yes'] = $this->language->get('entry_yes');
        $data['entry_no'] = $this->language->get('entry_no');
        $data['entry_one_click'] = $this->language->get('entry_one_click');
        $data['entry_css'] = $this->language->get('entry_css');

        // URL
        $data['entry_directkit_json_url'] = $this->language->get('entry_directkit_json_url');
        $data['entry_webkit_url'] = $this->language->get('entry_webkit_url');
        $data['entry_directkit_json_url_test'] = $this->language->get('entry_directkit_json_url_test');
        $data['entry_webkit_url_test'] = $this->language->get('entry_webkit_url_test');


        //Warning
        $data['warning_status'] = $this->language->get('warning_status');



        // Load settings
        $this->load->model('setting/setting');


        // Set document title
        $this->document->setTitle($this->language->get('heading_title'));


        //ACCOUNT INFORMATION
        $data['lemonway_api_login'] = $this->model_setting_setting->getSettingValue('lemonway_api_login');
        $data['lemonway_api_password'] = $this->model_setting_setting->getSettingValue('lemonway_api_password');
        $data['lemonway_merchant_id'] = $this->model_setting_setting->getSettingValue('lemonway_merchant_id');

        $data['lemonway_is_test_mode'] = $this->model_setting_setting->getSettingValue('lemonway_is_test_mode');
        $data['lemonway_oneclick_enabled'] = $this->model_setting_setting->getSettingValue('lemonway_oneclick_enabled');

        $data['lemonway_debug'] = $this->model_setting_setting->getSettingValue('lemonway_debug');//


        //ADVANCED ACCOUNT CONFIGURATION

        $data['lemonway_directkit_url'] = $this->model_setting_setting->getSettingValue('lemonway_directkit_url');
        $data['lemonway_webkit_url'] = $this->model_setting_setting->getSettingValue('lemonway_webkit_url');
        $data['lemonway_directkit_url_test'] = $this->model_setting_setting->getSettingValue('lemonway_directkit_url_test');
        $data['lemonway_webkit_url_test'] = $this->model_setting_setting->getSettingValue('lemonway_webkit_url_test');

        //One Click
        $data['lemonway_status'] = $this->model_setting_setting->getSettingValue('lemonway_status');
        $data['lemonway_css_url'] = $this->model_setting_setting->getSettingValue('lemonway_css_url');

        //UNSET THE ERROR

        unset($data['error_permission'], $data['error_login'], $data['error_password'], $data['error_curl'], $data['error_wallet'], $data['error_testConfig']);


        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        // Load default layout
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');

        $data['footer'] = $this->load->controller('common/footer');

        //ABOUT US
        $data['about_us'] = $this->load->view('extension/payment/lemonway_aboutus.tpl', $data);

        //Configure
        $data['configure'] = $this->load->view('extension/payment/lemonway_configure.tpl', $data);

        //ONE CLICK
        $data['one_click'] = $this->load->view('extension/payment/lemonway_oneclick.tpl', $data);



        // Load breadcrumbs
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_breadcrumbs'),
            'href' => $this->url->link('extension/payment/lemonway', 'token=' . $this->session->data['token'], true)
        );


        // Load action buttons urls
        $data['action'] = $this->url->link('extension/payment/lemonway', 'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], true);


        $data['link_css'] = 'view/stylesheet/lemonway/back.css';

        // If isset request to change settings
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {

            // Edit settings
            $this->model_setting_setting->editSetting('lemonway', $this->request->post);


            // Set success message
            if ($this->testConfig()) {
                $this->session->data['success'] = $this->language->get('text_success');
                // Return to extensions page
                $this->response->redirect($this->url->link('extension/payment/lemonway', '    token=' . $this->session->data['token'], true));
            } else {
                //Display Error
                $data['error_testConfig'] = $this->error['testConfig'];

                //ACCOUNT INFORMATION
                $data['lemonway_api_login'] = $this->model_setting_setting->getSettingValue('lemonway_api_login');
                $data['lemonway_api_password'] = $this->model_setting_setting->getSettingValue('lemonway_api_password');
                $data['lemonway_merchant_id'] = $this->model_setting_setting->getSettingValue('lemonway_merchant_id');

                $data['lemonway_is_test_mode'] = $this->model_setting_setting->getSettingValue('lemonway_is_test_mode');
                $data['lemonway_oneclick_enabled'] = $this->model_setting_setting->getSettingValue('lemonway_oneclick_enabled');

                $data['lemonway_debug'] = $this->model_setting_setting->getSettingValue('lemonway_debug');


                $data['error_testConfig'].='TEST:'.$data['lemonway_is_test_mode'];


                //ADVANCED ACCOUNT CONFIGURATION

                $data['lemonway_directkit_url'] = $this->model_setting_setting->getSettingValue('lemonway_directkit_url');
                $data['lemonway_webkit_url'] = $this->model_setting_setting->getSettingValue('lemonway_webkit_url');
                $data['lemonway_directkit_url_test'] = $this->model_setting_setting->getSettingValue('lemonway_directkit_url_test');
                $data['lemonway_webkit_url_test'] = $this->model_setting_setting->getSettingValue('lemonway_webkit_url_test');

                //One Click
                $data['lemonway_status'] = $this->model_setting_setting->getSettingValue('lemonway_status');
                $data['lemonway_css_url'] = $this->model_setting_setting->getSettingValue('lemonway_css_url');




                $this->response->setOutput($this->load->view('extension/payment/lemonway.tpl', $data));
            }

        }



        $data = $this->loadAllErrors($data);

        $this->response->setOutput($this->load->view('extension/payment/lemonway.tpl', $data));

    }

    protected function validate(){
        if (!$this->user->hasPermission('modify', 'extension/payment/lemonway')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!extension_loaded('curl')) {
            $this->error['curl_not_installed'] = $this->langage->get('error_curl');
        }

        if (!isset($this->request->post['lemonway_api_login']) && empty($this->model_setting_setting->getSettingValue('lemonway_api_login'))) {
            $this->error['login_not_set'] = $this->language->get('error_login');
        }

        if ((!isset($this->request->post['lemonway_api_login']) || strlen($this->request->post['lemonway_api_login']) == 0) && !empty($this->model_setting_setting->getSettingValue('lemonway_api_login'))) {
            $this->request->post['lemonway_api_login'] = $this->model_setting_setting->getSettingValue('lemonway_api_login');
        }


        if (!isset($this->request->post['lemonway_api_password']) && empty($this->model_setting_setting->getSettingValue('lemonway_api_password'))) {
            $this->error['password_not_set'] = $this->language->get('error_password');
        }


        if ((!isset($this->request->post['lemonway_api_password']) || strlen($this->request->post['lemonway_api_password']) == 0) && !empty($this->model_setting_setting->getSettingValue('lemonway_api_password'))) {
            $this->request->post['lemonway_api_password'] = $this->model_setting_setting->getSettingValue('lemonway_api_password');
        }


        if (!isset($this->request->post['lemonway_merchant_id']) && empty($this->model_setting_setting->getSettingValue('lemonway_merchant_id'))) {
            $this->error['merchant_id_not_set'] = $this->language->get('error_wallet');
        }

        if (!isset($this->request->post['lemonway_merchant_id']) && !empty($this->model_setting_setting->getSettingValue('lemonway_merchant_id'))) {
            $this->request->post['lemonway_merchant_id'] = $this->model_setting_setting->getSettingValue('lemonway_merchant_id');
        }

        // One Click
        if (!isset($this->request->post['lemonway_oneclick_enabled'])) {
            $this->request->post['lemonway_oneclick_enabled'] = '0';
        }



        //Dlemonway status

        if (!isset($this->request->post['lemonway_status']) ) {
            $this->request->post['lemonway_status'] = '0';
        }


        //Debug
        if (!isset($this->request->post['lemonway_debug']) ) {
            $this->request->post['lemonway_oneclick_enabled'] = '0';
        }




        //  Test Mode

        if (!isset($this->request->post['lemonway_is_test_mode']) ) {
            $this->request->post['lemonway_is_test_mode'] = '0';
        }


        // Default values
        if (!isset($this->request->post['lemonway_directkit_url']) && empty ($this->model_setting_setting->getSettingValue('lemonway_directkit_url'))) {
            $this->request->post['lemonway_directkit_url'] = self::LEMONWAY_DIRECTKIT_4ECOMMERCE_URL_PROD;
        }

        // Default values
        if (!isset($this->request->post['lemonway_directkit_url']) && !empty ($this->model_setting_setting->getSettingValue('lemonway_directkit_url'))) {
            $this->request->post['lemonway_directkit_url'] = $this->model_setting_setting->getSettingValue('lemonway_directkit_url');
        }


        // Default values
        if (!isset($this->request->post['lemonway_webkit_url']) && empty ($this->model_setting_setting->getSettingValue('lemonway_webkit_url'))) {
            $this->request->post['lemonway_webkit_url'] = self::LEMONWAY_WEBKIT_4ECOMMERCE_URL_PROD;
        }

        if (!isset($this->request->post['lemonway_webkit_url']) && !empty ($this->model_setting_setting->getSettingValue('lemonway_webkit_url'))) {
            $this->request->post['lemonway_webkit_url'] = $this->model_setting_setting->getSettingValue('lemonway_webkit_url');
        }


        // Default values
        if (!isset($this->request->post['lemonway_directkit_url_test']) && empty($this->model_setting_setting->getSettingValue('lemonway_directkit_url_test'))) {
            $this->request->post['lemonway_directkit_url_test'] = self::LEMONWAY_DIRECTKIT_4ECOMMERCE_URL_TEST;
        }

        if (!isset($this->request->post['lemonway_directkit_url_test']) && !empty($this->model_setting_setting->getSettingValue('lemonway_directkit_url_test'))) {
            $this->request->post['lemonway_directkit_url_test'] = $this->model_setting_setting->getSettingValue('lemonway_directkit_url_test');
        }

        // Default values
        if (!isset($this->request->post['lemonway_webkit_url_test']) && empty($this->model_setting_setting->getSettingValue('lemonway_webkit_url_test'))) {
            $this->request->post['lemonway_webkit_url_test'] = self::LEMONWAY_WEBKIT_4ECOMMERCE_URL_TEST;
        }

        if (!isset($this->request->post['lemonway_webkit_url_test']) && !empty($this->model_setting_setting->getSettingValue('lemonway_webkit_url_test'))) {
            $this->request->post['lemonway_webkit_url_test'] = $this->model_setting_setting->getSettingValue('lemonway_webkit_url_test');
        }

        // Default values
        if (!isset($this->request->post['lemonway_css_url'])) {
            $this->request->post['lemonway_css_url'] = 'https://webkit.lemonway.fr/css/mercanet/mercanet_lw_custom.css';
        }


        if (!$this->error) {
            return true;
        } else {
            return false;
        }


    }

    private function getConfig(){

        // Load settings
        $this->load->model('setting/setting');

        $config = array();
        // TEST
        if ($this->model_setting_setting->getSettingValue('lemonway_is_test_mode') == '1') {
            //DIRECT KIT URL TE
            if (!empty($this->model_setting_setting->getSettingValue('lemonway_directkit_url_test'))) {
                $config['dkURL'] = $this->model_setting_setting->getSettingValue('lemonway_directkit_url_test');
            } else {
                $config['dkURL'] = self::LEMONWAY_DIRECTKIT_4ECOMMERCE_URL_TEST;

            }
            //WEB KIT URL
            if (!empty($this->model_setting_setting->getSettingValue('lemonway_webkit_url_test'))) {
                $config['wkURL'] = $this->model_setting_setting->getSettingValue('lemonway_webkit_url_test');
            } else {
                $config['wkURL'] = self::LEMONWAY_WEBKIT_4ECOMMERCE_URL_TEST;
            }
            $config['test'] = '1';
        } //PROD
        else {
            ///DIRECT KIT URL
            if (!empty($this->model_setting_setting->getSettingValue('lemonway_directkit_url'))) {
                $config['dkURL'] = $this->model_setting_setting->getSettingValue('lemonway_directkit_url');
            } else {
                $config['dkURL'] = self::LEMONWAY_DIRECTKIT_4ECOMMERCE_URL_PROD;

            }
            ///WEBKIT URL
            if (!empty($this->model_setting_setting->getSettingValue('lemonway_webkit_url'))) {
                $config['wkURL'] = $this->model_setting_setting->getSettingValue('lemonway_webkit_url');
            } else {
                $config['wkURL'] = self::LEMONWAY_WEBKIT_4ECOMMERCE_URL_PROD;

            }
            $config['test'] = '0';
        }

        $config['login'] = $this->model_setting_setting->getSettingValue('lemonway_api_login');
        $config['pass'] = $this->model_setting_setting->getSettingValue('lemonway_api_password');
        $config['wallet'] = $this->model_setting_setting->getSettingValue('lemonway_merchant_id');
        $config['debug'] = $this->model_setting_setting->getSettingValue('lemonway_debug');
        return $config;

    }


    private function testConfig(){

        $config = $this->getConfig();
        require_once dirname(DIR_APPLICATION) . '/service/LemonWayKit.php'; // SEND REQUEST

        $lang = substr($this->language->get('code'), 0, 2);

        $kit = new LemonWayKit($config['dkURL'], $config['wkURL'], $config['login'], $config['pass'], $config['test'] != '1', $lang, $config['debug'] == 1, new Log($this->config->get('config_error_filename')));
        $params = array('wallet' => $config['wallet']);

        $res = $kit->getWalletDetails($params);
        if (isset($res->E)) {
            $this->error['testConfig'] = $res->E->Msg;
            return false;
        } else {
            return true;
        }


    }



    private function loadAllErrors($data){
        foreach ($this->error as $key => $value) {
            $data['error_' . $key] = $value;
        }
        return $data;
    }

    public function install(){
        /*
         *
         * CREATE TABLE
         *
         */

        $this->load->model('extension/payment/lemonway');
        $this->model_extension_payment_lemonway->install();


    }

    public function uninstall(){


        $this->load->model('extension/payment/lemonway');

        $this->model_extension_payment_lemonway->uninstall();
    }


}


?>