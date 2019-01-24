<?php

// `0`: Disable TOC
// `1`: Automatic section ID and TOC
// `2`: Automatic section ID only

// `true`: Alias for `1`
// `false`: Alias for `0`

return [
    'type' => 1,
    'id' => ['t-o-c:%{id}%', 'stage:%{id}%', 'point:%{id}%', 'a:%{id}%'],
    'class' => ['t-o-c', 't-o-c:stage', 't-o-c:point', 't-o-c:a', 'x:t-o-c']
];