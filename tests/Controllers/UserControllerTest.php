<?php

class UserControllerTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testIndex()
    {
        // user mock
        $userMock = Mockery::mock('ApiMain\Repositories\Contracts\UserRepositoryContract');
        $userMock->shouldReceive('paginate')
            ->once()
            ->andReturnUsing(function () {
                return new Illuminate\Pagination\Paginator([], 15);
            });
        $this->app->instance('ApiMain\Repositories\Contracts\UserRepositoryContract', $userMock);

        // response
        $responseMock = Mockery::mock('Dingo\Api\Http\Response\Factory');
        $responseMock->shouldReceive('paginator')
            ->once()
            ->andReturn(['user-paginate-success']);
        $this->app->instance('Dingo\Api\Http\Response\Factory', $responseMock);

        $this->json('GET', 'api/users')
            ->seeJsonEquals([
                'user-paginate-success',
            ])
            ->assertResponseOk();
    }

    public function testEditPassword()
    {
        // middleware        
        $this->withoutAuth();

        $this->PUT('api/user/password')
            ->seeJsonStructure([
                'old_password',
                'password',
                'password_confirmation',
            ])
            ->assertResponseStatus(400);

        $params = [
            'old_password' => 123456,
            'password' => 123456,
            'password_confirmation' => 654321,
        ];

        $this->PUT('api/user/password', $params)
            ->seeJsonStructure([
                'password',
                'password_confirmation',
            ])
            ->assertResponseStatus(400);

        $params = [
            'old_password' => 123456,
            'password' => 12341234,
            'password_confirmation' => 12341234,
        ];

        $onceResult = false;
        \Auth::shouldReceive('once')
            ->twice()
            ->andReturnUsing(function ($params) use (&$onceAttribute, &$onceResult) {
                $onceAttribute = $params;

                return $onceResult;
            });

        $this->PUT('api/user/password', $params)
            ->assertResponseStatus(401);

        $onceResult = true;

        $updateResult = [];
        $userMock = Mockery::mock('ApiMain\Repositories\Contracts\UserRepositoryContract');
        $userMock->shouldReceive('update')->once()->andReturnUsing(function ($id, $attributes) use (&$updateResult) {
            $updateResult = $attributes;
            $updateResult['id'] = $id;

            return true;
        });
        $this->app->instance('ApiMain\Repositories\Contracts\UserRepositoryContract', $userMock);

        $this->PUT('api/user/password', $params)
            ->assertResponseStatus(204);

        $this->assertEquals(1, $updateResult['id']);
        $this->assertTrue(password_verify(12341234, $updateResult['password']));
    }

    public function testShow()
    {
        $this->withoutAuth();

        $userMock = Mockery::mock('ApiMain\Repositories\Contracts\UserRepositoryContract');
        $userMock->shouldReceive('find')->twice()->andReturnUsing(function ($id) {
            return $id > 5 ? true : false;
        });
        $this->app->instance('ApiMain\Repositories\Contracts\UserRepositoryContract', $userMock);

        $this->GET('api/users/1')
            ->assertResponseStatus(404);

        $responseMock = Mockery::mock('Dingo\Api\Http\Response\Factory');
        $responseMock->shouldReceive('item')
            ->once()
            ->andReturn(['user-item-success']);
        $this->app->instance('Dingo\Api\Http\Response\Factory', $responseMock);

        $this->GET('api/users/6')
            ->seeJsonEquals(['user-item-success'])
            ->assertResponseStatus(200);
    }

    public function testUserShow()
    {
        $this->withoutAuth();

        $responseMock = Mockery::mock('Dingo\Api\Http\Response\Factory');
        $responseMock->shouldReceive('item')
            ->once()
            ->andReturn(['user-item-success']);
        $this->app->instance('Dingo\Api\Http\Response\Factory', $responseMock);
        $this->GET('api/user')
            ->seeJsonEquals(['user-item-success'])
            ->assertResponseStatus(200);
    }

    public function testPatch()
    {
        $this->withoutAuth();

        $params = [
            'name' => [1],
            'avatar' => 'foobar',
        ];

        $this->PATCH('api/user', $params)
            ->seeJsonStructure([
                'name',
                'avatar',
            ])
            ->assertResponseStatus(400);

        $params = [
            'name' => str_random(51),
            'avatar' => 'http://www.lyyw.com',
        ];

        $this->PATCH('api/user', $params)
            ->seeJsonStructure([
                'name',
            ])
            ->assertResponseStatus(400);

        $updateResult = [];
        $userMock = Mockery::mock('ApiMain\Repositories\Contracts\UserRepositoryContract');
        $userMock->shouldReceive('update')->once()->andReturnUsing(function ($id, $attributes) use (&$updateResult) {
            $updateResult = $attributes;

            return true;
        });
        $this->app->instance('ApiMain\Repositories\Contracts\UserRepositoryContract', $userMock);

        // mock response
        $responseMock = Mockery::mock('Dingo\Api\Http\Response\Factory');
        $responseMock->shouldReceive('item')
            ->andReturn(['user-item-success']);
        $this->app->instance('Dingo\Api\Http\Response\Factory', $responseMock);

        $params = [
            'name' => 'foobar',
            'avatar' => 'http://www.lyyw.info',
            'foo' => 'bar',
        ];

        $this->PATCH('api/user', $params)
            ->seeJsonEquals(['user-item-success'])
            ->assertResponseStatus(200);

        $this->assertEquals(['name' => 'foobar', 'avatar' => 'http://www.lyyw.info'], $updateResult);

        $this->PATCH('api/user')
            ->seeJsonEquals(['user-item-success'])
            ->assertResponseStatus(200);
    }

    protected function withoutAuth()
    {
        $authMiddleware = Mockery::mock('Dingo\Api\Http\Middleware\Auth');
        $authMiddleware->shouldReceive('handle')->andReturnUsing(function ($request, $next) {
            return $next($request);
        });
        $this->app->instance('Dingo\Api\Http\Middleware\Auth', $authMiddleware);

        $userMock = Mockery::mock('Dingo\Api\Auth\Auth');
        $userMock->shouldReceive('user')
            ->andReturn((object) ['email' => 'foo@bar.com', 'id' => 1]);
        $this->app->instance('Dingo\Api\Auth\Auth', $userMock);
    }
}
