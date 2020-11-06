<?php

namespace App\Repositories;

use App\Models\Office;

class OfficeRepository extends ItemRepository
{

    /**
     * OfficeRepository constructor.
     *
     * @param Office $office
     */
    public function __construct(Office $office)
    {
        parent::__construct($office, 'Office');
    }
}