<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginSuccess(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $client->submitForm('submit', [
            'email'    => 'user@user.com',
            'password' => 'pass'
        ]);

        $this->assertResponseRedirects('/');
    }

    public function testLoginFailure(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $client->submitForm('submit', [
            'email'    => 'incorrect@user.com',
            'password' => 'wrong'
        ]);

        $this->assertRouteSame('login');
    }

    public function testRegisterSuccess(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');
        $client->submitForm('register[submit]', [
            'register[email]'    => 'user@gmail.com',
            'register[password]' => 'password'
        ]);

        $this->assertResponseRedirects('/');
    }

   /**
     * @dataProvider validationProvider
     */
    public function testRegisterValidation(string $email, string $pass, string $error): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');
        $client->submitForm('register[submit]', [
            'register[email]'    => $email,
            'register[password]' => $pass
        ]);

        $this->assertSelectorTextSame('#register li', $error);
    }

    public function validationProvider(): array
    {
        $validEmail = 'user@gmail.com';
        $validPass  = 'password';
        // max email length is 180, 171 + 10 domain chars is 181
        $longEmail = str_repeat('a', 171) . '@gmail.com';
        $longPass  = str_repeat('a', 51);

        return [
            'Blank form' => [
                '',
                '',
                'This value should not be blank.'
            ],
            'Email invalid' => [
                'user.gmail.com',
                $validPass,
                'This value is not a valid email address.'
            ],
            'Email too long' => [
                $longEmail,
                $validPass,
                'This value is too long. It should have 180 characters or less.'
            ],
            'Password too short' => [
                $validEmail,
                'passwor',
                'This value is too short. It should have 8 characters or more.'
            ],
            'Password too long' => [
                $validEmail,
                $longPass,
                'This value is too long. It should have 50 characters or less.'
            ]
        ];
    }

   /**
     * @dataProvider redirectProvider
     */
    public function testRedirect(string $page): void
    {
        $client = static::createClient();
        $repo = static::$container->get(UserRepository::class);
        $user = $repo->findOneByEmail('user@user.com');
        $client->loginUser($user);

        // test redirect when user is already logged in
        $client->request('GET', '/' . $page);

        $this->assertResponseRedirects('/');
    }

    public function redirectProvider(): array
    {
        return [['login'], ['register']];
    }
}
