<?php namespace _\lot\x;

function t_o_c($content) {
    $block = \State::get('x.block', true);
    $hash = \P . $this->path . \P;
    if (
        // No content…
        !$content ||
        // Is error page…
        \State::is('error') ||
        // Is in page(s) view…
        \State::is('pages') ||
        // No header(s)…
        false === \stripos($content, '</h')
    ) {
        // Skip!
        return $block ? \Block::alter('t-o-c', "", $content) : $content;
    }
    // Disabled by the `state.t-o-c` field, skip…
    $test = $this->get('state.t-o-c') ?? $hash;
    if ($test !== $hash && !$test) {
        return $content;
    }
    $state = \State::get('x.t-o-c', true);
    if (true === $test || 1 === $test || 2 === $test) {
        $test = ['type' => true === $test ? 1 : $test];
    }
    $state = \array_replace_recursive($state, (array) $test);
    $type = $state['type'];
    // Disabled by the `type` state, skip…
    if (false === $type || 0 === $type) {
        return $content;
    }
    // Add the CSS file only if needed
    \Asset::set(__DIR__ . \DS . 'lot' . \DS . 'asset' . \DS . 'css' . \DS . 't-o-c.min.css', 20.1);
    \State::set([
        '[layout]' => ['t-o-c:' . $type => true],
        '[t-o-c]' => (\State::get('[t-o-c]') ?? 0) + 1,
        'has' => ['t-o-c' => true]
    ]);
    $pattern = '/<h([1-6])(\s[^>]*)?>([\s\S]*?)<\/h\1>/i';
    $depth = $level = 0;
    $out = "";
    $out_id = \State::get('[t-o-c]');
    $out_title = \i('Table of Content');
    $id = $state['id'];
    $class = $state['class'];
    if ($block) {
        $c = \Block::$state;
        $open = $c[0][0]; // `[[`
        $close = $c[0][1]; // `]]`
        $end = $c[0][2]; // `/`
        if (
            (true === $type || 1 === $type) &&
            // `[[t-o-c]]`
            false === \strpos($content, $open . 't-o-c' . $close) &&
            // `[[t-o-c/]]`
            false === \strpos($content, $open . 't-o-c' . $end . $close) &&
            // `[[t-o-c `
            false === \strpos($content, $open . 't-o-c ')
        ) {
            $content = (new \Block([
                0 => 't-o-c',
                1 => false,
                2 => ['title' => $out_title]
            ])) . "\n\n" . $content;
        }
    }
    $dupe = [];
    if (\preg_match_all($pattern, $content, $m)) {
        for ($i = 0, $count = \count($m[0]); $i < $count; ++$i) {
            $level = (int) $m[1][$i];
            if (false === \strpos($m[0][$i], $class[3])) {
                if ($level > $depth) {
                    $out .= '<ol>';
                } else {
                    $out .= \str_repeat('</li></ol>', $depth - $level);
                    $out .= '</li>';
                }
                $title = \w($m[3][$i], 'abbr,b,br,cite,code,del,dfn,em,i,ins,kbd,mark,q,span,strong,sub,sup,svg,time,u,var');
                $out .= '<li id="' . \sprintf($id[2], $out_id . '.' . ($i + 1)) . '">';
                if (false !== \stripos($m[0][$i], ' id="') && \preg_match('/\bid="(.*?)"/i', $m[0][$i], $s)) {
                    $out .= '<a href="#' . $s[1] . '">';
                } else {
                    $kebab = \To::kebab($title);
                    // Append unique number to header ID if it is already exists
                    if (isset($dupe[$kebab])) {
                        ++$dupe[$kebab];
                    } else {
                        $dupe[$kebab] = 0;
                    }
                    $out .= '<a href="#' . \sprintf($id[1], $kebab . (0 !== $dupe[$kebab] ? '.' . $dupe[$kebab] : "")) . '">';
                }
                $out .= $title . '</a>&#x00A0;<span class="' . $class[2] . '"></span>';
                $depth = $level;
            }
        }
        $out .= \str_repeat('</li></ol>', $depth - ((int) $m[1][0]) + 1);
        $out = '<details class="' . $class[0] . ' p" id="' . \sprintf($id[0], $out_id) . '"' . (!empty($state['open']) ? ' open' : "") . '><summary>' . \P . '</summary>' . $out . '</details>';
        $i = 0;
        $dupe = [];
        $content = \preg_replace_callback($pattern, function($m) use($type, $id, $class, $out_id, &$i, &$dupe) {
            if (false === \strpos($m[2], $class[3])) {
                ++$i;
                $kebab = \To::kebab($m[3]);
                // Append unique number to header ID if it is already exists
                if (isset($dupe[$kebab])) {
                    ++$dupe[$kebab];
                } else {
                    $dupe[$kebab] = 0;
                }
                if (true === $type || 1 === $type) {
                    $mark = '<a class="' . $class[2] . '" href="#' . \sprintf($id[2], $out_id . '.' . $i) . '"></a>';
                } else if (2 === $type) {
                    if (false !== \strpos($m[2], ' id="') && \preg_match('/\bid="(.*?)"/i', $m[2], $s)) {
                        $mark = '<a class="' . $class[2] . '" href="#' . $s[1] . '"></a>';
                    } else {
                        $mark = '<a class="' . $class[2] . '" href="#' . \sprintf($id[1], $kebab . (0 !== $dupe[$kebab] ? '.' . $dupe[$kebab] : "")) . '"></a>';
                    }
                }
                if (false === \strpos($m[2], ' class="')) {
                    $attr = ' class="' . $class[1] . '"' . $m[2];
                } else {
                    $attr = \str_replace(' class="', ' class="' . $class[1] . ' ', $m[2]);
                }
                if (false === \strpos($m[2], ' id="')) {
                    $attr .= ' id="' . \sprintf($id[1], $kebab . (0 !== $dupe[$kebab] ? '.' . $dupe[$kebab] : "")) . '"';
                }
                return '<h' . $m[1] . $attr . '>' . $m[3] . '&#x00A0;' . $mark . '</h' . $m[1] . '>';
            }
            return $m[0];
        }, $content);
        return $block ? \Block::alter('t-o-c', function($content, $attr) use($out, $out_title) {
            return \strtr($out, [\P => $attr['title'] ?? $out_title]);
        }, $content) : (1 === $type ? \strtr($out, [\P => $out_title]) : "") . $content;
    }
}

\Hook::set('page.content', __NAMESPACE__ . "\\t_o_c", 10);