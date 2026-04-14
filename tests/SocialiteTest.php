<?php

namespace Tests;

use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User;
use Revolution\Socialite\Discord\DiscordProvider;

class SocialiteTest extends TestCase
{
    public function test_instance()
    {
        $this->assertInstanceOf(DiscordProvider::class, Socialite::driver('discord'));
    }

    public function test_redirect()
    {
        Socialite::fake('discord');

        $response = Socialite::driver('discord')->redirect();

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    public function test_user()
    {
        Socialite::fake('discord', (new User)->map([
            'id' => '123456789012345678',
            'nickname' => 'testuser#1234',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'avatar' => 'https://cdn.discordapp.com/avatars/123456789012345678/a1b2c3d4e5f6g7h8i9j0.jpg',
        ]));

        $user = Socialite::driver('discord')->user();

        $this->assertEquals('123456789012345678', $user->getId());
        $this->assertEquals('Test User', $user->getName());
        $this->assertEquals('testuser#1234', $user->getNickname());
        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertEquals('https://cdn.discordapp.com/avatars/123456789012345678/a1b2c3d4e5f6g7h8i9j0.jpg', $user->getAvatar());
    }
}
