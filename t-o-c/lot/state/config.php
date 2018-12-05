<?php

// 1: Automatic TOC and section ID
// 2: Automatic section ID only

return [
    'type' => 1,
    'toc' => [
        'id' => ['toc:%{id}%', 'stage:%{id}%', 'point:%{id}%', 'a:%{id}%'],
        'class' => ['toc', 'toc-stage', 'toc-point', 'toc-a'],
        'class/x' => ["", 'not-toc-stage', 'not-toc-point']
    ]
];