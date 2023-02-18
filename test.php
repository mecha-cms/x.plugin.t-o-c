<?php

$content = file_get_contents(__DIR__ . D . 'test');

echo '<style>:target{background:#ff0}</style>';
echo x\t_o_c\tree($content) . x\t_o_c\content($content);

exit;