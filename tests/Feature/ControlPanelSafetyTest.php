<?php

namespace Tests\Feature;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Statamic\Exceptions\StatamicProRequiredException;
use Statamic\Facades\Stache;
use Tests\TestCase;

class ControlPanelSafetyTest extends TestCase
{
    private const EXCEPTION_ROUTE = '/cp-exception-test';

    private string $temporaryUserPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->temporaryUserPath = base_path('users/testing-temporary@example.com.yaml');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->temporaryUserPath)) {
            unlink($this->temporaryUserPath);
        }

        Stache::clear();
        Stache::warm();

        parent::tearDown();
    }

    public function test_cp_exception_redirects_after_recovering_stale_user_cache(): void
    {
        $this->createTemporaryUser();
        Stache::clear();
        Stache::warm();

        unlink($this->temporaryUserPath);

        $response = $this->renderException();

        $this->assertSame(302, $response->getStatusCode());
        $this->assertStringEndsWith(self::EXCEPTION_ROUTE, $response->headers->get('Location'));
    }

    public function test_cp_exception_shows_branded_unavailable_page_for_real_multi_user_state(): void
    {
        $this->createTemporaryUser();
        Stache::clear();
        Stache::warm();

        config([
            'app.name' => 'Dric Interior',
            'statamic.cp.custom_cms_name' => 'Dric Interior',
        ]);

        $response = $this->renderException();

        $this->assertSame(503, $response->getStatusCode());
        $this->assertStringContainsString('Dric Interior', $response->getContent());
        $this->assertStringContainsString('Admin workspace temporarily unavailable.', $response->getContent());
        $this->assertStringNotContainsString('Statamic Pro is required for multiple users.', $response->getContent());
        $this->assertStringNotContainsString('Statamic', $response->getContent());
    }

    private function createTemporaryUser(): void
    {
        file_put_contents($this->temporaryUserPath, implode("\n", [
            'super: false',
            'id: 11111111-2222-3333-4444-555555555555',
            'password_hash: $2y$10$abcdefghijklmnopqrstuvABCDEFGHIJKLMNOpqrstuvwxyz12',
            'email: testing-temporary@example.com',
            '',
        ]));
    }

    private function renderException()
    {
        return $this->app->make(ExceptionHandler::class)->render(
            Request::create(self::EXCEPTION_ROUTE, 'GET'),
            new StatamicProRequiredException('Statamic Pro is required for multiple users.')
        );
    }
}
