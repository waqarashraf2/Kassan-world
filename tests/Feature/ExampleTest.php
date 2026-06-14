<?php

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response
        ->assertStatus(200)
        ->assertSee('/build/assets/app-', false)
        ->assertDontSee('Unable to locate file in Vite manifest');
});
