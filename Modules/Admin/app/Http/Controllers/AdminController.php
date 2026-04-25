<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Admin\Services\AdminService;
use Yajra\DataTables\Facades\DataTables;

class AdminController extends Controller
{
    public function __construct(private AdminService $adminService) {}

    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $length = (int) $request->input('length', 10);
            $request->merge(['length' => max(1, min($length, 50))]);
            $query = $this->adminService->queryForDataTable();
            return DataTables::eloquent($query)->toJson();
        }
        return view('admin::admins.index');
    }

    public function dashboard(Request $request)
    {
        return view('admin::dashboard');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('admin::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('admin::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
