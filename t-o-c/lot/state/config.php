<?php

// `0`: Disable TOC
// `1`: Automatic section ID and TOC
// `2`: Automatic section ID only

// `true`: Alias for `1`
// `false`: Alias for `0`

return [
    'type' => 1,
    'id' => [
        0 => 't-o-c:%{id}%',
        1 => ["", 'stage:%{id}%', 'point:%{id}%'],
        2 => 'a:%{id}%'
    ],
    'class' => [
        0 => 't-o-c',
        1 => ["", 't-o-c:stage', 't-o-c:point'],
        2 => 't-o-c:a',
        3 => 'x:t-o-c'
    ]
];