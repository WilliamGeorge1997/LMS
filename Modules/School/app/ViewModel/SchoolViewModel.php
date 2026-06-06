<?php

namespace Modules\School\ViewModel;

use Modules\Country\Services\CountryService;

class SchoolViewModel
{
    public function countries()
    {
        return (new CountryService)->active();
    }
}
