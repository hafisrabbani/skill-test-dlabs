<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test store method - Create a new post.
     *
     * @return void
     */
    public function test_store_post()
    {
        // Arrange: Create a user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
            'member_code' => 'AIS123',
        ]);

        // Generate JWT token using JWTAuth::attempt() with email and password
        $token = JWTAuth::attempt(['email' => 'user@example.com', 'password' => 'password123']);

        // Arrange: Define post data
        $postData = [
            'title' => 'Sample Post',
            'content' => 'This is a sample post content.',
        ];

        // Act: Send a POST request with the Bearer token in the header
        $response = $this->postJson('/api/posts', $postData, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        // Assert: Check if the post was created successfully
        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Post created successfully',
                'data' => [
                    'title' => 'Sample Post',
                    'content' => 'This is a sample post content.',
                    'user_id' => $user->id,
                ]
            ]);
    }

    /**
     * Test update method - Update an existing post.
     *
     * @return void
     */
    public function test_update_post()
    {
        // Arrange: Create a user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
            'member_code' => 'AIS123',
        ]);

        // Generate JWT token using JWTAuth::attempt() with email and password
        $token = JWTAuth::attempt(['email' => 'user@example.com', 'password' => 'password123']);

        // Arrange: Manually create a post for this user
        $post = Post::create([
            'title' => 'Original Title',
            'content' => 'Original Content',
            'user_id' => $user->id,
        ]);

        // Arrange: Define updated data
        $updatedData = [
            'title' => 'Updated Post Title',
            'content' => 'Updated content.',
        ];

        // Act: Send a PUT request with the Bearer token in the header to update the post
        $response = $this->putJson("/api/posts/{$post->id}", $updatedData, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        // Assert: Check if the post was updated successfully
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Post updated successfully',
                'data' => [
                    'title' => 'Updated Post Title',
                    'content' => 'Updated content.',
                ]
            ]);
    }

    /**
     * Test destroy method - Delete a post.
     *
     * @return void
     */
    public function test_destroy_post()
    {
        // Arrange: Create a user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
            'member_code' => 'AIS123',
        ]);

        // Generate JWT token using JWTAuth::attempt() with email and password
        $token = JWTAuth::attempt(['email' => 'user@example.com', 'password' => 'password123']);

        // Arrange: Manually create a post for this user
        $post = Post::create([
            'title' => 'Post to be deleted',
            'content' => 'This post will be deleted.',
            'user_id' => $user->id,
        ]);

        // Act: Send a DELETE request with the Bearer token in the header
        $response = $this->deleteJson("/api/posts/{$post->id}", [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        // Assert: Check if the post was deleted
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Post deleted successfully',
            ]);
    }

    /**
     * Test index method - Retrieve posts with pagination and search.
     *
     * @return void
     */
    public function test_index_posts()
    {
        // Arrange: Create a user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
            'member_code' => 'AIS123',
        ]);

        // Generate JWT token using JWTAuth::attempt() with email and password
        $token = JWTAuth::attempt(['email' => 'user@example.com', 'password' => 'password123']);

        // Arrange: Manually create posts for this user
        Post::create([
            'title' => 'First Post',
            'content' => 'Content of first post',
            'user_id' => $user->id,
        ]);
        Post::create([
            'title' => 'Second Post',
            'content' => 'Content of second post',
            'user_id' => $user->id,
        ]);

        // Act: Send a GET request with the Bearer token in the header
        $response = $this->getJson('/api/posts?page=1&limit=2', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        // Assert: Check if posts are retrieved with pagination
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Posts retrieved successfully',
                'pagination' => [
                    'current_page' => 1,
                    'total_page' => 1,
                    'per_page' => 2,
                    'total_data' => 2,
                ],
            ]);
    }
}
