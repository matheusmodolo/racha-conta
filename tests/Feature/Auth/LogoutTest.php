<?php

use App\Models\User;

beforeEach(function () {
    // Sem Referer de domínio stateful, o Sanctum não liga a sessão
    // e o session()->regenerate() do controller estoura.
    $this->withHeader('Referer', 'http://localhost');
});

test('encerra a sessão com 204 e a requisição seguinte recebe 401', function () {
    $user = User::factory()->create();

    // Senha padrão na factory: 'password'
    $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertOk();

    $this->assertAuthenticatedAs($user);

    $this->postJson('/api/logout')->assertNoContent();

    // Nos testes, a app é a mesma entre requisições e o guard sanctum
    // guarda o usuário em memória. Sem isso, o GET abaixo daria 200.
    $this->app['auth']->forgetGuards();

    $this->getJson('/api/user')->assertUnauthorized();
});

test('rejeita logout sem sessão com 401', function () {
    $this->postJson('/api/logout')->assertUnauthorized();
});
