<?php
require('header.php');
?>
<div class="wrap">
    <div class="list">
        <?php
            $subaction = array_key_exists('subaction', $_GET) ? $_GET['subaction'] : false;
            $ret = false;
            $die = false;
            $domName = $lv->domain_get_name_by_uuid($_GET['uuid']);

            if ($subaction == 'disk-remove') {
                if ((array_key_exists('confirm', $_GET)) && ($_GET['confirm'] == 'yes'))
                    $ret = $lv->domain_disk_remove($domName, $_GET['dev']) ? 'Disk has been removed successfully' : 'Cannot remove disk: '.$lv->get_last_error();
                else {
                    $ret = '<table>
                        <tr>
                        <td colspan="2">
                            <b>Do you really want to delete disk <i>'.$_GET['dev'].' from the guest</i> ?</b><br />
                        </td>
                        </tr>
                        <tr align="center">
                        <td>
                            <a href="'.$_SERVER['REQUEST_URI'].'&amp;confirm=yes">Yes, delete it</a>
                        </td>
                        <td>
                             <a href="?action='.$action.'&amp;uuid='.$_GET['uuid'].'">No, go back</a>
                        </td>
                        </tr>';
                    $die = true;
                }
            }
            if ($subaction == 'disk-add') {
                $img = array_key_exists('img', $_POST) ? $_POST['img'] : false;

                if ($img)
                    $ret = $lv->domain_disk_add($domName, $_POST['img'], $_POST['dev']) ? 'Disk has been successfully added to the guest' :
                            'Cannot add disk to the guest: '.$lv->get_last_error();
                            else
                    $ret = '<b>Add a new disk device</b>
                    <form method="POST">
                    <table>
                    <tr>
                        <td>Disk image: </td>
                        <td><input type="text" name="img" /></td>
                    </tr>
                    <tr>
                        <td>Disk device in the guest: </td>
                        <td><input type="text" name="dev" value="hdb" /></td>
                    </tr>
                    <tr align="center">
                        <td colspan="2"><input type="submit" value=" Add new disk " /></td>
                    </tr>
                    </table>
                    </form>';
            }

            if ($subaction == 'nic-remove') {
                if ((array_key_exists('confirm', $_GET)) && ($_GET['confirm'] == 'yes'))
                    $ret = $lv->domain_nic_remove($domName, $_GET['mac']) ? 'Network card has been removed successfully' : 'Cannot remove network card: '.$lv->get_last_error();
                else {
                    $ret = '<table class="table table-bordered">
                        <tr>
                        <td colspan="2">
                            <b>Do you really want to delete NIC with MAC address <i>'.$_GET['mac'].' from the guest</i> ?</b><br />
                        </td>
                        </tr>
                        <tr align="center">
                        <td>
                            <a href="'.$_SERVER['REQUEST_URI'].'&amp;confirm=yes">Yes, delete it</a>
                        </td>
                        <td>
                             <a href="?action='.$action.'&amp;uuid='.$_GET['uuid'].'">No, go back</a>
                        </td>
                        </tr>';
                    $die = true;
                }
            }
            if ($subaction == 'nic-add') {
                $mac = array_key_exists('mac', $_POST) ? $_POST['mac'] : false;

                if ($mac)
                    $ret = $lv->domain_nic_add($domName, $_POST['mac'], $_POST['network'], $_POST['model']) ? 'Network card has been successfully added to the guest' :
                            'Cannot add NIC to the guest: '.$lv->get_last_error();
                            else {
                    $ret = '<b>Add a new NIC device</b>
                    <form method="POST">
                    <table>
                    <tr>
                        <td>Network card MAC address: </td>
                        <td><input type="text" name="mac" value="'.$lv->generate_random_mac_addr().'" /></td>
                    </tr>
                    <tr>
                        <td>Network: </td>
                        <td>
                            <select name="network">
                            ';
                            
                    $nets = $lv->get_networks();
                    for ($i = 0; $i < sizeof($nets); $i++)
                            $ret .= '<option value="'.$nets[$i].'">'.$nets[$i].'</option>';
                    
                    $ret .= '
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Card model: </td>
                        <td>
                            <select name="model">
                            ';
                            
                    $models = $lv->get_nic_models();
                    for ($i = 0; $i < sizeof($models); $i++)
                            $ret .= '<option value="'.$models[$i].'">'.$models[$i].'</option>';
                    
                    $ret .= '
                            </select>
                        </td>
                    </tr>
                    <tr align="center">
                        <td colspan="2"><input type="submit" value=" Add new network card " /></td>
                    </tr>
                    </table>
                    </form>';
                    }
            }

            $res = $lv->get_domain_object($domName);
            $dom = $lv->domain_get_info($res);
            $mem = number_format($dom['memory'] / 1024, 2, '.', ' ').' MB';
            $cpu = $dom['nrVirtCpu'];
            $state = $lv->domain_state_translate($dom['state']);
            $id = $lv->domain_get_id($res);
            $arch = $lv->domain_get_arch($res);
            $vnc = $lv->domain_get_vnc_port($res);

            if (!$id)
                $id = 'N/A';
            if ($vnc <= 0)
                $vnc = 'N/A';

            echo "<h2>$domName - domain information</h2>";
            echo "<b>Domain type: </b>".$lv->get_domain_type($domName).'<br />';
            echo "<b>Domain emulator: </b>".$lv->get_domain_emulator($domName).'<br />';
            echo "<b>Domain memory: </b>$mem<br />";
            echo "<b>Number of vCPUs: </b>$cpu<br />";
            echo "<b>Domain state: </b>$state<br />";
            echo "<b>Domain architecture: </b>$arch<br />";
            echo "<b>Domain ID: </b>$id<br />";
            echo "<b>VNC Port: </b>$vnc<br />";
            echo '<br />';

            echo $ret;
            if ($die)
                die('</body></html');

            /* Disk information */
            echo "<h3>Disk devices</h3>";
            $tmp = $lv->get_disk_stats($domName);

            if (!empty($tmp)) {
                echo "<table class='table table-bordered'>
                              <tr>
                                <th>Disk storage</th>
                                <th>$spaces Storage driver type $spaces</th>
                                <th>$spaces Domain device $spaces</th>
                                <th>$spaces Disk capacity $spaces</th>
                    <th>$spaces Disk allocation $spaces</th>
                    <th>$spaces Physical disk size $spaces</th>
                    <th>$spaces Actions $spaces</th>
                      </tr>";

                for ($i = 0; $i < sizeof($tmp); $i++) {
                    $capacity = $lv->format_size($tmp[$i]['capacity'], 2);
                    $allocation = $lv->format_size($tmp[$i]['allocation'], 2);
                    $physical = $lv->format_size($tmp[$i]['physical'], 2);
                    $dev = (array_key_exists('file', $tmp[$i])) ? $tmp[$i]['file'] : $tmp[$i]['partition'];

                    echo "<tr>
                                   <td>$spaces".basename($dev)."$spaces</td>
                                   <td align=\"center\">$spaces{$tmp[$i]['type']}$spaces</td>
                                   <td align=\"center\">$spaces{$tmp[$i]['device']}$spaces</td>
                                   <td align=\"center\">$spaces$capacity$spaces</td>
                                   <td align=\"center\">$spaces$allocation$spaces</td>
                                   <td align=\"center\">$spaces$physical$spaces</td>
                                   <td align=\"center\">$spaces
                                        <a href=\"?action=$action&amp;uuid={$_GET['uuid']}&amp;subaction=disk-remove&amp;dev={$tmp[$i]['device']}\">
                                            Remove disk device</a>
                                $spaces</td>
                                  </tr>";
                        }
                echo "</table>";
            }
            else
                echo "Domain doesn't have any disk devices";

                echo "<br />$spaces<a href=\"?action=$action&amp;uuid={$_GET['uuid']}&amp;subaction=disk-add\">Add new disk</a>";

                /* Network interface information */
                echo "<h3>Network devices</h3>";
                $tmp = $lv->get_nic_info($domName);
                if (!empty($tmp)) {
                    $anets = $lv->get_networks(VIR_NETWORKS_ACTIVE);

                echo "<table class='table table-bordered'>
                              <tr>
                               <th>MAC Address</th>
                               <th>$spaces NIC Type$spaces</th>
                               <th>$spaces Network$spaces</th>
                               <th>$spaces Network active$spaces</th>
                    <th>$spaces Actions $spaces</th>
                              </tr>";

                    for ($i = 0; $i < sizeof($tmp); $i++) {
                        if (in_array($tmp[$i]['network'], $anets))
                            $netUp = 'Yes';
                        else
                            $netUp = 'No <a href="?action=virtual-networks&amp;subaction=start&amp;name='.$tmp[$i]['network'].'">[Start]</a>';

                        echo "<tr>
                                   <td>$spaces{$tmp[$i]['mac']}$spaces</td>
                                   <td align=\"center\">$spaces{$tmp[$i]['nic_type']}$spaces</td>
                                   <td align=\"center\">$spaces{$tmp[$i]['network']}$spaces</td>
                                   <td align=\"center\">$spaces$netUp$spaces</td>
                                   <td align=\"center\">$spaces
                                        <a href=\"?action=$action&amp;uuid={$_GET['uuid']}&amp;subaction=nic-remove&amp;mac={$tmp[$i]['mac']}\">
                                            Remove network card</a>
                                $spaces</td>                               
                                  </tr>";
                    }
                    echo "</table>";
                    
                    echo "<br />$spaces<a href=\"?action=$action&amp;uuid={$_GET['uuid']}&amp;subaction=nic-add\">Add new network card</a>";
                }
                else
                    echo 'Domain doesn\'t have any network devices';

                if ( $dom['state'] == 1 ) {
                    echo "<h3>屏幕截图</h3><img src=\"screenshot.php?uuid={$_GET['uuid']}\">";
                }
        ?>
    </div>
</div>