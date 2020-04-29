<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testLoginSuccess(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $crawler = $this->client->submitForm('submit', [
            'email'    => 'user@user.com',
            'password' => 'pass'
        ]);

        $this->assertResponseRedirects('/');
    }

    public function testLoginFailue(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $crawler = $this->client->submitForm('submit', [
            'email'    => 'incorrect@user.com',
            'password' => 'wrong'
        ]);

        $this->assertRouteSame('login');
    }

    public function testRegisterSuccess(): void
    {
        $crawler = $this->client->request('GET', '/register');
        $crawler = $this->client->submitForm('register[submit]', [
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
        $crawler = $this->client->request('GET', '/register');
        $crawler = $this->client->submitForm('register[submit]', [
            'register[email]'    => $email,
            'register[password]' => $pass
        ]);

        $this->assertSelectorTextSame('#register li', $error);
    }

    public function validationProvider(): array
    {
        $max['email'] = 180;
        $max['pass']  = 50;
        $domain = '@gmail.com';

        return [
            [
                '',
                '',
                'This value should not be blank.'
            ],
            [
                'user.gmail.com',
                'password',
                'This value is not a valid email address.'
            ],
            [
                str_repeat('a', $max['email'] + 1 - strlen($domain)) . $domain,
                'password',
                'This value is too long. It should have 180 characters or less.'
            ],
            [
                'user@gmail.com',
                str_repeat('a', $max['pass'] + 1),
                'This value is too long. It should have 50 characters or less.'
            ],
            [
                'user@gmail.com',
                'passwor',
                'This value is too short. It should have 8 characters or more.'
            ],
        ];
    }
}
