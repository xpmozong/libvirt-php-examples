<?php
    require('libvirt.php');
    $lv = new Libvirt('qemu://118.26.200.67/system');
    $hn = $lv->get_hostname();
    if ($hn == false)
        die('Cannot open connection to hypervisor</body></html>');

    $vm_name = $_POST['vmname'];
    $res = @$lv->get_domain_by_name($name);
    if($res) exit('此虚拟机名称已存在，请重新<a href="javascript:history.back(-1);">创建</a>！');
    
    $memory = ((int)$_POST['memory']) * 1024 * 1024;
    $vcpu = $_POST['vcpu'];
    $hvm = $_POST['hvm'];
    $bridge = $_POST['bridge'];
    $mac1 = exec('MACADDR="52:56:$(dd if=/dev/urandom count=1 2>/dev/null | md5sum | sed \'s/^\(..\)\(..\)\(..\)\(..\).*$/\1:\2:\3:\4/\')"; echo $MACADDR');
    $mac2 = exec('MACADDR="52:56:$(dd if=/dev/urandom count=1 2>/dev/null | md5sum | sed \'s/^\(..\)\(..\)\(..\)\(..\).*$/\1:\2:\3:\4/\')"; echo $MACADDR');

    exec("qemu-img create -f qcow2 -o preallocation=metadata /data/vm/".$vm_name.".qcow2 100G");

    $xml = "<domain type='kvm'>
                <name>$vm_name</name>
                <memory>$memory</memory>
                <currentMemory>$memory</currentMemory>
                <vcpu>".$vcpu."</vcpu>
                <os>
                    <type arch='x86_64' machine='pc'>hvm</type>
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
                        <driver name='qemu' type='qcow2'/>
                        <source file='/data/vm/".$vm_name.".qcow2'/>
                        <target dev='hda' bus='ide'/>
                    </disk>
                    <disk type='file' device='cdrom'>
                        <source file='/data/iso/GamewaveOS-0.4-x86_64.iso'/>
                        <target dev='hdb' bus='ide'/>
                        <readonly/>
                    </disk>
                    <interface type='bridge'>
                        <source bridge='public'/>
                        <mac address='$mac1'/>
                    </interface>
                    <interface type='bridge'>
                        <source bridge='public'/>
                        <mac address='$mac2'/>
                    </interface>
                    <input type='mouse' bus='ps2'/>
                    <graphics type='vnc' port='-1' autoport='yes' keymap='en-us' listen='0.0.0.0'/>
                </devices>
            </domain>";
            
    $res = $lv->domain_define($xml);

    $res = $lv->get_domain_by_name($vm_name);
    $uuid = libvirt_domain_get_uuid_string($res);
    $domName = $lv->domain_get_name_by_uuid($uuid);
    $lv->domain_start($domName);

    if(!empty($res)) header('Location:index.php');
    else exit($lv->get_last_error());
?>