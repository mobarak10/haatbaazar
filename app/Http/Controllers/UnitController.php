<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnitStoreRequest;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;

class UnitController extends Controller
{
    protected string $permission_for = 'unit';
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');
        return Inertia::render('Unit/Index', [
            'units' => Unit::with('products')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $this->hasPermission('create');
        return Inertia::render("Unit/Create", [
            'units' => config('haatbaazar.unit')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UnitStoreRequest $request)
    {
        $this->hasPermission('create');
//        return $request->all();
        $data = $request->validated();
        $data['code'] = \str_pad(Unit::withTrashed()->max("id") + 1, 4, 0, STR_PAD_LEFT);

        Unit::create($data);
        return Redirect::route('unit.index')->with('success', 'Unit created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->hasPermission('show');
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->hasPermission('update');
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UnitStoreRequest $request, $id)
    {
        $this->hasPermission('update');
        $data = $request->validated();
        $unit = Unit::findOrFail($id);

        $unit->update($data);
        return Redirect::route('unit.index')->with('success', 'Unit updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->hasPermission('delete');
        $unit = Unit::findOrFail($id);
        $unit->delete();
        return Redirect::route('unit.index')->with('success', 'Unit Delete successfully');
    }
}
