<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>虚拟机管理</title>
<link type="text/css" rel="stylesheet" href="bootstrap/css/bootstrap.min.css"/>
<link type="text/css" rel="stylesheet" href="css/style.css"/>
<script type="text/javascript" src="js/jquery-1.4.3.min.js"></script>
</head>
<body>
<?php
require('libvirt.php');
$lv = new Libvirt('qemu:///system');
$hn = $lv->get_hostname();
if ($hn == false)
    die('Cannot open connection to hypervisor</body></html>');

$uri = $lv->get_uri();
$tmp = $lv->get_domain_count();

?>
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="brand" href="index.php">虚拟机管理</a>
            <div class="nav-collapse collapse">
                <ul class="nav">
                    <li class="active"><a href="index.php">Main page</a></li>
                    <li><a href="">Host information</a></li>
                    <li><a href="">Virtual networks</a></li>
                    <li><a href="">Node devices</a></li>
                    <li><a href="">Storage pools</a></li>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </div>
</div>

<div class="wrap">
    <div class="info">
        <p>
            <?php echo "Hypervisor URI: <i>$uri</i>, hostname: <i>$hn</i>";?>
        </p>
        <p>
            <?php echo "Statistics: {$tmp['total']} domains, {$tmp['active']} active, {$tmp['inactive']} inactive";?>
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
                <th>Name</th>
                <th>CPU#</th>
                <th>Memory</th>
                <th>Disk(s)</th>
                <th>NICs</th>
                <th>Arch</th>
                <th>State</th>
                <th>ID / VNC port</th>
                <?php if (($active > 0) && ($lv->supports('screenshot'))):?><th>Domain screenshot</th><?php endif;?>
                <th>Action</th>
            </tr>

            <?php
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

                    if ($vnc < 0)
                        $vnc = '-';
                    else
                        $vnc = $_SERVER['HTTP_HOST'].':'.$vnc;

                    unset($tmp);
                    if (!$id)
                        $id = '-';
                    unset($dom);

                    echo "<tr>
                            <td>
                            <a href=\"?action=domain-information&amp;uuid=$uuid\">$name</a>
                            </td>
                            <td>$cpu</td>
                            <td>$mem</td>
                            <td title='$diskdesc'>$disks</td>
                            <td>$nics</td>
                            <td>$arch</td>
                            <td>$state</td>
                            <td>$id / $vnc</td>";

                    if (($active > 0) && ($lv->supports('screenshot')))
                        echo "
                            <td><img src=\"?action=get-screenshot&uuid=$uuid&width=120\" id=\"screenshot$i\"></td>
                        ";

                        echo "
                            <td>
                        ";

                    if ($lv->domain_is_running($res, $name))
                        echo "<a href=\"?action=domain-stop&amp;uuid=$uuid\">Stop domain</a> | <a href=\"?action=domain-destroy&amp;uuid=$uuid\">Destroy domain</a> |";
                    else
                        echo "<a href=\"?action=domain-start&amp;uuid=$uuid\">Start domain</a> |";

                    echo "
                                <a href=\"?action=domain-get-xml&amp;uuid=$uuid\">Dump domain</a>
                        ";

                    if (!$lv->domain_is_running($res, $name))
                        echo "| <a href=\"?action=domain-edit&amp;uuid=$uuid\">Edit domain XML</a>";
                    else
                    if ($active > 0)
                        echo "| <a href=\"?action=get-screenshot&amp;uuid=$uuid\">Get screenshot</a>";

                    echo "
                            
                            </td>
                          </tr>";
                }
            ?>
        </table>
    </div>
</div>
</body>
</html>