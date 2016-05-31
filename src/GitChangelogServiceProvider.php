<?php namespace Cheycron\Gitchangelog;

use Cheycron\Gitchangelog\GitChangelog;
use Cheycron\Gitchangelog\GitChangelogFacade;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class GitChangelogServiceProvider extends ServiceProvider
{

    public function boot()
    {

    }

    public function register()
    {

        $this->app->singleton('GitChangelog', function ($app) {
            return new GitChangelog($app);
        });

        $loader = AliasLoader::getInstance();
        $loader->alias('GitChangelog', GitChangelogFacade::class);
    }
}
