<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>虚拟机管理</title>
<link type="text/css" rel="stylesheet" href="bootstrap/css/bootstrap.min.css"/>
<link type="text/css" rel="stylesheet" href="css/style.css"/>
<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
</head>
<body>
<?php
    error_reporting(0);
    $ip = '118.26.200.67';
    require('libvirt.php');
    $lv = new Libvirt('qemu://'.$ip.'/system');
    $hn = $lv->get_hostname();
    if ($hn == false)
        die('Cannot open connection to hypervisor</body></html>');

    $action = array_key_exists('action', $_GET) ? $_GET['action'] : '';
    $subaction = array_key_exists('subaction', $_GET) ? $_GET['subaction'] : '';

    if (($action == 'get-screenshot') && (array_key_exists('uuid', $_GET))) {
        if (array_key_exists('width', $_GET) && $_GET['width'])
            $tmp = $lv->domain_get_screenshot_thumbnail($_GET['uuid'], $_GET['width']);
        else
            $tmp = $lv->domain_get_screenshot($_GET['uuid']);

        if (!$tmp){
            echo $lv->get_last_error().'<br />';
        }else {
            Header('Content-Type: image/png');
            die($tmp);
        }
    }

    if($action){
        if( $action == 'domain-vnc'){
            $vmname = $_GET['vmname'];
            $res = $lv->get_domain_by_name($vmname);
            $vnc = $lv->domain_get_vnc_port($res);

            $port = (int)$vnc + 16100;
            
            $lsof = exec("lsof -i tcp:$port");
            if(empty($lsof)){
                exec("/data/noVNC/utils/websockify.py -D $port $ip:$vnc");
            }

            header('Location:http://'.$ip.':6080/vnc_auto.html?host='.$ip.'&port='.$port);
        }
    }

?>
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="brand" href="index.php">虚拟机管理</a>
            <div class="nav-collapse collapse">
                <ul class="nav">
                    <li><a href="index.php">Main page</a></li>
                    <li><a href="hostinfo.php">Host information</a></li>
                    <li><a href="networks.php">Virtual networks</a></li>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </div>
</div>
