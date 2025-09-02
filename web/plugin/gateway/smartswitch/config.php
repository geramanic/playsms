<?php
/**
 * SmartSwitch gateway configuration
 */
defined('_SECURE_') or die('Forbidden');

// gateway configuration in registry
$reg = gateway_get_registry('smartswitch');

$plugin_config['smartswitch'] = [
    'name' => 'smartswitch',
    'default_url' => 'https://api.smartswitch.example/message?login={SMARTSWITCH_USERNAME}&password={SMARTSWITCH_PASSWORD}&sender={SMARTSWITCH_SENDER}&to={SMARTSWITCH_TO}&text={SMARTSWITCH_MESSAGE}',
    'url' => isset($reg['url']) && $reg['url'] ? $reg['url'] : $plugin_config['smartswitch']['default_url'],
    'callback_url' => gateway_callback_url('smartswitch'),
    'callback_authcode' => isset($reg['callback_authcode']) && $reg['callback_authcode'] ? $reg['callback_authcode'] : '',
    'callback_access' => isset($reg['callback_access']) && $reg['callback_access'] ? $reg['callback_access'] : '',
    'http_method' => isset($reg['http_method']) && (strtoupper($reg['http_method']) == 'GET' || strtoupper($reg['http_method']) == 'POST') ? strtoupper($reg['http_method']) : 'GET',
    'api_username' => isset($reg['api_username']) ? $reg['api_username'] : '',
    'api_password' => isset($reg['api_password']) ? $reg['api_password'] : '',
    'module_sender' => isset($reg['module_sender']) ? $reg['module_sender'] : '',
    'datetime_timezone' => isset($reg['datetime_timezone']) ? $reg['datetime_timezone'] : '',
];

$plugin_config['smartswitch']['_smsc_config_'] = [
    'url' => _('SmartSwitch send SMS URL'),
    'callback_authcode' => _('Callback authcode'),
    'callback_access' => _('Callback access'),
    'http_method' => _('HTTP method'),
    'api_username' => _('API username'),
    'api_password' => _('API password'),
    'module_sender' => _('Module sender ID'),
    'datetime_timezone' => _('Module timezone'),
];

