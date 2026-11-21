<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    // Sem Referer de domínio stateful, o Sanctum não liga a sessão
    // e o session()->regenerate() do controller estoura.
    $this->withHeader('Referer', 'http://localhost');

    $this->payload = [
        'name' => 'Ana Silva',
        'email' => 'ana@example.com',
        'password' => 'senha-segura',
        'password_confirmation' => 'senha-segura',
    ];
});

test('registra o usuário, autentica e devolve 201 com os dados públicos', function () {
    $response = $this->postJson('/api/register', $this->payload);

    // Busca exatamente UM registro
    $user = User::where('email', 'ana@example.com')->sole();

    // Status 201
    $response->assertCreated()
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.name', 'Ana Silva')
        ->assertJsonPath('data.email', 'ana@example.com');

    // Garante a allowlist
    expect(array_keys($response->json('data')))->toBe(['id', 'name', 'email']);

    // A senha não está em texto puro, e não houve hash duplo.
    expect(Hash::check('senha-segura', $user->password))->toBeTrue();

    // Login automático
    $this->assertAuthenticatedAs($user);
});

test('rejeita e-mail já cadastrado com 422, sem criar nem autenticar', function () {
    User::factory()->create(['email' => 'ana@example.com']);

    $response = $this->postJson('/api/register', $this->payload);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('email')
        ->assertJsonMissingValidationErrors(['name', 'password']);

    $this->assertDatabaseCount('users', 1);
    $this->assertGuest();
});

test('rejeita confirmação de senha divergente com 422 no campo password', function () {
    $response = $this->postJson('/api/register', [
        ...$this->payload,
        'password_confirmation' => 'outra-senha',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('password')
        ->assertJsonMissingValidationErrors(['name', 'email', 'password_confirmation']);

    $this->assertDatabaseCount('users', 0);
    $this->assertGuest();
});
