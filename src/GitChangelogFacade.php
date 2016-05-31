<?php namespace Cheycron\Gitchangelog;

use Illuminate\Support\Facades\Facade;

class GitChangelogFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'GitChangelog';
    }
}