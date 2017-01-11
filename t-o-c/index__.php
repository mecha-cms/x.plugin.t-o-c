<?php

function fn_toc($content, $lot) {
    global $site;
    // No header(s), skip anyway …
    if (!$content || $site->type === 'pages' || stripos($content, '</h') === false) {
        return $content;
    }
    // Disabled by the `toc` field, skip …
    if (isset($lot['toc']) && !$lot['toc']) {
        return $content;
    }
    Config::set('toc_id', Config::get('toc_id', 0) + 1);
    global $language;
    $state = Plugin::state(__DIR__);
    $pattern = '#<h([1-6])(\s.*?)?>([\s\S]*?)<\/h\1>#i';
    $depth = $level = 0;
    $toc = "";
    $state_block = Extend::state('block', 'union', [
        1 => [['[[', ']]', '/']]
    ]);
    $o = $state_block[1][0][0];
    $c = $state_block[1][0][1];
    $d = $state_block[1][0][2];
    $v = explode(',', trim(str_replace(',a,', "", ',' . HTML_WISE_I . ','), ','));
    $type = $state['type'];
    $id = $state['toc']['id'];
    $class = $state['toc']['class'];
    $class_x = $state['toc']['class_x'][$type];
    $toc_id = Config::get('toc_id');
    $dupe = [];
    if ($type === 1 && strpos($content, $o . 'toc' . $c) === false && strpos($content, $o . 'toc' . $d . $c) === false) {
        $content = $o . 'toc' . $c . "\n\n" . $content;
    }
    if (preg_match_all($pattern, $content, $lot)) {
        for ($i = 0, $count = count($lot[0]); $i < $count; ++$i) {
            $level = (int) $lot[1][$i];
            if (strpos($lot[0][$i], $class_x) === false) {
                if ($level > $depth) {
                    $toc .= '<ol>';
                } else {
                    $toc .= str_repeat('</li></ol>', $depth - $level);
                    $toc .= '</li>';
                }
                $title = w($lot[3][$i], $v);
                $slug = h($title);
                // Append unique number to header ID if it is already exists
                if (isset($dupe[$slug])) {
                    ++$dupe[$slug];
                } else {
                    $dupe[$slug] = 0;
                }
                $toc .= '<li id="' . __replace__($id[3], ['id' => $toc_id . '-' . ($i + 1)]) . '">';
                if (stripos($lot[0][$i], ' id="') !== false && preg_match('#\bid="(.*?)"#i', $lot[0][$i], $s)) {
                    $toc .= '<a href="#' . __replace__($id[$type], ['id' => $s[1]]) . '">';
                } else {
                    $toc .= '<a href="#' . __replace__($id[$type], ['id' => $slug . ($dupe[$slug] !== 0 ? '.' . $dupe[$slug] : "")]) . '">';
                }
                $toc .= $title . '</a>&#x00A0;<span class="' . $class[3] . '"></span>';
                $depth = $level;
            }
        }
        $toc .= str_repeat('</li></ol>', $depth - ((int) $lot[1][0]) + 1);
        $toc = '<div class="' . $class[0] . '" id="' . __replace__($id[0], ['id' => $toc_id]) . '"><div class="' . $class[0] . '-header"><h3>' . $language->toc . '</h3></div><div class="' . $class[0] . '-body">' . $toc . '</div></div>';
        $i = 0;
        $dupe = [];
        $content = preg_replace_callback($pattern, function($lot) use($language, $type, $id, $class, $class_x, $toc_id, &$i, &$dupe) {
            if (strpos($lot[2], $class_x) === false) {
                ++$i;
                if (strpos($lot[2], ' class="') === false) {
                    $attr = ' class="' . $class[$type] . '"' . $lot[2];
                } else {
                    $attr = str_replace(' class="', ' class="' . $class[$type] . ' ', $lot[2]);
                }
                if (strpos($lot[2], ' id="') === false) {
                    $attr .= ' id="' . __replace__($id[$type], ['id' => h($lot[3])]) . '"';
                }
                if ($type === 1) {
                    $mark = '<a class="' . $class[3] . '" href="#' . __replace__($id[3], ['id' => $toc_id . '-' . $i]) . '"></a>';
                } else if ($type === 2) {
                    if (strpos($lot[2], ' id="') !== false && preg_match('#\bid="(.*?)"#i', $lot[2], $s)) {
                        $mark = '<a class="' . $class[3] . '" href="#' . $s[1] . '"></a>';
                    } else {
                        $slug = h($lot[3]);
                        // Append unique number to header ID if it is already exists
                        if (isset($dupe[$slug])) {
                            ++$dupe[$slug];
                        } else {
                            $dupe[$slug] = 0;
                        }
                        $mark = '<a class="' . $class[3] . '" href="#' . __replace__($id[$type], ['id' => $slug . ($dupe[$slug] !== 0 ? '.' . $dupe[$slug] : "")]) . '"></a>';
                    }
                }
                return '<h' . $lot[1] . $attr . '>' . $lot[3] . '&#x00A0;' . $mark . '</h' . $lot[1] . '>';
            }
            return $lot[0];
        }, $content);
        return Block::replace('toc', $toc, $content);
    }
}

Hook::set('page.content', 'fn_toc');

Asset::set(__DIR__ . DS . 'lot' . DS . 'asset' . DS . 'css' . DS . 'toc.min.css');