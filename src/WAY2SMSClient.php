<?php
/**
 * Created by Aashutosh Sharma.
 * @author Aashutosh Sharma <aashu.engineer@gmail.com>
 * Date: 22-Aug-16
 * Time: 12:56 AM
 */

namespace Aashu;


class WAY2SMSClient
{


    var $curl;
    var $timeout = 120;
    var $jsToken;
    var $way2smsHost;
    var $refurl;
    var $useragent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:48.0) Gecko/20100101 Firefox/48.0';
    var $page;
    var $sendsmsurl;

    function login($username, $password) {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, "http://way2sms.com");
        curl_setopt($this->curl, CURLOPT_HEADER, true);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($this->curl, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Expect:'));
        $a = curl_exec($this->curl);
        $this->refurl = curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL);

        if (preg_match('#Location:(.*)#', $a, $r))
            $this->way2smsHost = trim($r[1]);

        curl_setopt($this->curl, CURLOPT_URL, $this->way2smsHost . "Login1.action");
        curl_setopt($this->curl, CURLOPT_POST, 1);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, "username=" . urlencode(trim($username)) . "&password=" . urlencode(trim($password)) . "&button=Login");
        curl_setopt($this->curl, CURLOPT_COOKIESESSION, 1);
        curl_setopt($this->curl, CURLOPT_COOKIEJAR, "cookie_way2sms.txt");
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, "cookie_way2sms.txt");
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($this->curl, CURLOPT_REFERER, $this->refurl);
        curl_setopt($this->curl, CURLOPT_NOBODY, false);
        $this->page = $text = curl_exec($this->curl);
        $this->refurl = curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL);
        if (preg_match('/Password entered by you is not correct./', $text, $r)) {
            $this->page = $text;
            return "password not matched.";
        }
        if (preg_match('/Invalid Request./', $text, $r)) {
            $this->page = $text;
            return "Invalid Request.";
        }
        if (preg_match('/Your email id is not verified. Please verify your email id to ensure the security of your account./', $text, $r)) {
            $this->refurl = curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL);
            $newurl = str_replace("vem.action", "ebrdg", $this->refurl);
            curl_setopt($this->curl, CURLOPT_URL, $newurl);
            $this->page = $text = curl_exec($this->curl);
            $this->refurl = curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL);
        }

        $this->refurl = curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL);
        $newurl = str_replace("ebrdg", "main.action", $this->refurl);
        $newurl = str_replace("id=", "Token=", $newurl);
        $d = explode('?', $newurl);
        $newurl = $d[0] . '?section=s&' . $d[1] . '&vfType=register_verify';
        curl_setopt($this->curl, CURLOPT_URL, $newurl);
        $this->page = $text = curl_exec($this->curl);

        $this->refurl = curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL);
        preg_match('/jsessionToken=(.*)\?/i', $this->refurl, $matches);
        $this->jstoken = $matches[1];
        $newurl = str_replace("main.action", "sendSMS", $this->refurl);
        $d = explode('?', $newurl);
        $this->sendsmsurl = $d[0] . '?Token=' . $this->jstoken;
        curl_setopt($this->curl, CURLOPT_URL, $this->sendsmsurl);
        $this->page = $text = curl_exec($this->curl);
        $this->refurl = curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL);
        return true;
    }

    function send($phone, $msg) {
        $msg = substr($msg, 0, 140);
        curl_setopt($this->curl, CURLOPT_URL, $this->way2smsHost . 'smstoss.action');
        curl_setopt($this->curl, CURLOPT_REFERER, $this->refurl);
        curl_setopt($this->curl, CURLOPT_POST, 1);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, "ssaction=ss&Token=" . $this->jstoken . "&mobile=" . $phone . "&message=" . $msg . "&button=Login");
        $this->page = curl_exec($this->curl);
        $pos = strpos($this->page, 'Can\'t submit your message, finished your day quota');
        if($pos !== false){
            return "finished your day quota limit ";
        }
        $pos = strpos($this->page, 'Message has been submitted successfully');
        $res = ($pos !== false) ? "msg sent." : "error in sending.";
        return 'phone :"' . $phone . '"   msg:  "' . $msg . '"  result: ' . $res;
    }

    /**
     * logout of current session.
     */
    function logout() {
        curl_setopt($this->curl, CURLOPT_URL, $this->way2smsHost . "LogOut");
        curl_setopt($this->curl, CURLOPT_REFERER, $this->refurl);
        $text = curl_exec($this->curl);
        curl_close($this->curl);
    }
}
