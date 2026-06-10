<?php
class TestSwoole extends Swoole\Server {
    public array $fdlist = [];
}
$s = new TestSwoole('127.0.0.1', 9999);
$s->fdlist['test'] = 1;
echo count($s->fdlist) === 1 ? "Properties work!\n" : "Failed\n";
