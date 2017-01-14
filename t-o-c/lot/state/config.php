<?php

// 1: automatic table of content and section ID
// 2: automatic section ID

return [
    'type' => 1,
    'toc' => [
        'id' => ['toc:%{id}%', 'stage:%{id}%', 'point:%{id}%', 'a:%{id}%'],
        'class' => ['toc', 'toc-stage', 'toc-point', 'toc-a'],
        'class_x' => ["", 'not-toc-stage', 'not-toc-point']
    ]
];