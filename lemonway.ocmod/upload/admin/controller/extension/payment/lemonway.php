<?php
define('LEMONWAY_VERSION', '1.1.1');

class ControllerExtensionPaymentLemonway extends Controller
{
    const LEMONWAY_ENVIRONMENT_DEFAULT = 'lwecommerce';

    const LEMONWAY_WEBKIT_FORMAT_URL_PROD = 'https://webkit.lemonway.fr/mb/%s/prod/';
    const LEMONWAY_WEBKIT_FORMAT_URL_TEST = 'https://sandbox-webkit.lemonway.fr/%s/dev/';
    const LEMONWAY_DIRECTKIT_FORMAT_URL_PROD = 'https://ws.lemonway.fr/mb/%s/prod/directkitjson2/service.asmx';
    const LEMONWAY_DIRECTKIT_FORMAT_URL_TEST = 'https://sandbox-api.lemonway.fr/mb/%s/dev/directkitjson2/service.asmx';

    const CSS_URL_DEFAULT = 'https://webkit.lemonway.fr/css/mercanet/mercanet_lw_custom.css';

    private $variables = array();

    public function index()
    {   
        // Load settings
        $this->load->model('setting/setting');

        // Load language
        $this->variables = $this->load->language('extension/payment/lemonway');
        $this->document->setTitle($this->language->get('heading_title'));

        // If POST request => validate the data before saving
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
            // Edit settings
            $this->model_setting_setting->editSetting($this->prefix() . 'lemonway', $this->request->post);
        }

        // Load default layout
        $this->variables['header'] = $this->load->controller('common/header');
        $this->variables['column_left'] = $this->load->controller('common/column_left');
        $this->variables['footer'] = $this->load->controller('common/footer');

        $this->variables['cancel_link'] = (version_compare(VERSION, '3.0', '>=')) ? $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true) : $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);

        // Load setting values
        $this->variables['lemonway_api_login'] = $this->model_setting_setting->getSettingValue($this->prefix() . 'lemonway_api_login');
        $this->variables['lemonway_api_password'] = $this->model_setting_setting->getSettingValue($this->prefix() . 'lemonway_api_password');
        $this->variables['lemonway_is_test_mode'] = $this->model_setting_setting->getSettingValue($this->prefix() . 'lemonway_is_test_mode');
        $this->variables['lemonway_css_url'] = $this->model_setting_setting->getSettingValue($this->prefix() . 'lemonway_css_url');
        $this->variables['lemonway_debug'] = $this->model_setting_setting->getSettingValue($this->prefix() . 'lemonway_debug');
        $this->variables['lemonway_environment_name'] = $this->model_setting_setting->getSettingValue($this->prefix() . 'lemonway_environment_name');
        $this->variables['lemonway_custom_wallet'] = $this->model_setting_setting->getSettingValue($this->prefix() . 'lemonway_custom_wallet');
        $this->variables['lemonway_status'] = $this->model_setting_setting->getSettingValue($this->prefix() . 'lemonway_status');
        $this->variables['lemonway_oneclick_enabled'] = $this->model_setting_setting->getSettingValue($this->prefix() . 'lemonway_oneclick_enabled');
        $this->variables['lemonway_template_name'] = $this->model_setting_setting->getSettingValue($this->prefix() . 'lemonway_template_name');


        // Alerts
        $this->variables['no_permission'] = false;
        $this->variables['no_method'] = false;
        $this->variables['success'] = false;
        $this->variables['api_error'] = false;

        if ($this->variables['lemonway_status']) { // If enabled
            // Test the config
            if ($this->testConfig()) {
                $this->variables['success'] = true;
            }
        } else { // If no method enabled
            $this->variables['no_method'] = true;
        }
        // Load tabs
        // About us
        $this->variables['about_us'] = $this->load->view('extension/payment/lemonway_aboutus', $this->variables);
        // Configuration
        $this->variables['config'] = $this->load->view('extension/payment/lemonway_config', $this->variables);
        // Credit Card
        $this->variables['cc'] = $this->load->view('extension/payment/lemonway_cc', $this->variables);

        $this->response->setOutput($this->load->view('extension/payment/lemonway', $this->variables));
    }

    private function prefix() {
        return (version_compare(VERSION, '3.0', '>=')) ? 'payment_' :  '';
    }

    private function validate()
    {
        $error = false;

        if (!$this->user->hasPermission('modify', 'extension/payment/lemonway')) {
            $this->variables['no_permission'] = true;
            $error = true;
        }

        //  Test mode
        if (!isset($this->request->post['lemonway_is_test_mode'])) {
            $this->request->post['lemonway_is_test_mode'] = 0;
        }

        // Default value if lemonway_css_url is empty
        if (empty($this->request->post['lemonway_css_url'])) {
            $this->request->post['lemonway_css_url'] = self::CSS_URL_DEFAULT;
        }

        // Debug mode
        if (!isset($this->request->post['lemonway_debug'])) {
            $this->request->post['lemonway_debug'] = 0;
        }

        // Environment name
        if (empty($this->request->post['lemonway_environment_name'])) {
            // If no custom environment => lwecommerce
            $env_name = self::LEMONWAY_ENVIRONMENT_DEFAULT;
        } else {
            // If custom environment
            $env_name = $this->request->post['lemonway_environment_name'];
        }
        
        // Generate API links
        $this->request->post['lemonway_directkit_url'] = sprintf(self::LEMONWAY_DIRECTKIT_FORMAT_URL_PROD, $env_name);
        $this->request->post['lemonway_webkit_url'] = sprintf(self::LEMONWAY_WEBKIT_FORMAT_URL_PROD, $env_name);
        $this->request->post['lemonway_directkit_url_test'] = sprintf(self::LEMONWAY_DIRECTKIT_FORMAT_URL_TEST, $env_name);
        $this->request->post['lemonway_webkit_url_test'] = sprintf(self::LEMONWAY_WEBKIT_FORMAT_URL_TEST, $env_name);

        //Credit Card status
        if (!isset($this->request->post['lemonway_status'])) {
            $this->request->post['lemonway_status'] = 0;
        }
        
        // One-click
        if (!isset($this->request->post['lemonway_oneclick_enabled'])) {
            $this->request->post['lemonway_oneclick_enabled'] = 0;
        }

        foreach ($this->request->post as $key => $value) {
            unset($this->request->post[$key]);
            $this->request->post[$this->prefix() . $key] = $value; //concatinate your existing array with new one
        }

        return !$error; // If no error => validated
    }

    // Test if the config is OK
    private function testConfig()
    {
        // Load settings
        $this->load->model('setting/setting');

        // TEST
        if ($this->variables['lemonway_is_test_mode']) {
            // DIRECTKIT TEST URL
            $dkUrl = $this->model_setting_setting->getSettingValue($this->prefix() . 'lemonway_directkit_url_test');
        } //PROD
        else {
            // DIRECTKIT URL
            $dkUrl = $this->model_setting_setting->getSettingValue($this->prefix() . 'lemonway_directkit_url');
        }

        require_once DIR_SYSTEM . '/library/lemonway/LemonWayService.php';

        // API connection
        $lemonwayService = new LemonWayService(
            $dkUrl,
            $this->variables['lemonway_api_login'],
            $this->variables['lemonway_api_password'],
            substr($this->language->get('code'), 0, 2),
            $this->variables['lemonway_debug']
        );

        if (empty($this->variables['lemonway_environment_name'])) {
            // If lwecommerce, get wallet by email
            $params = array('email' => $this->variables['lemonway_api_login']);
        } else {
            // If custom env, get custom wallet
            $params = array('wallet' => $this->variables['lemonway_custom_wallet']);
        }

        $res = $lemonwayService->getWalletDetails($params);
        //var_dump($res->WALLET->ID);
        if (empty($this->variables['lemonway_environment_name'])) {
            // If lwecommerce, get wallet
            if (isset($res->WALLET->ID)) {
                $this->model_setting_setting->editSettingValue($this->prefix() . 'lemonway', $this->prefix() . 'lemonway_wallet', $res->WALLET->ID);
            }
        }

        if (isset($res->E)) {
            $this->variables['error_api'] = $this->language->get('error_api') . " - " . $lemonwayService->printError($res->E);
            $this->variables['api_error'] = true;
            return false;
        } else {
            return true;
        }
    }

    public function install()
    {
        $this->load->model('extension/payment/lemonway');
        $this->model_extension_payment_lemonway->install();

        // Default settings
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting($this->prefix() . 'lemonway', [
            $this->prefix() . 'lemonway_status' => 1,
            $this->prefix() . 'lemonway_css_url' => self::CSS_URL_DEFAULT,
            $this->prefix() . 'lemonway_directkit_url' => sprintf(self::LEMONWAY_DIRECTKIT_FORMAT_URL_PROD, self::LEMONWAY_ENVIRONMENT_DEFAULT),
            $this->prefix() . 'lemonway_webkit_url' => sprintf(self::LEMONWAY_WEBKIT_FORMAT_URL_PROD, self::LEMONWAY_ENVIRONMENT_DEFAULT),
            $this->prefix() . 'lemonway_directkit_url_test' => sprintf(self::LEMONWAY_DIRECTKIT_FORMAT_URL_TEST, self::LEMONWAY_ENVIRONMENT_DEFAULT),
            $this->prefix() . 'lemonway_webkit_url_test' => sprintf(self::LEMONWAY_WEBKIT_FORMAT_URL_TEST, self::LEMONWAY_ENVIRONMENT_DEFAULT)
        ]);
    }

    public function uninstall()
    {
        $this->load->model('extension/payment/lemonway');
        $this->model_extension_payment_lemonway->uninstall();
    }
}
