<?php
    require('libvirt.php');
    $lv = new Libvirt('qemu:///system');
    $hn = $lv->get_hostname();
    if ($hn == false)
        die('Cannot open connection to hypervisor</body></html>');

    $domains = $lv->get_domains();
    print_r($domains);
?>