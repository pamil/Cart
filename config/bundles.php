<?php

return [
    \Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    \Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    \Broadway\Bundle\BroadwayBundle\BroadwayBundle::class => ['all' => true],
    \Snc\RedisBundle\SncRedisBundle::class => ['all' => true],

    \Pamil\CommandCartBundle\PamilCommandCartBundle::class => ['all' => true],
    \Pamil\QueryCartBundle\PamilQueryCartBundle::class => ['all' => true],
];
