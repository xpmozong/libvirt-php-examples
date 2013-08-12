<?php
require('header.php');
?>
<div class="wrap">
    <div class="info">
        <h2>Networks</h2>
    </div>
    <div class="list">
        <p>
            This is the administration of virtual networks. You can see all the virtual network being available with their settings. Please make sure you're using the right network for the purpose you need to since using the isolated network between two or multiple guests is providing the sharing option but internet connectivity will be disabled. Please enable internet services only on the guests that are really requiring internet access for operation like e.g. HTTP server or FTP server but you don't need to put the internet access to the guest with e.g. MySQL instance or anything that might be managed from the web-site. For the scenario described you could setup 2 network, internet and isolated, where isolated network should be setup on both machine with Apache and MySQL but internet access should be set up just on the machine with Apache webserver with scripts to remotely connect to MySQL instance and manage it (using e.g. phpMyAdmin). Isolated network is the one that's having forwarding column set to None.
        </p>
        <?php
            $ret = false;
            if ($subaction) {
                $name = $_GET['name'];
                if ($subaction == 'start'){
                    $ret = $lv->set_network_active($name, true) ? "Network has been started successfully" : 'Error while starting network: '.$lv->get_last_error();
                } elseif ($subaction == 'stop'){
                    $ret = $lv->set_network_active($name, false) ? "Network has been stopped successfully" : 'Error while stopping network: '.$lv->get_last_error();
                } elseif (($subaction == 'dumpxml') || ($subaction == 'edit')) {
                    $xml = $lv->network_get_xml($name, false);

                    if ($subaction == 'edit') {
                        if (@$_POST['xmldesc']) {
                            $ret = $lv->network_change_xml($name, $_POST['xmldesc']) ? "Network definition has been changed" :
                                                                                        'Error changing network definition: '.$lv->get_last_error();
                        }
                        else
                            $ret = 'Editing network XML description: <br /><br /><form method="POST"><table width="100%"><tr><td width="200px">Network XML description: </td>'.
                                        '<td><textarea name="xmldesc" rows="25" style="width:80%">'.$xml.'</textarea></td></tr><tr align="center"><td colspan="2">'.
                                        '<input type="submit" value=" Edit domain XML description "></tr></form>';
                    }
                    else
                        $ret = 'XML dump of network <i>'.$name.'</i>:<br /><br />'.htmlentities($lv->get_network_xml($name, false));
                }
            }

            echo "<h3>List of networks</h3>";
            $tmp = $lv->get_networks(VIR_NETWORKS_ALL);

            echo "<table class='table table-bordered'>
                <tr>
                 <th>Network name $spaces</th>
                 <th>$spaces Network state $spaces</th>
                 <th>$spaces Gateway IP Address $spaces</th>
                 <th>$spaces IP Address Range $spaces</th>
                 <th>$spaces Forwarding $spaces</th>
                 <th>$spaces DHCP Range $spaces</th>
                 <th>$spaces Actions $spaces</th>
                </tr>";

            for ($i = 0; $i < sizeof($tmp); $i++) {
                $tmp2 = $lv->get_network_information($tmp[$i]);
                if ($tmp2['forwarding'] != 'None')
                    $forward = $tmp2['forwarding'].' to '.$tmp2['forward_dev'];
                else
                    $forward = 'None';
                if (array_key_exists('dhcp_start', $tmp2) && array_key_exists('dhcp_end', $tmp2))
                    $dhcp = $tmp2['dhcp_start'].' - '.$tmp2['dhcp_end'];
                else
                    $dhcp = 'Disabled';
                $activity = $tmp2['active'] ? 'Active' : 'Inactive';

                $act = !$tmp2['active'] ? "<a href=\"?subaction=start&amp;name={$tmp2['name']}\">Start network</a>" :
                                          "<a href=\"?subaction=stop&amp;name={$tmp2['name']}\">Stop network</a>";
                $act .= " | <a href=\"?subaction=dumpxml&amp;name={$tmp2['name']}\">Dump network XML</a>";
                if (!$tmp2['active']) {
                    $act .= ' | <a href="?subaction=edit&amp;name='.$tmp2['name'].'">Edit network</a>';
                }

                echo "<tr>
                        <td>$spaces{$tmp2['name']}$spaces</td>
                        <td align=\"center\">$spaces$activity$spaces</td>
                        <td align=\"center\">$spaces{$tmp2['ip']}$spaces</td>
                        <td align=\"center\">$spaces{$tmp2['ip_range']}$spaces</td>
                        <td align=\"center\">$spaces$forward$spaces</td>
                        <td align=\"center\">$spaces$dhcp$spaces</td>
                        <td align=\"center\">$spaces$act$spaces</td>
                      </tr>";
            }
            echo "</table>";

            if ($ret)
                echo "<pre>$ret</pre>";

        ?>
    </div>
