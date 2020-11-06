<?php

namespace Tests\Unit\Http\Requests;

use PHPUnit\Framework\TestCase;

use App\Http\Requests\StoreOffice;

class StoreOfficeTest extends TestCase
{
	/** @var StoreOffice */
    private $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new StoreOffice();
    }

    public function testRules()
    {
        $this->assertEquals([
            'name' => 'required|string|between:5,80',
            'address' => 'required|string|between:10,250'
            ],
            $this->subject->rules()
        );
    }

    public function testAuthorize()
    {
        $this->assertTrue($this->subject->authorize());
    }
}