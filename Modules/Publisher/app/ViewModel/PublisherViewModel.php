<?php

namespace Modules\Publisher\ViewModel;

use Modules\Admin\Services\AdminService;

class PublisherViewModel
{

    public function activeManagers()
    {
        return (new AdminService())->findActiveManagers();
    }
}
