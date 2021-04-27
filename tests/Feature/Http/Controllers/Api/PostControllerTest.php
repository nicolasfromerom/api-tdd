<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store()
    {
        //$this->withoutExceptionHandling();
        $response = $this->json('POST','/api/posts',[
            'title'=>'Título del Post de Prueba'
        ]);

        $response->assertJsonStructure(['id','title','created_at','updated_at'])
            ->assertJson(['title'=>'Título del Post de Prueba'])
            ->assertStatus(201);// Se ha creado correctamente

        $this->assertDatabaseHas('posts',['title'=>'Título del Post de Prueba']);
    }

    public function test_validate_title()
    {
        $response = $this->json('POST','/api/posts',[
            'title'=>''
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('title');
    }
}
