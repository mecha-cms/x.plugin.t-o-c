<?php

namespace x\t_o_c {
    function content($content) {
        \extract($GLOBALS, \EXTR_SKIP);
        return $state->is('page') ? \x\t_o_c\to\content($content, $state->x->{'t-o-c'}->min ?? 2) : $content;
    }
    function tree($content) {
        \extract($GLOBALS, \EXTR_SKIP);
        $c = $state->x->{'t-o-c'};
        if (!$state->is('page') || (isset($page->state['t-o-c']) && !$page->state['t-o-c']) || (!$tree = \x\t_o_c\to\tree($content, $c->min ?? 2))) {
            return $content;
        }
        $id = 't-o-c:' . \substr(\uniqid(), 6);
        return (new \HTML(\Hook::fire('y.t-o-c', [['details', [
            'title' => ['summary', \i('Table of Contents'), [
                'id' => $id,
                'role' => 'heading'
            ]],
            'content' => $tree
        ], [
            'aria-labelledby' => $id,
            'open' => !isset($c->open) || !empty($c->open),
            'role' => 'doc-toc'
        ]]]), true)) . $content;
    }
    \Hook::set('page.content', __NAMESPACE__ . "\\content", 2.2);
    \Hook::set('page.content', __NAMESPACE__ . "\\tree", 2.1);
    \class_exists("\\Asset") && \State::is('page') && \Asset::set(__DIR__ . \D . 'index' . (\defined("\\TEST") && \TEST ? '.' : '.min.') . 'css', 10);
}

namespace x\t_o_c\to {
    function content(?string $content, int $min = 2): ?string {
        if (!$content || (false === \stripos($content, '</h') && false === \stripos(\strtr($content, [
            "'" => "",
            '"' => ""
        ]), 'role=heading'))) {
            return $content;
        }
        $count = [];
        $out = \preg_replace_callback('/<(caption|div|figcaption|h[1-6]|p|summary)(\s(?:"[^"]*"|\'[^\']*\'|[^>])*)?>([\s\S]*?)<\/\1>/i', static function ($m) use (&$count) {
            if ('h' === \strtolower($m[1][0]) && \is_numeric(\substr($m[1], 1))) {
                if (false !== \stripos($m[2], 'role=') && !\preg_match('/\brole=([\'"]?)heading\1/i', $m[2])) {
                    return $m[0]; // Skip!
                }
                if (false !== \stripos($m[2], 'id=') && \preg_match('/\bid=("[^"]+"|\'[^\']+\'|[^>\s]+)/i', $m[2], $mm)) {
                    $id = $mm[1];
                    if (('"' === $id[0] && '"' === \substr($id[0], -1)) || ("'" === $id[0] && "'" === \substr($id[0], -1))) {
                        $id = \htmlspecialchars_decode(\substr($id, 1, -1));
                    } else {
                        $id = \htmlspecialchars_decode($id);
                    }
                } else {
                    $id = 'to:' . \To::kebab(\strip_tags($m[3] ?: \substr(\uniqid(), 6)));
                }
                $count[$id] = ($count[$id] ?? -1) + 1;
                $out = new \HTML($m[0]);
                $out['id'] = $id . ($count[$id] > 0 ? '.' . $count[$id] : "");
                return (string) $out;
            }
            if (false !== \stripos($m[2], 'role=') && \preg_match('/\brole=([\'"]?)heading\1/i', $m[2]) && \preg_match('/\baria-level=("\d+"|\'\d+\'|\d+)/i', $m[2], $mm)) {
                $level = $mm[1];
                if (('"' === $level[0] && '"' === \substr($level, -1)) || ("'" === $level[0] && "'" === \substr($level, -1))) {
                    $level = \substr($level, 1, -1);
                }
                if (false !== \stripos($m[2], 'id=') && \preg_match('/\bid=("[^"]+"|\'[^\']+\'|[^>\s]+)/i', $m[2], $mm)) {
                    $id = $mm[1];
                    if (('"' === $id[0] && '"' === \substr($id[0], -1)) || ("'" === $id[0] && "'" === \substr($id[0], -1))) {
                        $id = \htmlspecialchars_decode(\substr($id, 1, -1));
                    } else {
                        $id = \htmlspecialchars_decode($id);
                    }
                } else {
                    $id = 'to:' . \To::kebab(\strip_tags($m[3] ?: \substr(\uniqid(), 6)));
                }
                $count[$id] = ($count[$id] ?? -1) + 1;
                $out = new \HTML($m[0]);
                $out['id'] = $id . ($count[$id] > 0 ? '.' . $count[$id] : "");
                return (string) $out;
            }
            return $m[0];
        }, $content);
        return \count($count) >= $min ? $out : $content;
    }
    function tree(?string $content, int $min = 2): ?string {
        if (!$content || (false === \stripos($content, '</h') && false === \stripos(\strtr($content, [
            "'" => "",
            '"' => ""
        ]), 'role=heading'))) {
            return null;
        }
        $count = [];
        $current = 0;
        $out = "";
        if (\preg_match_all('/<(caption|div|figcaption|h[1-6]|p|summary)(\s(?:"[^"]*"|\'[^\']*\'|[^>])*)?>([\s\S]*?)<\/\1>/i', $content, $m)) {
            foreach ($m[0] as $k => $v) {
                $level = 0;
                if ('h' === \strtolower($m[1][$k][0]) && \is_numeric(\substr($m[1][$k], 1))) {
                    if (false !== \stripos($m[2][$k], 'role=') && !\preg_match('/\brole=([\'"]?)heading\1/i', $m[2][$k])) {
                        continue; // Skip!
                    }
                    $level = \substr($m[1][$k], 1);
                } else if (false !== \stripos($m[2][$k], 'role=') && \preg_match('/\brole=([\'"]?)heading\1/i', $m[2][$k]) && \preg_match('/\baria-level=("\d+"|\'\d+\'|\d+)/i', $m[2][$k], $mm)) {
                    $level = $mm[1];
                    if (('"' === $level[0] && '"' === \substr($level, -1)) || ("'" === $level[0] && "'" === \substr($level, -1))) {
                        $level = \substr($level, 1, -1);
                    }
                } else {
                    continue;
                }
                if ($m[2][$k] && \preg_match('/\bid=("[^"]+"|\'[^\']+\'|[^>\s]+)/i', $m[2][$k], $mm)) {
                    $id = $mm[1];
                    if (('"' === $id[0] && '"' === \substr($id[0], -1)) || ("'" === $id[0] && "'" === \substr($id[0], -1))) {
                        $id = \htmlspecialchars_decode(\substr($id, 1, -1));
                    } else {
                        $id = \htmlspecialchars_decode($id);
                    }
                } else {
                    $id = 'to:' . \To::kebab(\strip_tags($m[3][$k] ?: \substr(\uniqid(), 6)));
                }
                $count[$id] = ($count[$id] ?? -1) + 1;
                if ($level > $current) {
                    $out .= '<ol aria-level="' . $level . '" role="doc-pagelist"><li>';
                } else if ($level < $current) {
                    for ($i = $level; $i < $current; ++$i) {
                        $out .= '</li></ol>';
                    }
                    $out .= '</li><li>';
                } else if ("" === $out) {
                    $out .= '<ol aria-level="' . $level . '" role="doc-pagelist"><li>';
                } else {
                    $out .= '</li><li>';
                }
                $out .= '<a href="' . \To::query($_GET) . '#' . $id . ($count[$id] > 0 ? '.' . $count[$id] : "") . '">';
                $out .= \trim(\strip_tags($m[3][$k], ['abbr', 'b', 'br', 'cite', 'code', 'del', 'dfn', 'em', 'i', 'ins', 'kbd', 'mark', 'q', 'span', 'strong', 'sub', 'sup', 'svg', 'time', 'u', 'var']));
                $out .= '</a>';
                $current = $level;
            }
            while ($current > 0) {
                $out .= '</li></ol>';
                $current -= 1;
            }
            return \count($count) >= $min && "" !== $out ? $out : null;
        }
        return null;
    }
    if (\defined("\\TEST") && 'x.t-o-c' === \TEST && \is_file($test = __DIR__ . \D . 'test.php')) {
        require $test;
    }
}