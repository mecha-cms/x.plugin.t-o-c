<?php

if ('POST' === $_SERVER['REQUEST_METHOD']) {
    if (0 === strpos(($_POST['type'] ?? P) . '/', 'page/page/') && !isset($_POST['page']['state']['x']['t-o-c'])) {
        // Set default value to `false` if user does not check the toggle
        $_POST['page']['state']['x']['t-o-c'] = false;
    }
}

if ('GET' === $_SERVER['REQUEST_METHOD']) {
    Hook::set('_', function ($_) {
        if (0 === strpos($_['type'] . '/', 'page/page/') && !empty($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['state']['lot']['fields'])) {
            $page = new Page($_['file'] ?: null);
            $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['state']['lot']['fields']['lot']['t-o-c'] = [
                'hint' => 'Show',
                'name' => 'page[state][x][t-o-c]',
                'stack' => 40,
                'title' => '<abbr title="' . i('Table of Contents') . '">' . i('TOC') . '</abbr>',
                'type' => 'toggle',
                'value' => (int) ($page->state['x']['t-o-c'] ?? $state->x->{'t-o-c'}->status ?? 1)
            ];
        }
        return $_;
    }, 20);
}