<?php

function fn_toc($content, $lot = [], $that = null) {
    global $language, $site;
    if (
        // No content…
        !$content ||
        // Is error page…
        $site->is('error') ||
        // Is in page(s) view…
        $site->is('pages') ||
        // No header(s)…
        stripos($content, '</h') === false
    ) {
        // Skip!
        return $content;
    }
    // Disabled by the `toc` field, skip…
    if ($that->toc !== null && !$that->toc) {
        return $content;
    }
    // Add the CSS file only if needed
    Asset::set(__DIR__ . DS . 'lot' . DS . 'asset' . DS . 'css' . DS . 'toc.min.css');
    Config::set('_toc_id', Config::get('_toc_id', 0) + 1);
    $state = Plugin::state('t-o-c');
    $pattern = '#<h([1-6])(\s.*?)?>([\s\S]*?)<\/h\1>#i';
    $depth = $level = 0;
    $toc = "";
    $toc_id = Config::get('_toc_id');
    $toc_title = $language->toc;
    $id = $state['toc']['id'];
    $type = $state['type'];
    $class = $state['toc']['class'];
    $class_x = $state['toc']['class_x'][$type];
    if ($block = Extend::exist('block')) {
        $union = Extend::state('block', 'union');
        $o = $union[1][0][0]; // `[[`
        $c = $union[1][0][1]; // `]]`
        $d = $union[1][0][2]; // `/`
        $s = $union[1][1][3]; // ` `
        if (
            $type === 1 &&
            // `[[toc]]`
            strpos($content, $o . 'toc' . $c) === false &&
            // `[[toc/]]`
            strpos($content, $o . 'toc' . $d . $c) === false &&
            // `[[toc `
            strpos($content, $o . 'toc' . $s) === false
        ) {
            $content = Block::unite('toc', false, ['title' => $toc_title]) . "\n\n" . $content;
        }
    }
    $v = explode(',', trim(str_replace(',a,', "", ',' . HTML_WISE_I . ','), ','));
    $dupe = [];
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
                $toc .= '<li id="' . __replace__($id[3], ['id' => $toc_id . '-' . ($i + 1)]) . '">';
                if (stripos($lot[0][$i], ' id="') !== false && preg_match('#\bid="(.*?)"#i', $lot[0][$i], $s)) {
                    $toc .= '<a href="#' . $s[1] . '">';
                } else {
                    $slug = To::slug($title);
                    // Append unique number to header ID if it is already exists
                    if (isset($dupe[$slug])) {
                        ++$dupe[$slug];
                    } else {
                        $dupe[$slug] = 0;
                    }
                    $toc .= '<a href="#' . __replace__($id[$type], ['id' => $slug . ($dupe[$slug] !== 0 ? '.' . $dupe[$slug] : "")]) . '">';
                }
                $toc .= $title . '</a>&#x00A0;<span class="' . $class[3] . '"></span>';
                $depth = $level;
            }
        }
        $toc .= str_repeat('</li></ol>', $depth - ((int) $lot[1][0]) + 1);
        $toc = '<div class="' . $class[0] . '" id="' . __replace__($id[0], ['id' => $toc_id]) . '"><div class="' . $class[0] . '-header"><h3>' . X . '</h3></div><div class="' . $class[0] . '-body">' . $toc . '</div></div>';
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
                    $attr .= ' id="' . __replace__($id[$type], ['id' => To::slug($lot[3])]) . '"';
                }
                if ($type === 1) {
                    $mark = '<a class="' . $class[3] . '" href="#' . __replace__($id[3], ['id' => $toc_id . '-' . $i]) . '"></a>';
                } else if ($type === 2) {
                    if (strpos($lot[2], ' id="') !== false && preg_match('#\bid="(.*?)"#i', $lot[2], $s)) {
                        $mark = '<a class="' . $class[3] . '" href="#' . $s[1] . '"></a>';
                    } else {
                        $slug = To::slug($lot[3]);
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
        return $block ? Block::replace('toc', function($content, $attr) use($toc, $toc_title) {
            return str_replace(X, !empty($attr['title']) ? $attr['title'] : $toc_title, $toc);
        }, $content) : str_replace(X, $toc_title, $toc) . $content;
    }
}

Hook::set('page.content', 'fn_toc', 10);