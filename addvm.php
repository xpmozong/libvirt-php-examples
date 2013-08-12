<?php
require('header.php');
?>
<style type="text/css">
input{ width: 50%;}
</style>
<div class="wrap">
    <div class="info">
        <h2>添加虚拟机</h2>
    </div>
    <div class="list">
        <form class="form-horizontal" method="post" action="create_xml.php">
            <div class="control-group">
                <label class="control-label" for="inputName">虚拟机名称</label>
                <div class="controls">
                    <input type="text" id="inputName" name="vmname">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMemory">内存（单位：G）</label>
                <div class="controls">
                    <input type="text" id="inputMemory" name="memory">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputCMemory">最大内存（单位：G）</label>
                <div class="controls">
                    <input type="text" id="inputCMemory" name="cmemory">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputHvm">系统位数</label>
                <div class="controls">
                    <input type="text" id="inputHvm" name="hvm">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputVcpu">CPU个数</label>
                <div class="controls">
                    <input type="text" id="inputVcpu" name="vcpu">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputImg">硬盘</label>
                <div class="controls">
                    <input type="text" id="inputImg" name="img" value="/home/data/img/L-CentOS-6.3-x64.img">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputIso">光驱</label>
                <div class="controls">
                    <input type="text" id="inputIso" name="iso" value="/home/data/iso/GamewaveOS-0.4-x86_64.iso">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputBridge">网桥名称</label>
                <div class="controls">
                    <input type="text" id="inputBridge" name="bridge" value="virbr0">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputAddress">物理地址</label>
                <div class="controls">
                    <input type="text" id="inputAddress" name="address" value="00:16:3e:5d:aa:a8">
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn btn-primary">提交</button>
                </div>
            </div>
        </form>
    </div>
</div>
