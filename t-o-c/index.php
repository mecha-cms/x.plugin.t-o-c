<?php namespace fn;

function t_o_c($content) {
    $block = \Extend::exist('block');
    $hash = X . $this->path . X;
    if (
        // No content…
        !$content ||
        // Is error page…
        \Config::is('error') ||
        // Is in page(s) view…
        \Config::is('pages') ||
        // No header(s)…
        \stripos($content, '</h') === false
    ) {
        // Skip!
        return $block ? \Block::replace('t-o-c', "", $content) : $content;
    }
    // Disabled by the `state.t-o-c` field, skip…
    $test = $this->get('state.t-o-c', $hash);
    if ($test !== $hash && !$test) {
        return $content;
    }
    $state = (array) \Plugin::state('t-o-c');
    if ($test === true || $test === 1 || $test === 2) {
        $test = ['type' => $test === true ? 1 : $test];
    }
    $state = \extend($state, (array) $test);
    $type = $state['type'];
    // Disabled by the `type` state, skip…
    if ($type === false || $type === 0) {
        return $content;
    }
    // Add the CSS file only if needed
    \Asset::set(__DIR__ . DS . 'lot' . DS . 'asset' . DS . 'css' . DS . 't-o-c.min.css', 20.1);
    \Config::set('_t-o-c', \Config::get('_t-o-c', 0) + 1);
    $pattern = '#<h([1-6])(\s[^<>]*?)?>([\s\S]*?)</h\1>#i';
    $depth = $level = 0;
    $out = "";
    $out_id = \Config::get('_t-o-c');
    $out_title = \Language::get('t_o_c');
    $id = $state['id'];
    $class = $state['class'];
    //$class_x = $state['class/x'][$type];
    if ($block) {
        $union = \Extend::state('block', 'union');
        $open = $union[1][0][0]; // `[[`
        $close = $union[1][0][1]; // `]]`
        $end = $union[1][0][2]; // `/`
        $separator = $union[1][1][3]; // ` `
        if (
            ($type === true || $type === 1) &&
            // `[[t-o-c]]`
            \strpos($content, $open . 't-o-c' . $close) === false &&
            // `[[t-o-c/]]`
            \strpos($content, $open . 't-o-c' . $end . $close) === false &&
            // `[[t-o-c `
            \strpos($content, $open . 't-o-c' . $separator) === false
        ) {
            $content = \Block::unite('t-o-c', false, ['title' => $out_title]) . "\n\n" . $content;
        }
    }
    $v = \explode(',', \trim(\str_replace(',a,', "", ',' . HTML_WISE_I . ','), ','));
    $dupe = [];
    if (\preg_match_all($pattern, $content, $m)) {
        for ($i = 0, $count = \count($m[0]); $i < $count; ++$i) {
            $level = (int) $m[1][$i];
            if (\strpos($m[0][$i], $class[4]) === false) {
                if ($level > $depth) {
                    $out .= '<ol>';
                } else {
                    $out .= \str_repeat('</li></ol>', $depth - $level);
                    $out .= '</li>';
                }
                $title = \w($m[3][$i], $v);
                $out .= '<li id="' . \candy($id[3], ['id' => $out_id . '.' . ($i + 1)]) . '">';
                if (\stripos($m[0][$i], ' id="') !== false && \preg_match('#\bid="(.*?)"#i', $m[0][$i], $s)) {
                    $out .= '<a href="#' . $s[1] . '">';
                } else {
                    $slug = \To::slug($title);
                    // Append unique number to header ID if it is already exists
                    if (isset($dupe[$slug])) {
                        ++$dupe[$slug];
                    } else {
                        $dupe[$slug] = 0;
                    }
                    $out .= '<a href="#' . \candy($id[$type], ['id' => $slug . ($dupe[$slug] !== 0 ? '.' . $dupe[$slug] : "")]) . '">';
                }
                $out .= $title . '</a>&#x00A0;<span class="' . $class[3] . '"></span>';
                $depth = $level;
            }
        }
        $out .= \str_repeat('</li></ol>', $depth - ((int) $m[1][0]) + 1);
        $out = '<div class="' . $class[0] . '" id="' . \candy($id[0], ['id' => $out_id]) . '"><div class="' . $class[0] . '-header"><h3>' . X . '</h3></div><div class="' . $class[0] . '-body">' . $out . '</div></div>';
        $i = 0;
        $dupe = [];
        $content = \preg_replace_callback($pattern, function($m) use($type, $id, $class, $out_id, &$i, &$dupe) {
            if (\strpos($m[2], $class[4]) === false) {
                ++$i;
                $slug = \To::slug($m[3]);
                // Append unique number to header ID if it is already exists
                if (isset($dupe[$slug])) {
                    ++$dupe[$slug];
                } else {
                    $dupe[$slug] = 0;
                }
                if ($type === true || $type === 1) {
                    $mark = '<a class="' . $class[3] . '" href="#' . \candy($id[3], ['id' => $out_id . '.' . $i]) . '"></a>';
                } else if ($type === 2) {
                    if (\strpos($m[2], ' id="') !== false && \preg_match('#\bid="(.*?)"#i', $m[2], $s)) {
                        $mark = '<a class="' . $class[3] . '" href="#' . $s[1] . '"></a>';
                    } else {
                        $mark = '<a class="' . $class[3] . '" href="#' . \candy($id[$type], ['id' => $slug . ($dupe[$slug] !== 0 ? '.' . $dupe[$slug] : "")]) . '"></a>';
                    }
                }
                if (\strpos($m[2], ' class="') === false) {
                    $attr = ' class="' . $class[$type] . '"' . $m[2];
                } else {
                    $attr = \str_replace(' class="', ' class="' . $class[$type] . ' ', $m[2]);
                }
                if (\strpos($m[2], ' id="') === false) {
                    $attr .= ' id="' . \candy($id[$type], ['id' => $slug . ($dupe[$slug] !== 0 ? '.' . $dupe[$slug] : "")]) . '"';
                }
                return '<h' . $m[1] . $attr . '>' . $m[3] . '&#x00A0;' . $mark . '</h' . $m[1] . '>';
            }
            return $m[0];
        }, $content);
        return $block ? \Block::replace('t-o-c', function($content, $attr) use($out, $out_title) {
            return \str_replace(X, $attr['title'] ?? $out_title, $out);
        }, $content) : \str_replace(X, $out_title, $out) . $content;
    }
}

\Hook::set('page.content', __NAMESPACE__ . "\\t_o_c", 10);