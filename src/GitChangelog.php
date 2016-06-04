<?php namespace Cheycron\Gitchangelog;

use Carbon\Carbon;
use League\CommonMark\CommonMarkConverter;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

class GitChangelog
{

    public $changelog;
    protected $appendToVersion;
    protected $branch;
    protected $prependToVersion;
    protected $rawOutput;
    protected $workingPath;

    public function __construct()
    {
        $this->appendToVersion = 'v1.';
        $this->branch = 'HEAD';
        $this->changelog = collect();
        $this->prependToVersion = '';
        $this->workingPath = app_path();
    }

    public function parse()
    {

        return $this->getParsedOutput();
    }

    protected function getParsedOutput()
    {
        $output = $this->getCommandOutput(['git', 'log', $this->branch]);
        $lines = explode("\n", $output);
        $version = $this->getCurrentVersion();
        $commit = collect();
        foreach ($lines as $key => $line) {
            if (strpos($line, 'commit') === 0 || $key + 1 == count($lines)) {
                if (!$commit->isEmpty()) {
                    $converter = new CommonMarkConverter();
                    $commit->put('markdown', $converter->convertToHtml($commit->get('message')));
                    $commit->put('subject', trim(explode("\n", $commit->get('message'))[0]));
                    $commit->put('version', $this->appendToVersion . $version . $this->prependToVersion);
                    $this->changelog->push($commit->toArray());
                    $version--;
                    $commit = collect();
                }
                $commit->put('hash', substr($line, strlen('commit') + 1));
            } else if (strpos($line, 'Author') === 0) {
                preg_match_all("/(?:[a-z0-9!#$%&'*+=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+=?^_`{|}~-]+)*|\"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*\")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/", $line, $emails);
                $commit->put('email', array_first(array_first($emails)));
                $commit->put('author', trim(str_replace([$commit->get('email'), '<', '>'], '', substr($line, strlen('Author:') + 1))));
            } else if (strpos($line, 'Date') === 0) {
                $commit->put('date', Carbon::createFromFormat('D M d H:i:s Y O', substr($line, strlen('Date:') + 3)));
            } elseif (strpos($line, 'Merge') === 0) {
                $commit->put('merge', explode(' ', substr($line, strlen('Merge:') + 1)));
            } elseif (!empty($line)) {
                if ($commit->has('message')) {
                    $commit->put('message', $commit->get('message') . "\n" . trim($line));
                } else {
                    $commit->put('message', trim($line));
                }
            }
        }

        return $this;
    }

    protected function getCommandOutput(array $arguments)
    {
        $process = $this->getCommand($arguments);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        return $process->getOutput();
    }

    protected function getCommand(array $arguments)
    {
        return $this->buildCommand($arguments)->getProcess()->setWorkingDirectory($this->workingPath);
    }

    protected function buildCommand(array $arguments)
    {
        return new ProcessBuilder($arguments);
    }

    protected function getCurrentVersion()
    {
        $version_number = $this->getCommandOutput(['git', 'rev-list', $this->branch]);
        return count(explode("\n", $version_number)) - 2;
    }

    public function currentVersion()
    {
        return $this->appendToVersion . $this->getCurrentVersion() . $this->prependToVersion;
    }

    public function branch($value)
    {
        $this->branch = $value;

        return $this;
    }

    public function append($value)
    {
        $this->appendToVersion = $value;

        return $this;
    }

    public function prepend($value)
    {
        $this->prependToVersion = $value;

        return $this;
    }

    public function workingPath($value)
    {
        $this->workingPath = $value;

        return $this;
    }

}