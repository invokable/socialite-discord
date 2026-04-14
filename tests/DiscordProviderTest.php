<?php

namespace Tests;

use Illuminate\Http\Request;
use Laravel\Socialite\Two\User;
use Revolution\Socialite\Discord\DiscordProvider;

class DiscordProviderTest extends TestCase
{
    protected function provider(): DiscordProvider
    {
        return new DiscordProvider(Request::create('foo'), 'client_id', 'client_secret', 'http://localhost/callback');
    }

    protected function mapUser(array $data): User
    {
        $method = (new \ReflectionClass(DiscordProvider::class))->getMethod('mapUserToObject');
        $method->setAccessible(true);

        return $method->invoke($this->provider(), $data);
    }

    public function test_default_scopes()
    {
        $this->assertEquals(['identify', 'email'], $this->provider()->getScopes());
    }

    public function test_map_user_with_all_fields()
    {
        $user = $this->mapUser([
            'id' => '123456789012345678',
            'username' => 'testuser',
            'discriminator' => '1234',
            'global_name' => 'Test User',
            'email' => 'test@example.com',
            'avatar' => 'a1b2c3d4e5f6g7h8i9j0',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('123456789012345678', $user->getId());
        $this->assertEquals('Test User', $user->getName());
        $this->assertEquals('testuser#1234', $user->getNickname());
        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertEquals('https://cdn.discordapp.com/avatars/123456789012345678/a1b2c3d4e5f6g7h8i9j0.jpg', $user->getAvatar());
    }

    public function test_map_user_without_global_name()
    {
        $user = $this->mapUser([
            'id' => '987654321098765432',
            'username' => 'testuser2',
            'discriminator' => '5678',
            'avatar' => null,
        ]);

        $this->assertEquals('testuser2', $user->getName());
        $this->assertEquals('testuser2#5678', $user->getNickname());
        $this->assertNull($user->getEmail());
        $this->assertNull($user->getAvatar());
    }

    public function test_map_user_with_null_avatar()
    {
        $user = $this->mapUser([
            'id' => '111222333444555666',
            'username' => 'testuser3',
            'discriminator' => '9999',
            'global_name' => 'Test User 3',
            'email' => 'test3@example.com',
            'avatar' => null,
        ]);

        $this->assertNull($user->getAvatar());
        $this->assertEquals('Test User 3', $user->getName());
        $this->assertEquals('testuser3#9999', $user->getNickname());
        $this->assertEquals('test3@example.com', $user->getEmail());
    }
}
