<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        // $result = $this->dashboardService->getDataDashboard();

        // return $result;
        try {
            $result = $this->dashboardService->getDataDashboard();

            return $this->success("Successfully retrieve data", 200, $result);
        }
        catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
