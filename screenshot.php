<?php
    error_reporting(0);

    require('libvirt.php');
    $Libvirt = new Libvirt('qemu://118.26.200.67/system');
    $hn = $Libvirt->get_hostname();
    if ($hn == false)
        die('Cannot open connection to hypervisor</body></html>');

    $tmp = $Libvirt->domain_get_screenshot($_GET['uuid']);

    if (!$tmp)
        echo $Libvirt->get_last_error().'<br />';
    else {
        Header('Content-Type: image/png');
        die($tmp);
    }

?>