<?php

$content = file_get_contents(__DIR__ . D . 'test.txt');

echo '<style>:target{background:#ff0}</style>';
echo x\t_o_c\to\tree($content) . x\t_o_c\to\content($content);

exit;