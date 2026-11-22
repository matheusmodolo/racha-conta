<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    // Sem Referer de domínio stateful, o Sanctum não liga a sessão
    // e o session()->regenerate() do controller estoura.
    $this->withHeader('Referer', 'http://localhost');
});

test('autentica com credenciais válidas e devolve 200 com os dados públicos', function () {
    $user = User::factory()->create();

    // Senha padrão na factory: 'password'
    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password'
    ]);

    $response->assertOk()
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.email', $user->email);

    $this->assertAuthenticatedAs($user);
});

test('responde senha errada e e-mail inexistente com o mesmo 422 genérico', function () {
    $user = User::factory()->create();

    $senhaErrada = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'senha-errada'
    ]);

    $emailErrado = $this->postJson('/api/login', [
        'email' => 'errado@email.com',
        'password' => 'password'
    ]);

    $senhaErrada->assertUnprocessable()->assertJsonValidationErrors('email');
    $emailErrado->assertUnprocessable()->assertJsonValidationErrors('email');

    expect($senhaErrada->json('errors'))->toBe($emailErrado->json('errors'));

    $this->assertGuest();
});

test('bloqueia com 429 a sexta tentativa no mesmo minuto', function () {
    $user = User::factory()->create();
    $credenciais = ['email' => $user->email, 'password' => 'senha-errada'];

    foreach (range(1, 5) as $attempt) {
        $this->postJson('/api/login', $credenciais)->assertUnprocessable();
    }

    $this->postJson('/api/login', $credenciais)->assertTooManyRequests();
});
