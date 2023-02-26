<?php

namespace x\t_o_c {
    function asset() {
        \extract($GLOBALS, \EXTR_SKIP);
        \class_exists("\\Asset") && $state->is('page') && \Asset::set(__DIR__ . \D . 'index' . (\defined("\\TEST") && \TEST ? '.' : '.min.') . 'css', 10);
    }
    function content($content) {
        \extract($GLOBALS, \EXTR_SKIP);
        return $state->is('page') ? \x\t_o_c\to\content($content, $state->x->{'t-o-c'}->min ?? 2) : $content;
    }
    function tree($content) {
        \extract($GLOBALS, \EXTR_SKIP);
        $c = $state->x->{'t-o-c'};
        if (
            // Skip if not a page…
            !$state->is('page') ||
            // Skip if disabled by the extension state…
            (isset($c->status) && !$c->status && !isset($this->state['x']['t-o-c'])) ||
            // Skip if disabled by the page state…
            (isset($this->state['x']['t-o-c']) && !$this->state['x']['t-o-c']) ||
            // Skip if table of content(s) tree is empty…
            (!$tree = \x\t_o_c\to\tree($content, $c->min ?? 2))
        ) {
            return $content;
        }
        $id = $this->id;
        $tree = new \HTML($tree, true);
        return (new \HTML(\Hook::fire('y.t-o-c', [['details', [
            'title' => ['summary', \i('Table of Contents'), [
                'id' => 't-o-c:' . $id,
                'role' => 'heading'
            ]],
            'content' => [$tree[0], $tree[1], $tree[2]]
        ], [
            'aria-labelledby' => 't-o-c:' . $id,
            'open' => !isset($c->open) || !empty($c->open),
            'role' => 'doc-toc'
        ]]]), true)) . $content;
    }
    \Hook::set('content', __NAMESPACE__ . "\\asset", -1);
    \Hook::set('page.content', __NAMESPACE__ . "\\content", 2.3);
    \Hook::set('page.content', __NAMESPACE__ . "\\tree", 2.2);
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
        $out = \preg_replace_callback('/<(caption|div|dt|figcaption|h[1-6]|p|summary)(\s(?:"[^"]*"|\'[^\']*\'|[^>])*)?>([\s\S]*?)<\/\1>/i', static function ($m) use (&$count) {
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
                    $id = 'to:' . \To::kebab(\w($m[3] ?: \substr(\uniqid(), 6)));
                }
                $count[$id] = ($count[$id] ?? -1) + 1;
                $out = new \HTML($m[0]);
                $out['id'] = $id . ($count[$id] > 0 ? '.' . $count[$id] : "");
                return (string) $out;
            }
            if (false !== \stripos($m[2], 'role=') && \preg_match('/\brole=([\'"]?)heading\1/i', $m[2]) && \preg_match('/\baria-level=("\d+"|\'\d+\'|\d+)/i', $m[2])) {
                if (false !== \stripos($m[2], 'id=') && \preg_match('/\bid=("[^"]+"|\'[^\']+\'|[^>\s]+)/i', $m[2], $mm)) {
                    $id = $mm[1];
                    if (('"' === $id[0] && '"' === \substr($id[0], -1)) || ("'" === $id[0] && "'" === \substr($id[0], -1))) {
                        $id = \htmlspecialchars_decode(\substr($id, 1, -1));
                    } else {
                        $id = \htmlspecialchars_decode($id);
                    }
                } else {
                    $id = 'to:' . \To::kebab(\w($m[3] ?: \substr(\uniqid(), 6)));
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
        \extract($GLOBALS, \EXTR_SKIP);
        $count = [];
        $current = $deep = $next = 0;
        $out = "";
        $query = $url->query;
        if (\preg_match_all('/<(caption|div|dt|figcaption|h[1-6]|p|summary)(\s(?:"[^"]*"|\'[^\']*\'|[^>])*)?>([\s\S]*?)<\/\1>/i', $content, $m)) {
            foreach ($m[0] as $k => $v) {
                if ('h' === \strtolower($m[1][$k][0]) && \is_numeric(\substr($m[1][$k], 1))) {
                    if (false !== \stripos($m[2][$k], 'role=') && !\preg_match('/\brole=([\'"]?)heading\1/i', $m[2][$k])) {
                        continue; // Skip!
                    }
                    $next = \substr($m[1][$k], 1);
                } else if (false !== \stripos($m[2][$k], 'role=') && \preg_match('/\brole=([\'"]?)heading\1/i', $m[2][$k]) && \preg_match('/\baria-level=("\d+"|\'\d+\'|\d+)/i', $m[2][$k], $mm)) {
                    $next = $mm[1];
                    if (('"' === $next[0] && '"' === \substr($next, -1)) || ("'" === $next[0] && "'" === \substr($next, -1))) {
                        $next = \substr($next, 1, -1);
                    }
                } else {
                    continue;
                }
                $next = (int) $next;
                if ($m[2][$k] && \preg_match('/\bid=("[^"]+"|\'[^\']+\'|[^>\s]+)/i', $m[2][$k], $mm)) {
                    $id = $mm[1];
                    if (('"' === $id[0] && '"' === \substr($id[0], -1)) || ("'" === $id[0] && "'" === \substr($id[0], -1))) {
                        $id = \htmlspecialchars_decode(\substr($id, 1, -1));
                    } else {
                        $id = \htmlspecialchars_decode($id);
                    }
                } else {
                    $id = 'to:' . \To::kebab(\w($m[3][$k] ?: \substr(\uniqid(), 6)));
                }
                $count[$id] = ($count[$id] ?? -1) + 1;
                if ($next > $current) {
                    $out .= '<ol aria-level="' . $next . '" role="doc-pagelist"><li>';
                    ++$deep;
                } else if ($next < $current) {
                    for ($i = $next; $i < $current; ++$i) {
                        $out .= '</li></ol>';
                        --$deep;
                    }
                    $out .= '</li><li>';
                } else {
                    $out .= '</li><li>';
                }
                $out .= '<a href="' . $query . '#' . $id . ($count[$id] > 0 ? '.' . $count[$id] : "") . '">';
                $out .= \trim(\w($m[3][$k], ['abbr', 'b', 'br', 'cite', 'code', 'del', 'dfn', 'em', 'i', 'ins', 'kbd', 'mark', 'q', 'span', 'strong', 'sub', 'sup', 'svg', 'time', 'u', 'var']));
                $out .= '</a>';
                $current = $next;
            }
            while ($deep > 0) {
                $out .= '</li></ol>';
                $deep -= 1;
            }
            return \count($count) >= $min && "" !== $out ? $out : null;
        }
        return null;
    }
    if (\defined("\\TEST") && 'x.t-o-c' === \TEST && \is_file($test = __DIR__ . \D . 'test.php')) {
        require $test;
    }
}