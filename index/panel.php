<?php

if ('POST' === $_SERVER['REQUEST_METHOD']) {
    if (0 === strpos(($_POST['type'] ?? P) . '/', 'page/page/') && !isset($_POST['page']['state']['x']['t-o-c'])) {
        // Set value to default if user does not check the toggle
        $_POST['page']['state']['x']['t-o-c'] = (int) ($state->x->{'t-o-c'}->status ?? 1);
    }
}

if ('GET' === $_SERVER['REQUEST_METHOD']) {
    Hook::set('_', function ($_) {
        extract($GLOBALS, EXTR_SKIP);
        if (0 === strpos($_['type'] . '/', 'page/page/') && !empty($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['state']['lot']['fields']['lot']['state.x'])) {
            $page = new Page($_['file'] ?: null);
            $status = (int) ($state->x->{'t-o-c'}->status ?? 1); // The default visibility status
            if (!isset($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['state']['lot']['fields']['lot']['state.x']['values']['t-o-c'])) {
                // Set default value
                $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['state']['lot']['fields']['lot']['state.x']['values']['t-o-c'] = (int) ($page->state['x']['t-o-c'] ?? $status);
            }
            // Set option to hide if it is shown by default or show if it is hidden by default
            $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['state']['lot']['fields']['lot']['state.x']['lot']['t-o-c'] = [
                'title' => [(1 === $status ? 'Hide' : 'Show') . ' %s', ['table of contents']],
                'value' => 1 === $status ? 0 : 1
            ];
        }
        return $_;
    }, 0);
}