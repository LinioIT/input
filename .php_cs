<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        'concat_space' => ['spacing' => 'one'],
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => true,
        'pre_increment' => false,
        'phpdoc_order' => true,
        'blank_line_after_opening_tag' => true,
        'phpdoc_align' => false,
    ])
    ->setFinder($finder)
    ->setUsingCache(true);
