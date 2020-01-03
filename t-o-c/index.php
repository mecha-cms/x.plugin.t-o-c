<?php

namespace _\lot\x\t_o_c {
    function block($content, $fn, $state) {
        $c = \Block::$state;
        $type = $state['type'];
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
            return \_\lot\x\t_o_c\content($content, $fn, $state);
        }
        return \_\lot\x\t_o_c\content($content, function($type, $out, $content) {
            return \Block::alter('t-o-c', function($a, $b) use($out, $type) {
                return (1 === $type ? \strtr($out, [\P => $b['title'] ?? \i('Table of Contents')]) : "");
            }, $content);
        }, $state);
    }
    function content($content, $fn, $state) {
        if (
            // No content…
            !$content ||
            // No header(s)…
            false === \stripos($content, '</h')
        ) {
            // Skip!
            return;
        }
        $pattern = '/<h([1-6])(\s[^>]*)?>([\s\S]*?)<\/h\1>/i';
        $deep = $level = 0;
        $out = "";
        $out_id = \State::get('[t-o-c]');
        $type = $state['type'];
        $id = $state['id'];
        $class = $state['class'];
        $d = []; // Check for duplicate ID
        if (\preg_match_all($pattern, $content, $m)) {
            for ($i = 0, $count = \count($m[0]); $i < $count; ++$i) {
                $level = (int) $m[1][$i];
                if (false === \strpos($m[0][$i], $class[3])) {
                    if ($level > $deep) {
                        $out .= '<ol>';
                    } else {
                        $out .= \str_repeat('</li></ol>', $deep - $level);
                        $out .= '</li>';
                    }
                    $title = \w($m[3][$i], 'abbr,b,br,cite,code,del,dfn,em,i,ins,kbd,mark,q,span,strong,sub,sup,svg,time,u,var');
                    $out .= '<li id="' . \sprintf($id[2], $out_id . '.' . ($i + 1)) . '">';
                    if (false !== \stripos($m[0][$i], ' id="') && \preg_match('/\bid="(.*?)"/i', $m[0][$i], $mm)) {
                        $out .= '<a href="#' . $mm[1] . '">';
                    } else {
                        $kebab = \To::kebab($title);
                        // Append unique number to header ID if it is already exists
                        $d[$kebab] = ($d[$kebab] ?? -1) + 1;
                        $out .= '<a href="#' . \sprintf($id[1], $kebab . (0 !== $d[$kebab] ? '.' . $d[$kebab] : "")) . '">';
                    }
                    $out .= $title . '</a>&#x00A0;<span class="' . $class[2] . '"></span>';
                    $deep = $level;
                }
            }
            $out .= \str_repeat('</li></ol>', $deep - ((int) $m[1][0]) + 1);
            $out = '<details class="' . $class[0] . ' p" id="' . \sprintf($id[0], $out_id) . '"' . (!empty($state['open']) ? ' open' : "") . '><summary>' . \P . '</summary>' . $out . '</details>';
            $i = 0;
            $d = []; // Check for duplicate ID
            $content = \preg_replace_callback($pattern, function($m) use($type, $id, $class, $out_id, &$i, &$d) {
                if (false === \strpos($m[2], $class[3])) {
                    ++$i;
                    $kebab = \To::kebab($m[3]);
                    // Append unique number to header ID if it is already exists
                    $d[$kebab] = ($d[$kebab] ?? -1) + 1;
                    if (true === $type || 1 === $type) {
                        $mark = '<a class="' . $class[2] . '" href="#' . \sprintf($id[2], $out_id . '.' . $i) . '"></a>';
                    } else if (2 === $type) {
                        if (false !== \strpos($m[2], ' id="') && \preg_match('/\bid="(.*?)"/i', $m[2], $mm)) {
                            $mark = '<a class="' . $class[2] . '" href="#' . $mm[1] . '"></a>';
                        } else {
                            $mark = '<a class="' . $class[2] . '" href="#' . \sprintf($id[1], $kebab . (0 !== $d[$kebab] ? '.' . $d[$kebab] : "")) . '"></a>';
                        }
                    }
                    if (false === \strpos($m[2], ' class="')) {
                        $attr = ' class="' . $class[1] . '"' . $m[2];
                    } else {
                        $attr = \str_replace(' class="', ' class="' . $class[1] . ' ', $m[2]);
                    }
                    if (false === \strpos($m[2], ' id="')) {
                        $attr .= ' id="' . \sprintf($id[1], $kebab . (0 !== $d[$kebab] ? '.' . $d[$kebab] : "")) . '"';
                    }
                    return '<h' . $m[1] . $attr . '>' . $m[3] . '&#x00A0;' . $mark . '</h' . $m[1] . '>';
                }
                return $m[0];
            }, $content);
            return \is_callable($fn) ? \call_user_func($fn, $type, $out, $content) : (1 === $type ? \strtr($out, [\P => \i('Table of Contents')]) : "") . $content;
        }
    }
}

namespace _\lot\x {
    function t_o_c($content) {
        if (
            // Is error page…
            \State::is('error') ||
            // Is in page(s) view…
            \State::is('pages')
        ) {
            // Skip!
            return $content;
        }
        $hash = \P . $this->path . \P;
        // Disabled by the `state.t-o-c` field, skip…
        $test = $this->get('state.t-o-c') ?? $hash;
        if ($test !== $hash && !$test) {
            return $content;
        }
        if (true === $test || 1 === $test || 2 === $test) {
            $test = ['type' => true === $test ? 1 : $test];
        }
        $state = \array_replace_recursive(\State::get('x.t-o-c', true), (array) $test);
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
        return \call_user_func("\\_\\lot\\x\\t_o_c\\" . (null !== \State::get('x.block') ? 'block' : 'content'), $content, 1, $state);
    }
    \Hook::set('page.content', __NAMESPACE__ . "\\t_o_c", 10);
}
