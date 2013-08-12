libvirt-php-examples
====================

libvirt-php 例子

https://github.com/xpmozong/libvirt-php-examples/blob/master/vm.jpg

Libvirt 库是一种实现 Linux 虚拟化功能的 Linux® API，它支持各种虚拟机监控程序，包括 Xen 和 KVM，以及 QEMU 和用于其他操作系统的一些虚拟产品。

一、安装libvirt环境
(1)、yum install virt-manager libvirt libvirt-python python-virtinst -y
(2)、升级
yum -y install gcc gcc-c++
yum install libxml2-devel gnutls-devel device-mapper-devel python-devel libnl-devel -y
wget http://libvirt.org/sources/libvirt-1.1.1.tar.gz
tar xzvf libvirt-0.9.4.tar.gz
cd libvirt-0.9.4
./configure
make && make install

[root@localhost ~]# service libvirtd start

virsh -version 可能会报错
1、error: Failed to connect socket to '/usr/local/var/run/libvirt/libvirt-sock': No such file or directory
做个链接：
ln -s /var/run/libvirt/libvirt-sock /usr/local/var/run/libvirt/libvirt-sock
2、libvirt: Remote error : unable to connect to '/usr/local/var/run/libvirt/libvirt-sock-ro': No such file or directory”
再做个链接
ln -s /var/run/libvirt/libvirt-sock-ro /usr/local/var/run/libvirt/libvirt-sock-ro

[root@localhost ~]# virsh -version
0.10.0
[root@localhost ~]# libvirtd --version
libvirtd (libvirt) 1.1.1

重启下电脑
[root@localhost ~]# virsh -version
1.1.1

二、创建虚拟机
1、demo.xml
<pre class="brush: php;">
    <domain type='kvm'>
        <name>myvm</name> <!-- 名称 -->
        <memory>10485760</memory> <!-- 空间 -->
        <currentMemory>10485760</currentMemory>
        <vcpu>1</vcpu>
        <os>
        <type arch='x86_64' machine='pc'>hvm</type> <!-- 系统 -->
        <boot dev='cdrom'/> <!-- 从光驱iso启动 -->
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
            <source file='/home/data/img/L-CentOS-6.3-x64.img'/>    <!-- 一个img，我不懂 -->
            <target dev='hda' bus='ide'/>
        </disk>
        <disk type='file' device='cdrom'>
            <source file='/home/data/iso/GamewaveOS-0.4-x86_64.iso'/> <!-- 镜像 -->
            <target dev='hdb' bus='ide'/>
            <readonly/>
        </disk>
        <interface type='bridge'>
            <source bridge='virbr0'/>
            <mac address="00:16:3e:5d:aa:a8"/>
        </interface>
        <input type='mouse' bus='ps2'/>
        <graphics type='vnc' port='-1' autoport='yes' keymap='en-us' listen='0.0.0.0'/> <!-- ip 端口5900 累计加的 -->
        </devices>
    </domain>

</pre>

2、定义KVM
[root@localhost ~]# virsh define demo.xml 
Domain myvm defined from demo.xml

3、启动KVM
[root@localhost ~]# virsh start myvm
Domain myvm started
myvm 只的是demo.xml里的虚拟机名称

4、查看vnc信息
[root@localhost ~]# virsh vncdisplay myvm
127.0.0.1:2

5、如何修改
 方法1：
 virsh edit <Name of KVM>
 virsh define <Name of XML definition file>
 
 方法2：
 virsh undefine <Name of KVM>
 virsh define <Name of XML definition file>

 先要关闭虚拟机 virsh destroy myvm

6、查看运行中的虚拟机
[root@localhost ~]# virsh list
[root@localhost ~]# virsh list --all

三、用python创建虚拟机
demo.xml  注意，虚拟机名字要换一下
<pre class="brush: py;">
    #encoding:utf8
    import libvirt

    uri='qemu:///system'
    conn =libvirt.open(uri) #这里要用读写的方式打开连接  

    with open('demo.xml') as f:  
        xml = f.read()  

    domain = conn.defineXML(xml)  
    domain.createWithFlags(0)  
      
    try:  
        dom0 = conn.lookupByName("pyvm")  #虚拟机名称
    except:  
        print 'Failed to find the main domain'  
        sys.exit(1)  
      
    print "Domain 0: id %d running %s" % (dom0.ID(), dom0.OSType())  
    print dom0.info()
</pre>

四、用php创建虚拟机
再安装依赖包：yum -y install gcc gcc.c++ zlib libxml2 libxml2-devel libmcrypt libcrypt-devel libmhash libjpeg libpng
然后安装PHP APACHE MYSQL
yum -y install httpd httpd-devel mysql-server mysql-devel php php-devel php-gd php-mysql
然后再装libvirt-php
wget http://libvirt.org/sources/php/libvirt-php-0.4.8.tar.gz
tar -zxvf libvirt-php-0.4.8.tar.gz
然后cd libvirt-php*
./configure
编译的时候如果报错，提示You need libvirt 说明libvirt开发包没有安装好，运行
yum -y install libvirt-devel （一定要这一步）
最好运行
make && make install

print_r( libvirt_version() );  有结果，就表示，安装成功啦


可能有人不喜欢apache，ok，就安装nginx呗，顺便 yum -y install php-fpm
启动service php-fpm start
这里就不详细说啦


从 https://github.com/xpmozong/libvirt-php-examples 下载例子
[root@localhost examples]# php create.php  创建
[root@localhost examples]# php test.php    列出虚拟机列表
其中libvirt.php 里有很多函数，没写全，要添加的话参考http://libvirt.org/php/api-reference.html

php有执行libvirt的权限，但是apache、nginx没有这个权限，所以要赋予权限。
还有一点，就是虚拟机创建好后，要用TigerVNC，连接过去，是个装机的界面。

[root@localhost examples]# netstat -ntlp
Active Internet connections (only servers)
Proto Recv-Q Send-Q Local Address               Foreign Address             State       PID/Program name   
tcp        0      0 0.0.0.0:5900                0.0.0.0:*                   LISTEN      3118/qemu-kvm       
tcp        0      0 127.0.0.1:5901              0.0.0.0:*                   LISTEN      22459/qemu-kvm      
tcp        0      0 127.0.0.1:5902              0.0.0.0:*                   LISTEN      22607/qemu-kvm 

看到上面的了吗？有些不同吧，是的，0.0.0.0:5900在外网可以访问，127.0.0.1:5901只能在内网访问，区别在于配置文件listen='0.0.0.0'
brctl show 查看网桥
