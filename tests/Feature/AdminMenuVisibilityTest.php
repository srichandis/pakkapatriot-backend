<?php

uses(Webkul\Admin\Tests\AdminTestCase::class);

test('admin sidebar shows the new collection menus', function () {
    $this->loginAsAdmin();

    $response = $this->get(route('admin.dashboard.index'));
    $response->assertOk();

    $html = $response->getContent();

    foreach (['/admin/ideas', '/admin/places', '/admin/culture', '/admin/create', '/admin/games'] as $href) {
        expect($html)->toContain($href);
    }
});
