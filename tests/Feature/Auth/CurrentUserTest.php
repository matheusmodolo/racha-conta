<?php

use App\Models\User;

test('rejeita acesso sem sessão com 401', function () {
    $this->getJson('/api/user')->assertUnauthorized();
});

test('devolve 200 com apenas os dados públicos do usuário autenticado', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->getJson('/api/user')
        ->assertOk()
        ->assertExactJson([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
});

test('devolve o usuário da sessão, não outro do banco', function () {
    User::factory()->create();
    $logado = User::factory()->create();

    $this->actingAs($logado)
        ->getJson('/api/user')
        ->assertJsonPath('data.id', $logado->id);
});
