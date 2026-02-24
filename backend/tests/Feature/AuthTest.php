<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['token', 'token_type', 'user']);
                 
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token', 'token_type', 'user']);
    }
    
    public function test_user_can_upload_document()
    {
        Storage::fake('private');
        
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $file = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/upload', [
                             'file' => $file,
                         ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'document']);
                 
        $this->assertDatabaseHas('document_metadata', [
            'user_id' => $user->id,
            'filename' => 'document.pdf',
            'status' => 'pending',
        ]);
    }
}
