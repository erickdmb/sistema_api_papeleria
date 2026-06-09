<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Usuario Test',
            'email' => 'test@papeleria.com',
            'password' => bcrypt('password123'),
        ]);
    }

    public function test_debe_iniciar_sesion_con_credenciales_correctas()
    {
        $payload = [
            'email' => 'test@papeleria.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'access_token',
            'token_type',
            'user' => ['id', 'name', 'email']
        ]);
        $response->assertJsonPath('user.email', 'test@papeleria.com');
    }

    public function test_no_debe_iniciar_sesion_con_credenciales_incorrectas()
    {
        $payload = [
            'email' => 'test@papeleria.com',
            'password' => 'password_incorrecta',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(401);
        $response->assertJsonPath('message', 'Las credenciales proporcionadas son incorrectas.');
    }

    public function test_debe_obtener_usuario_autenticado()
    {
        $token = $this->user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/user');

        $response->assertStatus(200);
        $response->assertJsonPath('email', 'test@papeleria.com');
    }

    public function test_debe_cerrar_sesion_exitosamente()
    {
        $token = $this->user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Sesión cerrada correctamente.');

        // Verificar que el token fue eliminado
        $this->assertEquals(0, $this->user->tokens()->count());
    }
}
