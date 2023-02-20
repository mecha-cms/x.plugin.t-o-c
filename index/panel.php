<?php

if ('POST' === $_SERVER['REQUEST_METHOD']) {
    if (0 === strpos(($_POST['type'] ?? P) . '/', 'page/page/')) {
        // The default value of `page[state][t-o-c]` property is `true`, so if there is `page[state][t-o-c]` data with a
        // truthy value being sent, then we can delete it to optimize the page file size. Conversely, if there is no
        // data `page[state][t-o-c]` being sent, then we can set a falsy value to it to be stored in the page file.
        if (!empty($_POST['page']['state']['t-o-c'])) {
            unset($_POST['page']['state']['t-o-c']);
        } else {
            $_POST['page']['state']['t-o-c'] = 0; // Defaults to `0`
        }
    }
}

if ('GET' === $_SERVER['REQUEST_METHOD']) {
    Hook::set('_', function ($_) {
        if (0 === strpos($_['type'] . '/', 'page/page/') && !empty($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['state']['lot']['fields'])) {
            $page = new Page($_['file'] ?: null);
            $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['state']['lot']['fields']['lot']['t-o-c'] = array_replace_recursive([
                'hint' => 'Show',
                'name' => 'page[state][t-o-c]',
                'stack' => 40,
                'title' => '<abbr title="' . i('Table of Contents') . '">' . i('TOC') . '</abbr>',
                'type' => 'toggle',
                'value' => $page->state['t-o-c'] ?? true
            ], $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['state']['lot']['fields']['lot']['t-o-c'] ?? []);
        }
        return $_;
    }, 20);
}