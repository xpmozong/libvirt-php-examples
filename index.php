<?php
require('header.php');

$uri = $lv->get_uri();
$tmp = $lv->get_domain_count();

?>
<div class="wrap">
    <div class="info">
        <p>
            <?php echo "Hypervisor URI: <i>$uri</i>, hostname: <i>$hn</i>";?>
        </p>
        <p>
            <?php echo "Statistics: {$tmp['total']} domains, {$tmp['active']} active, {$tmp['inactive']} inactive";?>
        </p>
        <p>
            <button class="btn btn-primary" onclick="javascript:window.location.href='addvm.php'"><i class="icon-plus icon-white"></i>添加虚拟机</button>
        </p>
    </div>
    <div class="list">
        <?php
            $doms = $lv->get_domains();
            $domkeys = array_keys($doms);
            $active = $tmp['active'];
        ?>
        <table class="table table-bordered">
            <tr>
                <th>虚拟机名称</th>
                <th>CPU#</th>
                <th>内存</th>
                <th>硬盘(s)</th>
                <th>NICs</th>
                <th>系统位数</th>
                <th>状态</th>
                <th>ID / VNC 端口</th>
                <th>操作</th>
            </tr>
            <?php
            $ret = false;
            if ($action) {
                $domName = $lv->domain_get_name_by_uuid($_GET['uuid']);
                if ($action == 'domain-start') {
                    $ret = $lv->domain_start($domName) ? "Domain has been started successfully" : 'Error while starting domain: '.$lv->get_last_error();
                }
                else if ($action == 'domain-stop') {
                    $ret = $lv->domain_shutdown($domName) ? "Domain has been stopped successfully" : 'Error while stopping domain: '.$lv->get_last_error();
                }
                else if ($action == 'domain-destroy') {
                    $ret = $lv->domain_destroy($domName) ? "Domain has been destroyed successfully" : 'Error while destroying domain: '.$lv->get_last_error();
                }
                else if (($action == 'domain-get-xml') || ($action == 'domain-edit')) {
                    $inactive = (!$lv->domain_is_running($domName)) ? true : false;
                    $xml = $lv->domain_get_xml($domName, $inactive);

                    if ($action == 'domain-edit') {
                        if (@$_POST['xmldesc']) {
                            $ret = $lv->domain_change_xml($domName, $_POST['xmldesc']) ? "Domain definition has been changed" :
                                                        'Error changing domain definition: '.$lv->get_last_error();
                        }
                        else
                            $ret = 'Editing domain XML description: <br /><br /><form method="POST"><table width="80%"><tr><td width="200px">Domain XML description: </td>'.
                                '<td><textarea name="xmldesc" rows="25" style="width:80%">'.$xml.'</textarea></td></tr><tr align="center"><td colspan="2">'.
                                '<input type="submit" value=" Edit domain XML description "></tr></form>';
                    }
                    else
                        $ret = "Domain XML for domain <i>$domName</i>:<br /><br />".htmlentities($xml);
                }
            }
            for ($i = 0; $i < sizeof($doms); $i++) {
                $name = $doms[$i];
                $res = $lv->get_domain_by_name($name);
                $uuid = libvirt_domain_get_uuid_string($res);
                $dom = $lv->domain_get_info($res);
                $mem = number_format($dom['memory'] / 1024, 2, '.', ' ').' MB';
                $cpu = $dom['nrVirtCpu'];
                $state = $lv->domain_state_translate($dom['state']);
                $id = $lv->domain_get_id($res);
                $arch = $lv->domain_get_arch($res);
                $vnc = $lv->domain_get_vnc_port($res);
                $nics = $lv->get_network_cards($res);
                if (($diskcnt = $lv->get_disk_count($res)) > 0) {
                    $disks = $diskcnt.' / '.$lv->get_disk_capacity($res);
                    $diskdesc = 'Current physical size: '.$lv->get_disk_capacity($res, true);
                }
                else {
                    $disks = '-';
                    $diskdesc = '';
                }

                if ($vnc < 0){
                    $vnc = '-';
                    $vncport = $vnc;
                }else{
                    $vncport = $vnc;
                    $vnc = $_SERVER['HTTP_HOST'].':'.$vnc;
                }
                    
                unset($tmp);
                if (!$id)
                    $id = '-';
                unset($dom);

                echo "<tr>
                        <td>
                        <a href=\"domaininfo.php?uuid=$uuid\">$name</a>
                        </td>
                        <td>$cpu</td>
                        <td>$mem</td>
                        <td title='$diskdesc'>$disks</td>
                        <td>$nics</td>
                        <td>$arch</td>
                        <td>$state</td>
                        <td>$id / $vnc</td>";

                echo "<td>";

                if ($lv->domain_is_running($res, $name)){
                    echo "<button class=\"btn btn-info\" onclick=\"javascript:window.open('index.php?action=domain-vnc&amp;vmname=$name');\">VNC</button> | ";
                    echo "<button class=\"btn btn-warning\" onclick=\"javascript:location.href='index.php?action=domain-stop&amp;uuid=$uuid'\">关机</button> | ";
                    echo "<button class=\"btn btn-danger\" onclick=\"javascript:location.href='index.php?action=domain-destroy&amp;uuid=$uuid'\">强制关机</button>";
                }else
                    echo "<button class=\"btn btn-success\" onclick=\"javascript:location.href='index.php?action=domain-start&amp;uuid=$uuid'\">开启</button>";

                echo " | <button class=\"btn btn-info\" onclick=\"javascript:location.href='index.php?action=domain-edit&amp;uuid=$uuid'\">编辑XML</button>";

                if (!$lv->domain_is_running($res, $name))
                    echo " | <button class=\"btn btn-danger\" onclick=\"javascript:location.href='delvm.php?vmname=$name'\">删除</button>";
                else
                    echo " | <a href=\"screenshot.php?uuid=$uuid\">屏幕截图</a>";

                echo "</td></tr>";
            }
            ?>
        </table>
        <?php if ($ret) echo "<br /><pre>$ret</pre>";?>
    </div>
</div>

<?php require('footer.php');?>