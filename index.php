<?php

namespace x {
    function t_o_c($content) {
        if (!$tree = \x\t_o_c\tree($content)) {
            return $content;
        }
        \extract($GLOBALS, \EXTR_SKIP);
        $c = $state->x->{'t-o-c'};
        $id = 't-o-c:' . \substr(\uniqid(), 6);
        return (new \HTML(\Hook::fire('y.t-o-c', [['details', [
            'title' => ['summary', \i('Table of Contents'), ['id' => $id]],
            'content' => $tree
        ], [
            'aria-labelledby' => $id,
            'open' => !isset($c->open) || $c->open,
            'role' => 'doc-toc'
        ]]]), true)) . \x\t_o_c\content($content);
    }
    \Hook::set('page.content', __NAMESPACE__ . "\\t_o_c", 2.1);
}

namespace x\t_o_c {
    function content(?string $content): ?string {
        if (!$content || (false === \stripos($content, '</h') && false === \stripos(\strtr($content, [
            "'" => "",
            '"' => ""
        ]), 'role=heading'))) {
            return $content;
        }
        $count = [];
        $content = \preg_replace_callback('/<(caption|div|figcaption|h[1-6]|p|summary)((?:"[^"]*"|\'[^\']*\'|[^>])*)>([\s\S]*?)<\/\1>/i', static function ($m) use (&$count) {
            if ('h' === \strtolower($m[1][0]) && \is_numeric(\substr($m[1], 1))) {
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
            if (\preg_match('/\baria-level=("\d+"|\'\d+\'|\d+)/i', $m[2], $mm)) {
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
        return $content;
    }
    function tree(?string $content): ?string {
        if (!$content || (false === \stripos($content, '</h') && false === \stripos(\strtr($content, [
            "'" => "",
            '"' => ""
        ]), 'role=heading'))) {
            return null;
        }
        $count = [];
        $current = 0;
        $out = "";
        if (\preg_match_all('/<(caption|div|figcaption|h[1-6]|p|summary)((?:"[^"]*"|\'[^\']*\'|[^>])*)>([\s\S]*?)<\/\1>/i', $content, $m)) {
            foreach ($m[0] as $k => $v) {
                $level = 0;
                if ('h' === \strtolower($m[1][$k][0]) && \is_numeric(\substr($m[1][$k], 1))) {
                    $level = \substr($m[1][$k], 1);
                } else if (false === \stripos(\strtr($m[2][$k], [
                    "'" => "",
                    '"' => ""
                ]), 'role=heading')) {
                    continue;
                }
                if (\preg_match('/\baria-level=("\d+"|\'\d+\'|\d+)/i', $m[2][$k], $mm)) {
                    $level = $mm[1];
                    if (('"' === $level[0] && '"' === \substr($level, -1)) || ("'" === $level[0] && "'" === \substr($level, -1))) {
                        $level = \substr($level, 1, -1);
                    }
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
                $out .= '<a href="#' . $id . ($count[$id] > 0 ? '.' . $count[$id] : "") . '">';
                $out .= \w($m[3][$k], ['abbr', 'b', 'br', 'cite', 'code', 'del', 'dfn', 'em', 'i', 'ins', 'kbd', 'mark', 'q', 'span', 'strong', 'sub', 'sup', 'svg', 'time', 'u', 'var']);
                $out .= '</a>';
                $current = $level;
            }
            while ($current > 0) {
                $out .= '</li></ol>';
                $current -= 1;
            }
            return $out;
        }
        return null;
    }
    if (\defined("\\TEST") && 'x.t-o-c' === \TEST && \is_file($test = __DIR__ . \D . 'test.php')) {
        require $test;
    }
}