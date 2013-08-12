<?php
    require('libvirt.php');
    $lv = new Libvirt('qemu:///system');
    $hn = $lv->get_hostname();
    if ($hn == false)
        die('Cannot open connection to hypervisor</body></html>');

    $name = $_POST['vmname'];
    $res = $lv->get_domain_by_name($name);
    if($res) exit('此虚拟机名称已存在，请重新<a href="javascript:history.back(-1);">创建</a>！');
    
    $memory = ($_POST['memory']) * 1024 * 1024;
    $cmemory = ($_POST['cmemory']) * 1024 * 1024;
    $vcpu = $_POST['vcpu'];
    $hvm = $_POST['hvm'];
    $img = $_POST['img'];
    $iso = $_POST['iso'];
    $bridge = $_POST['bridge'];
    $address = $_POST['address'];

    $xml = "<domain type='kvm'>
    <name>$name</name>
    <memory>$memory</memory>
    <currentMemory>$cmemory</currentMemory>
    <vcpu>$vcpu</vcpu>
    <os>
        <type arch='$hvm' machine='pc'>hvm</type>
        <boot dev='cdrom'/>
    </os>
    <features>
        <acpi/>
        <apic/>
        <pae/>
    </features>
    <clock offset='localtime'/>
    <on_poweroff>destroy</on_poweroff>
    <on_reboot>restart</on_reboot>
    <on_crash>destroy</on_crash>
    <devices>
        <emulator>/usr/libexec/qemu-kvm</emulator>
        <disk type='file' device='disk'>
            <source file='$img'/>
            <target dev='hda' bus='ide'/>
        </disk>
        <disk type='file' device='cdrom'>
            <source file='$iso'/>
            <target dev='hdb' bus='ide'/>
            <readonly/>
        </disk>
        <interface type='bridge'>
            <source bridge='$bridge'/>
            <mac address='$address'/>
        </interface>
        <input type='mouse' bus='ps2'/>
        <graphics type='vnc' port='-1' autoport='yes' keymap='en-us' listen='0.0.0.0'/>
    </devices>
</domain>";

    $res = $lv->domain_define($xml);

    if(!empty($res)) header('Location:index.php');
    else exit($lv->get_last_error());
?>