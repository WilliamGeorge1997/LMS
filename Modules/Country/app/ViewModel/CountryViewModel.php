<?php

namespace Modules\Country\ViewModel;

use Modules\Country\Services\CityService;
use Modules\Country\Services\CountryService;

class CountryViewModel
{
    public function countries()
    {
        return (new CountryService)->findByTenant(['id', 'title']);
    }

    public function citiesByCountry(int $countryId)
    {
        return (new CityService)->findBy('country_id', (string) $countryId, ['id', 'title']);
    }
}
