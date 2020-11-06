<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WebserviceControllerTest extends TestCase
{

    use DatabaseTransactions;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testBasic_test()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testGet_api_invalid_route()
    {
        $response = $this->get('/api/thisroutedoesnotexists');
        $response->assertStatus(404);

        $response = $this->get('/api/this-route-does-not-exists');
        $response->assertStatus(404);

        $response = $this->get('/api/offices');
        $response->assertStatus(404);

        $response = $this->get('/api/office/a');
        $response->assertStatus(404);

        $response = $this->get('/api/office/123abc');
        $response->assertStatus(404);
    }

    public function testPost_api_invalid_route()
    {
        $response = $this->post('/api/thisroutedoesnotexists');
        $response->assertStatus(404);

        $response = $this->post('/api/this-route-does-not-exists');
        $response->assertStatus(404);

        $response = $this->post('/api/offices');
        $response->assertStatus(404);

        $response = $this->post('/api/office/a');
        $response->assertStatus(404);

        $response = $this->post('/api/office/123abc');
        $response->assertStatus(404);
    }

    public function testPut_api_invalid_route()
    {
        $response = $this->put('/api/thisroutedoesnotexists');
        $response->assertStatus(404);

        $response = $this->put('/api/this-route-does-not-exists');
        $response->assertStatus(404);

        $response = $this->put('/api/offices');
        $response->assertStatus(404);

        $response = $this->put('/api/office/a');
        $response->assertStatus(404);

        $response = $this->put('/api/office/123abc');
        $response->assertStatus(404);
    }

    public function testDelete_api_invalid_route()
    {
        $response = $this->delete('/api/thisroutedoesnotexists');
        $response->assertStatus(404);

        $response = $this->delete('/api/this-route-does-not-exists');
        $response->assertStatus(404);

        $response = $this->delete('/api/offices');
        $response->assertStatus(404);

        $response = $this->delete('/api/office/a');
        $response->assertStatus(404);

        $response = $this->delete('/api/office/123abc');
        $response->assertStatus(404);
    }

    public function testGet_api_valid_route()
    {
        $response = $this->get('/api/office');
        $response->assertStatus(200);

        $office = \App\Models\Office::factory()->create();
        $response = $this->get('/api/office/'.$office->id);
        $response->assertStatus(200);
    }

    public function testPost_api_valid_route()
    {
        $response = $this->post('/api/office');
        $response->assertStatus(422);
    }

    public function testCreate_office()
    {
        $officeData = \App\Models\Office::factory()->make()->toArray();
        $response = $this->post('/api/office', $officeData);
        
        $response->assertStatus(201);

        $this->assertDatabaseHas('offices', [
            'name' => $officeData['name'],
            'address' => $officeData['address']
        ]);
    }

    public function testCreate_office_validation_error()
    {
        $office = \App\Models\Office::factory()->make();

        $officeData = $office->toArray();
        unset($officeData['address']);
        $response = $this->post('/api/office', $officeData);
        
        $response->assertStatus(422);

        $officeData = $office->toArray();
        $officeData['name'] = '1234';
        $response = $this->post('/api/office', $officeData);
        
        $response->assertStatus(422);
    }

}
