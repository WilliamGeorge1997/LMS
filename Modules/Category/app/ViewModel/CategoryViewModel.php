<?php

namespace Modules\Category\ViewModel;

use Modules\Publisher\Services\PublisherService;

class CategoryViewModel
{
    public function publishersByTenant()
    {
        return (new PublisherService())->findByTenant();
    }
}