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

        $form = $crawler->selectButton('submit')->form();
        $form['email'] = 'user@user.com';
        $form['password'] = 'pass';
        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects('/');
    }

    public function testLoginFailue(): void
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('submit')->form();
        $form['email'] = 'incorrect@user.com';
        $form['password'] = 'wrong';
        $crawler = $this->client->submit($form);

        $this->assertRouteSame('login');
    }

    public function testRegisterSuccess(): void
    {
        $crawler = $this->client->request('GET', '/register');

        $form = $crawler->selectButton('register[submit]')->form();
        $form['register[email]'] = 'user@gmail.com';
        $form['register[password]'] = 'password';
        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects('/');
    }

    public function assertionProvider(): array
    {
        return [
            [
                '',
                '',
                'This value should not be blank.',
                2
            ],
            [
                'user.gmail.com',
                'password',
                'This value is not a valid email address.',
                 1
            ],
            [
                str_repeat("a", 171) . '@gmail.com',
                'password',
                'This value is too long. It should have 180 characters or less.',
                 1
            ],
            [
                'user@gmail.com',
                str_repeat("a", 51),
                'This value is too long. It should have 50 characters or less.',
                 1
            ],
            [
                'user@gmail.com',
                'passwor',
                'This value is too short. It should have 8 characters or more.',
                 1
            ],
        ];
    }

   /**
     * @dataProvider assertionProvider
     */
    public function testRegisterAssertions(
        string $email,
        string $pass,
        string $error,
        int    $count
    ): void {
        $crawler = $this->client->request('GET', '/register');

        $form = $crawler->selectButton('register[submit]')->form();
        $form['register[email]'] = $email;
        $form['register[password]'] = $pass;
        $crawler = $this->client->submit($form);

        $this->assertSelectorTextSame('#register li', $error);
        $this->assertEquals($count, $crawler->filter('#register li')->count());
    }
}
