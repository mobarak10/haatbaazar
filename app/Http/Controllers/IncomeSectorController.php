<?php

namespace App\Http\Controllers;

use App\Http\Requests\IncomeSectorRequest;
use App\Models\IncomeSector;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IncomeSectorController extends Controller
{
    protected string $permission_for = 'income_sector';
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');

        return Inertia::render('IncomeSector/Index', [
            'income_sectors' => IncomeSector::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param IncomeSectorRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(IncomeSectorRequest $request)
    {
        $data = $request->validated();
        IncomeSector::create($data);

        return redirect()->back()->with('success', 'Income sector created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param IncomeSectorRequest $request
     * @param IncomeSector $incomeSector
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(IncomeSectorRequest $request, IncomeSector $incomeSector)
    {
        $data = $request->validated();
        $incomeSector->update($data);

        return redirect()->back()->with('success', 'Income sector updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param IncomeSector $incomeSector
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(IncomeSector $incomeSector)
    {
        $incomeSector->delete();
        return redirect()->back()->with('success', 'Income Sector deleted successfully.');
    }
}
