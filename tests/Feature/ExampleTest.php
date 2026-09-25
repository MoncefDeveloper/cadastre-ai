<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test that visiting the root URL redirects to the Filament admin login.
     */
    public function test_root_url_redirects_to_admin_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/admin/login');
    }
}
