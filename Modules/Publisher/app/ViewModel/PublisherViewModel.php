<?php

namespace Modules\Publisher\ViewModel;

use Modules\Admin\Services\AdminService;

class PublisherViewModel{

    public function activeManagers(AdminService $adminService){
        return $adminService->findActiveManagers();
    }
}
