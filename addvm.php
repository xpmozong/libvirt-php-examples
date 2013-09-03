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
                    <select name="memory">
                        <option value="1">1G</option>
                        <option value="2">2G</option>
                        <option value="4">4G</option>
                        <option value="6">6G</option>
                        <option value="8">8G</option>
                        <option value="10">10G</option>
                        <option value="12">12G</option>
                        <option value="14">14G</option>
                        <option value="16">16G</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputHvm">系统位数</label>
                <div class="controls">
                    <input type="text" id="inputHvm" name="hvm" value="x86_64" readonly>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputVcpu">CPU个数</label>
                <div class="controls">
                    <select name="vcpu">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="4">4</option>
                        <option value="6">6</option>
                        <option value="8">8</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputIso">光驱</label>
                <div class="controls">
                    <input type="text" id="inputIso" name="iso" value="/home/data/iso/GamewaveOS-0.4-x86_64.iso" readonly>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputBridge">网桥名称</label>
                <div class="controls">
                    <input type="text" id="inputBridge" name="bridge" value="public" readonly>
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
