<?php
    require('libvirt.php');
    $lv = new Libvirt('qemu:///system');
    $hn = $lv->get_hostname();
    if ($hn == false)
        die('Cannot open connection to hypervisor</body></html>');

    $name = "phpvm";
    $arch = "x86_64";
    $memMB = 1024;
    $maxmemMB = 1024;
    $vcpus = 1;
    $iso_image = "/home/data/iso/GamewaveOS-0.4-x86_64.iso";
    $disk1 = array( "path" => "/home/data/img/L-CentOS-6.3-x64.img",
                    "driver" => "raw",
                    "bus" => "ide",
                    "dev" => "hda",
                    "size" => "5G",
                    "flags" => VIR_DOMAIN_DISK_FILE | VIR_DOMAIN_DISK_ACCESS_ALL );

    $disks = array( $disk1 );

    $network1 = array( 'mac' => '00:11:22:33:44:55',
                       'network' => 'default',
                       'model' => 'e1000' );

    $networks = array( $network1 );
    $flags = 1;

    $res = $lv->domain_new($name, $arch, $memMB, $maxmemMB, $vcpus, $iso_image, $disks, $networks, $flags);
    print_r($res);

    $domains = $lv->get_domains();
    print_r($domains);
?>