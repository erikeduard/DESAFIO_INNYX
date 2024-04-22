
<?php

use Doctum\RemoteRepository\GitHubRemoteRepository;

$dir = 'backend/app';
return new Doctum\Doctum($dir,[
    'title'=> 'Desafio FullStack Documetation API',
    'build_dir'=> __DIR__.'/docs',
    'remote_repository'    => new GitHubRemoteRepository('erikeduard/Desafio', dirname($dir,2)),
]);