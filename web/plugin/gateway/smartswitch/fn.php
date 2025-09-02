<?php
/**
 * SmartSwitch gateway functions
 */
defined('_SECURE_') or die('Forbidden');

function smartswitch_hook_sendsms($smsc, $sms_sender, $sms_footer, $sms_to, $sms_msg, $uid = 0, $gpid = 0, $smslog_id = 0, $sms_type = 'text', $unicode = 0)
{
    global $plugin_config;

    $plugin_config = gateway_apply_smsc_config($smsc, $plugin_config);

    $module_sender = isset($plugin_config['smartswitch']['module_sender']) && core_sanitize_sender($plugin_config['smartswitch']['module_sender']) ? core_sanitize_sender($plugin_config['smartswitch']['module_sender']) : '';
    $sms_sender = $module_sender ?: core_sanitize_sender($sms_sender);
    $sms_to = core_sanitize_mobile($sms_to);
    $sms_footer = core_sanitize_footer($sms_footer);
    $sms_msg = stripslashes($sms_msg . $sms_footer);

    _log("enter smsc:" . $smsc . " smslog_id:" . $smslog_id . " uid:" . $uid . " from:" . $sms_sender . " to:" . $sms_to, 3, "smartswitch_hook_sendsms");

    if ($sms_sender && $sms_to && $sms_msg) {
        $unicode_query_string = '';
        if ($unicode && function_exists('mb_convert_encoding')) {
            $sms_msg = mb_convert_encoding($sms_msg, "UCS-2", "auto");
            $unicode_query_string = "&coding=8";
        }

        $url = htmlspecialchars_decode($plugin_config['smartswitch']['url'] . $unicode_query_string);
        $url = str_replace('{SMARTSWITCH_USERNAME}', urlencode($plugin_config['smartswitch']['api_username']), $url);
        $url = str_replace('{SMARTSWITCH_PASSWORD}', urlencode($plugin_config['smartswitch']['api_password']), $url);
        $url = str_replace('{SMARTSWITCH_SENDER}', urlencode($sms_sender), $url);
        $url = str_replace('{SMARTSWITCH_TO}', urlencode($sms_to), $url);
        $url = str_replace('{SMARTSWITCH_MESSAGE}', urlencode($sms_msg), $url);
        $url = str_replace('{SMARTSWITCH_CALLBACK_URL}', urlencode($plugin_config['smartswitch']['callback_url']), $url);

        $response = core_get_contents($url, $plugin_config['smartswitch']['http_method']);
        $resp = preg_split('/\s+/', trim($response));
        if (isset($resp[0]) && $resp[0] !== '') {
            $remote_id = $resp[0];
            if (dba_update(_DB_PREF_ . '_tblSMSOutgoing', ['remote_id' => $remote_id], ['smslog_id' => $smslog_id, 'flag_deleted' => 0])) {
                $p_status = 1;
                dlr($smslog_id, $uid, $p_status);
            } else {
                $p_status = 0;
                dlr($smslog_id, $uid, $p_status);
            }
            return true;
        }

        _log("failed smslog_id:" . $smslog_id . " response:[" . $response . "] smsc:" . $smsc, 2, "smartswitch_hook_sendsms");
    }

    $p_status = 2;
    dlr($smslog_id, $uid, $p_status);
    return false;
}
