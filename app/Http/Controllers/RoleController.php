<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Services\MenuService\MenuService;
use App\Services\PermissionService\PermissionService;
use App\Services\RoleService\RoleService;
use Couchbase\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleController extends Controller
{
    protected string $permission_for = 'role';
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(RoleService $service)
    {
        // has permission
        $this->hasPermission('view');

        // get all roles
        $roles = $service->allRoles();
        return Inertia::render('Role/Index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create(MenuService $menuService, PermissionService $permissionService)
    {
        // check permission
        $this->hasPermission('create');
        // get created menus
        $menus = $menuService->createdMenuItems();
        // get permissions
        $permissions = $permissionService->permissions();

        return Inertia::render('Role/Create', [
            'menus' => $menus,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleStoreRequest $request, RoleService $roleService)
    {
        // check permission
        $this->hasPermission('create');

        // create a new rule
        $role = $roleService->createRole($request);

        if (is_array($request->permissions)) {
            // sync permission role
            $roleService->syncPermissionsToRole($role, $request->permissions);
        }

        // return redirect to index form
        return redirect()->route('role.index')->withSuccess('Role created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function show($id, RoleService $roleService, MenuService $menuService, PermissionService $permissionService)
    {
        // check permission
        $this->hasPermission('show');

        // get role details
        $role = $roleService->roleDetailsById($id);

        // get created menu items
        $menus = $menuService->createdMenuItems();

        // get permissions
        $permissions = $permissionService->permissions();

        //
        $selectedPermission = $role->permissions->pluck('id')->all();

        return Inertia::render('Role/Show', [
            'role' => $role,
            'menus' => $menus,
            'permissions' => $permissions,
            'selectedPermission' => $selectedPermission,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function edit($id, RoleService $roleService, MenuService $menuService, PermissionService $permissionService)
    {
        // check permission
        $this->hasPermission('update');

        // get role
        $role = $roleService->roleDetailsById($id);
        $selectedPermission = $role->permissions->pluck('id')->all();

        // get created menu items
        $menus = $menuService->createdMenuItems();

        // get permissions
        $permissions = $permissionService->permissions();

        return Inertia::render('Role/Edit', [
            'role' => $role,
            'menus' => $menus,
            'permissions' => $permissions,
            'selectedPermission' =>$selectedPermission,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleUpdateRequest $request, $id, RoleService $roleService)
    {
        // check permission
        $this->hasPermission('update');

        // get role by id
        $role = $roleService->updateRoleById($request, $id);

        // is has permissions
        if (is_array($request->permissions)) {
            $roleService->syncPermissionsToRole($role, $request->permissions);
        }

        return redirect()->route('role.index')->withSuccess('Role updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, RoleService $roleService)
    {
        if ($roleService->permanentlyDeleteRoleById($id)) {
            return redirect()->route('role.index')->withSuccess('Role deleted successfully');
        } else {
            return redirect()->back()->withError('Failed to delete role');
        }
    }
}
