<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BrandController extends Controller
{
    protected string $permission_for = 'brand';
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');
        return Inertia::render('Brand/Index', [
            'brands' => Brand::addProductName()
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->hasPermission('create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->hasPermission('create');
        $data = $request->validate([
            'name' => 'required|string|max:50',
        ]);

        $data['slug'] = Str::slug($request->name);
        Brand::create($data);

        return redirect()->back()->with('success', 'Brand created successfully.');
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
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->hasPermission('update');
        $data = $request->validate([
            'name' => 'required|string|max:50',
        ]);

        $data['slug'] = Str::slug($request->name);
        $brand = Brand::findOrFail($id);
        $brand->update($data);

        return redirect()->back()->with('success', 'Brand updated successfully.');
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
        Brand::find($id)->delete();
        return back();
    }
}
