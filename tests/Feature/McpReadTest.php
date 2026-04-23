<?php

namespace Tests\Feature;

use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class McpReadTest extends TestCase
{
    use RefreshDatabase;

    public function test_mcp_films_rejects_without_token(): void
    {
        $this->getJson('/api/mcp/films', ['HTTP_Accept' => 'application/json'])->assertStatus(401);
    }

    public function test_mcp_films_returns_list_with_bearer(): void
    {
        config(['mcp.read_token' => 'mcp-test-token-for-phpunit']);

        Film::factory()->create([
            'title' => 'Film MCP',
            'release_year' => 2020,
        ]);

        $r = $this->withHeader('Authorization', 'Bearer mcp-test-token-for-phpunit')
            ->getJson('/api/mcp/films');

        $r->assertOk();
        $r->assertJsonPath('films.0.title', 'Film MCP');
    }
}
