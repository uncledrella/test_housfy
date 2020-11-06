<?php

namespace Tests\Feature\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

use App\Models\Office;
use App\Repositories\OfficeRepository;
use App\Exceptions;

class OfficeRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /** @var StoreOffice */
    private $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new OfficeRepository(new Office, 'office');
    }

    public function testGet_read_ok()
    {
        $newOffice = Office::factory()->create();

        $office = $this->subject->get($newOffice->id);

        $this->assertEquals($newOffice->id, $office->id);
    }

    public function testCreate_insert_ok()
    {
        $officeData = Office::factory()->make()->toArray();

        $office = $this->subject->create($officeData);

        $this->assertDatabaseHas('offices', [
            'id' => $office->id
        ]);
    }

    public function testUpdate_modified_ok()
    {
        $office = Office::factory()->create();

        $office->name .= ' [[test]]';

        $this->subject->update($office->id, ['name' => $office->name, 'address' => $office->address]);

        $this->assertDatabaseHas('offices', [
            'id' => $office->id,
            'name' => $office->name
        ]);
    }

    public function testDelete_removed_ok()
    {
        $office = Office::factory()->create();

        $this->subject->delete($office->id);

        $this->assertDatabaseMissing('offices', [
            'id' => $office->id
        ]);
    }

    public function testCreate_insert_ko_missing_address_throws_exception()
    {
        $this->expectException(Exceptions\ApiValidationFailedException::class);

        $officeData = Office::factory()->make()->toArray();
        unset($officeData['address']);

        $this->subject->create($officeData);
    }

    public function testCreate_insert_ko_name_less_than_5_chars_throws_exception()
    {
        $this->expectException(Exceptions\ApiValidationFailedException::class);

        $office = Office::factory()->make();
        $office->name = 'abcd';

        $this->subject->create($office->toArray());
    }

    public function testUpdate_update_ko_name_less_than_5_chars_throws_exception()
    {
        $this->expectException(Exceptions\ApiValidationFailedException::class);

        $office = Office::factory()->create();
        $office->name = 'abcd';

        $this->subject->update($office->id, ['name' => $office->name, 'address' => $office->address]);
    }
}
