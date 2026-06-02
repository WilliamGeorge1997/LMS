<?php

namespace Modules\Book\ViewModel;

use Modules\Book\Services\BookService;
use Modules\Category\Services\CategoryService;
use Modules\Level\Services\LevelService;
use Modules\Publisher\Services\PublisherService;

class BookViewModel
{
    public function publishersByTenant()
    {
        return (new PublisherService)->findByTenant();
    }

    public function booksByTenant()
    {
        return (new BookService)->findByTenant(['id', 'title']);
    }

    public function categoriesByPublisher(int $publisherId)
    {
        return (new CategoryService)->findBy('publisher_id', (string) $publisherId, ['id', 'title']);
    }

    public function levelsByCategory(int $categoryId)
    {
        return (new LevelService)->findBy('category_id', (string) $categoryId, ['id', 'title']);
    }
}
