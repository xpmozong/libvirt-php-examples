<?php
    require('libvirt.php');
    $lv = new Libvirt('qemu:///system');
    $hn = $lv->get_hostname();
    if ($hn == false)
        die('Cannot open connection to hypervisor</body></html>');

    $xml = "<domain type='kvm'>
    <name>phpxmlvm</name>
    <memory>1048576</memory>
    <currentMemory>1048576</currentMemory>
    <vcpu>1</vcpu>
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
            <source file='/home/data/img/L-CentOS-6.3-x64.img'/>
            <target dev='hda' bus='ide'/>
        </disk>
        <disk type='file' device='cdrom'>
            <source file='/home/data/iso/GamewaveOS-0.4-x86_64.iso'/>
            <target dev='hdb' bus='ide'/>
            <readonly/>
        </disk>
        <interface type='bridge'>
            <source bridge='virbr0'/>
            <mac address='00:16:3e:5d:aa:a8'/>
        </interface>
        <input type='mouse' bus='ps2'/>
        <graphics type='vnc' port='-1' autoport='yes' keymap='en-us' listen='0.0.0.0'/>
    </devices>
</domain>";

    $lv->domain_define($xml);

    $domains = $lv->get_domains();
    print_r($domains);
?>