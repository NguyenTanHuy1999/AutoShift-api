<?php

class AuthControllerTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testLogin()
    {
        
        $this->json('POST', 'api/auth/login')
            ->seeJsonStructure([
                'email',
                'password',
            ])
            ->assertResponseStatus(400);

        
        $attemptResult = [];
        $attempValue = false;
        $authMock = Mockery::mock('Illuminate\Auth\AuthManager');
        $authMock->shouldReceive('attempt')->andReturnUsing(function ($credentials) use (&$attemptResult, &$attempValue) {
            $attemptResult = $credentials;

            return $attempValue ? 'login-token' : false;
        });
        $this->app->instance('Illuminate\Auth\AuthManager', $authMock);
        
        $params = [
            'email' => 'foo@bar.com',
            'password' => '123456',
            'foo' => 'bar',
        ];

        $this->json('POST', 'api/auth/login', $params)
            ->seeJsonEquals([
                'status_code' => 403,
                'message' =>  'email or password is incorrect',
            ])
            ->assertResponseStatus(403);
        
        $expected = [
            'email' => 'foo@bar.com',
            'password' => '123456',
        ];
        $this->assertEquals($expected, $attemptResult);

        
        $attempValue = true;
        $this->json('POST', 'api/auth/login', $params)
            ->seeJsonEquals([
                'token' => 'login-token',
            ])
            ->assertResponseStatus(200);
    }

    public function testRefreshToken()
    {
        $authMock = Mockery::mock('Illuminate\Auth\AuthManager');
        $authMock->shouldReceive('refresh')->once()->andReturn('refresh-token');
        $this->app->instance('Illuminate\Auth\AuthManager', $authMock);

        $this->json('POST', 'api/auth/token/refresh')
            ->seeJsonEquals([
                'token' => 'refresh-token',
            ])
            ->assertResponseStatus(200);
    }

    public function testRegister()
    {
        
        $this->json('POST', 'api/auth/register')
            ->seeJsonEquals([
                'email' => ['The Email field is required.'],
                'password'  => ['The password field is required.'],
            ])
            ->assertResponseStatus(400);

        
        $params = ['email' => 'foobar', 'password' => 123456];
        $this->json('POST', 'api/auth/register', $params)
            ->seeJsonEquals([
                'email' => ['The Email must be a valid email address.'],
            ])
            ->assertResponseStatus(400);

        
        $params = ['email' => 'foo@bar.com'];
        $expectedRules = [
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ];
        
        \Validator::shouldReceive('make')
            ->twice()
            ->andReturnSelf()
            ->shouldReceive('fails')
            ->twice()
            ->andReturnValues([true, false])
            ->shouldReceive('messages')
            ->once()
            ->andReturn(['email not pass']);

        $createResult = [];
        //mock user repository
        $userMock = Mockery::mock('ApiMain\Repositories\Contracts\UserRepositoryContract');
        $userMock->shouldReceive('create')->andReturnUsing(function ($attributes) use (&$createResult) {
            return $createResult = $attributes;
        });
        $this->app->instance('ApiMain\Repositories\Contracts\UserRepositoryContract', $userMock);
       
        // mock auth
        $authMock = Mockery::mock('Illuminate\Auth\AuthManager');
        $authMock->shouldReceive('fromUser')->once()->andReturn('register-token');
        $this->app->instance('Illuminate\Auth\AuthManager', $authMock);

        $params = ['email' => 'foo@bar.com', 'password' => 123456];

        
        $this->post('api/auth/register', $params)
            ->seeJsonEquals(['email not pass'])
            ->assertResponseStatus(400);

        $this->post('api/auth/register', $params)
            ->seeJsonEquals([
                'token'  => 'register-token',
            ])
            ->assertResponseStatus(200);

        
        $this->assertEquals('foo@bar.com', $createResult['email']);
        $this->assertTrue(password_verify(123456, $createResult['password']));
    }
}
