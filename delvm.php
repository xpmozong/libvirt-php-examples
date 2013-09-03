<?php
    error_reporting(0);

    require('libvirt.php');
    $Libvirt = new Libvirt('qemu://118.26.200.67/system');
    $hn = $Libvirt->get_hostname();
    if ($hn == false)
        die('Cannot open connection to hypervisor</body></html>');

    $name = $_GET['vmname'];
    $res = $Libvirt->get_domain_by_name($name);
    $ret = '';
    if(!$Libvirt->domain_is_running($res, $name)){
        if(!$Libvirt->domain_undefine($name)){
            $ret .= $name.'删除失败！<br>';
        }else{
            exec("rm -f /data/vm/".$name.".qcow2");
            $ret .= $name.'删除成功！<br>';
        }
    }else{
        $ret .= $name.'正在运行中，不能删除！<br>';
    }

   header('Location:index.php');

?>