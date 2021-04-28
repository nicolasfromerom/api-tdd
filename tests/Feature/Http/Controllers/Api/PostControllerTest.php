<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Post;
use App\User;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store()
    {
        //$this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $response = $this->actingAs($user, 'api')->json('POST','/api/posts',[
            'title'=>'Título del Post de Prueba'
        ]);

        $response->assertJsonStructure(['id','title','created_at','updated_at'])
            ->assertJson(['title'=>'Título del Post de Prueba'])
            ->assertStatus(201);// Se ha creado correctamente

        $this->assertDatabaseHas('posts',['title'=>'Título del Post de Prueba']);
    }

    public function test_validate_title()
    {
        //$this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $response = $this->actingAs($user,'api')->json('POST','/api/posts',[
            'title'=>''
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('title');
    }

    public function test_show()
    {
        $user = factory(User::class)->create();
        $post = factory(Post::class)->create();

        $response = $this->actingAs($user,'api')->json('GET',"/api/posts/$post->id");

        $response->assertJsonStructure(['id','title','created_at','updated_at'])
            ->assertJson(['title'=>$post->title])
            ->assertStatus(200);// Se ha creado correctamente
    }
    
    public function test_404_show()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user,'api')->json('GET','/api/posts/1000');

        $response->assertStatus(404);// No encontrado
    }

    public function test_update()
    {
        $user = factory(User::class)->create();
        //$this->withoutExceptionHandling();

        $post = factory(Post::class)->create();

        $response = $this->actingAs($user,'api')->json('PUT',"/api/posts/$post->id",[
            'title'=>'titulo editado'
        ]);

        $response->assertJsonStructure(['id','title','created_at','updated_at'])
            ->assertJson(['title'=>'titulo editado'])
            ->assertStatus(200);// ok

        $this->assertDatabaseHas('posts',['title'=>'titulo editado']);
    }

    public function test_delete()
    {
        //$this->withoutExceptionHandling();
        $user = factory(User::class)->create();

        $post = factory(Post::class)->create();

        $response = $this->actingAs($user,'api')->json('DELETE',"/api/posts/$post->id");

        $response->assertSee(null)
            ->assertStatus(204);// Sin contenido
        $this->assertDatabaseMissing('posts',['id'=>$post->id]);
    }

    public function test_index()
    {
        $user = factory(User::class)->create();
        factory(Post::class,5)->create();

        $response = $this->actingAs($user,'api')->json('GET','/api/posts');

        $response->assertJsonStructure([
            'data'=>[
                '*'=>['id','title','created_at','updated_at']
            ]
        ])->assertStatus(200);
    }

    public function test_guest()
    {
        $this->json('GET',      '/api/posts')->assertStatus(401);
        $this->json('POST',     '/api/posts')->assertStatus(401);
        $this->json('GET',      '/api/posts/1000')->assertStatus(401);
        $this->json('PUT',      '/api/posts/1000')->assertStatus(401);
        $this->json('DELETE',   '/api/posts/1000')->assertStatus(401);
    }
}
