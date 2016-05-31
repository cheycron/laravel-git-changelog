<?php namespace Cheycron\Gitchangelog;

class GitChangelog
{

    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function currentVersion($full = false){
        exec('git describe --always', $version_mini_hash);
        exec('git rev-list HEAD | wc -l', $version_number);
        exec('git log -1', $line);
        return "v1." . trim($version_number[0]) . "." . $version_mini_hash[0] . ($full ? ' (' . str_replace('commit ', '', $line[0]) . ') ' : '');
    }

}