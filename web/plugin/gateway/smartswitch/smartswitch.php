<?php
/**
 * SmartSwitch gateway administration
 */
defined('_SECURE_') or die('Forbidden');

if (!auth_isadmin()) {
    auth_block();
}

include $core_config['apps_path']['plug'] . "/gateway/smartswitch/config.php";

switch (_OP_) {
    case "manage":
        $tpl = [
            'name' => 'smartswitch',
            'vars' => [
                'DIALOG_DISPLAY' => _dialog(),
                'Manage' => _('Manage'),
                'Gateway' => _('Gateway'),
                'SmartSwitch send SMS URL' => _mandatory(_('SmartSwitch send SMS URL')),
                'Callback URL' => _('Callback URL'),
                'Callback authcode' => _('Callback authcode'),
                'Callback access' => _('Callback access'),
                'HTTP method' => _('HTTP method'),
                'API username' => _('API username'),
                'API password' => _('API password'),
                'Module sender ID' => _('Module sender ID'),
                'Module timezone' => _('Module timezone'),
                'Save' => _('Save'),
                'Notes' => _('Notes'),
                'HINT_CALLBACK_URL' => _hint(_('Empty callback URL to set default')),
                'HINT_CALLBACK_AUTHCODE' => _hint(_('Fill with at least 16 alphanumeric authentication code to secure callback URL')),
                'HINT_CALLBACK_ACCESS' => _hint(_('Fill with IP addresses (separated by comma) to limit access to callback URL')),
                'HINT_HTTP_METHOD' => _hint(_('Select which HTTP method will be used to submit requests')),
                'HINT_FILL_PASSWORD' => _hint(_('Fill to change the API password')),
                'HINT_MODULE_SENDER' => _hint(_('Max. 16 numeric or 11 alphanumeric char. empty to disable')),
                'HINT_TIMEZONE' => _hint(_('Eg: +0700 for UTC+7 or Jakarta/Bangkok timezone')),
                'CALLBACK_URL_ACCESSIBLE' => _('Your callback URL must be accessible from IP addresses listed in callback access'),
                'CALLBACK_AUTHCODE' => sprintf(_('You have to include callback authcode as query parameter %s'), ': <strong>authcode</strong>'),
                'CALLBACK_ACCESS' => _('Your callback requests must be coming from IP addresses listed in callback access'),
                'REMOTE_PUSH_DLR' => _('Remote gateway or callback server will push DLR and incoming SMS to your callback URL'),
                'BUTTON_BACK' => _back('index.php?app=main&inc=core_gateway&op=gateway_list'),
                'gateway_name' => $plugin_config['smartswitch']['name'],
                'url' => $plugin_config['smartswitch']['url'],
                'callback_url' => gateway_callback_url('smartswitch'),
                'callback_authcode' => $plugin_config['smartswitch']['callback_authcode'],
                'callback_access' => $plugin_config['smartswitch']['callback_access'],
                'http_method' => $plugin_config['smartswitch']['http_method'],
                'api_username' => $plugin_config['smartswitch']['api_username'],
                'module_sender' => $plugin_config['smartswitch']['module_sender'],
                'datetime_timezone' => $plugin_config['smartswitch']['datetime_timezone']
            ]
        ];
        _p(tpl_apply($tpl));
        break;

    case "manage_save":
        $url = isset($_REQUEST['url']) && $_REQUEST['url'] ? $_REQUEST['url'] : $plugin_config['smartswitch']['default_url'];
        $callback_url = gateway_callback_url('smartswitch');
        $callback_authcode = isset($_REQUEST['callback_authcode']) && core_sanitize_alphanumeric($_REQUEST['callback_authcode']) ? core_sanitize_alphanumeric($_REQUEST['callback_authcode']) : '';
        $callback_access = isset($_REQUEST['callback_access']) ? preg_replace('/[^0-9a-zA-Z\.,_\-\/]+/', '', trim($_REQUEST['callback_access'])) : '';
        $callback_access = preg_replace('/[,]+/', ',', $callback_access);
        $http_method = $_REQUEST['http_method'] ?? 'GET';
        $http_method = strtoupper($http_method) == 'GET' || strtoupper($http_method) == 'POST' ? strtoupper($http_method) : 'GET';
        $api_username = $_REQUEST['api_username'];
        $api_password = $_REQUEST['api_password'];
        $module_sender = core_sanitize_sender($_REQUEST['module_sender']);
        $datetime_timezone = $_REQUEST['datetime_timezone'];
        if ($url) {
            $items = [
                'url' => $url,
                'callback_url' => $callback_url,
                'callback_authcode' => $callback_authcode,
                'callback_access' => $callback_access,
                'http_method' => $http_method,
                'api_username' => $api_username,
                'module_sender' => $module_sender,
                'datetime_timezone' => $datetime_timezone
            ];
            if ($api_password) {
                $items['api_password'] = $api_password;
            }
            if (registry_update(0, 'gateway', 'smartswitch', $items)) {
                $_SESSION['dialog']['info'][] = _('Gateway module configurations has been saved');
            } else {
                $_SESSION['dialog']['danger'][] = _('Fail to save gateway module configurations');
            }
        } else {
            $_SESSION['dialog']['danger'][] = _('All mandatory fields must be filled');
        }
        header("Location: " . _u('index.php?app=main&inc=gateway_smartswitch&op=manage'));
        exit();
}
