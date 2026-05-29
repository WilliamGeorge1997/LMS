<?php

namespace Modules\Level\ViewModel;

use Modules\Category\Services\CategoryService;
use Modules\Publisher\Services\PublisherService;

class LevelViewModel
{
    public function publishersByTenant()
    {
        return (new PublisherService)->findByTenant();
    }

    public function categoriesByPublisher(int $publisherId)
    {
        return (new CategoryService)->findBy('publisher_id', (string) $publisherId, ['id', 'title']);
    }
}
