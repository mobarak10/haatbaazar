<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Models\CostCategory;
use Illuminate\Http\Request;

class CostCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render("CostCategory/Index", [
            "costCategories" => CostCategory::query()
                ->withCount('costSubcategories')
                ->orderBy('name')
                ->get()
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:App\Models\CostCategory',
            'description' => 'nullable|string',
        ]);

        $data['slug'] = Str::slug($request->name);
        CostCategory::create($data);

        return redirect()->back()->with('success', 'Cost category created successfully.');
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'id' => 'required|integer|exists:cost_categories',
            'name' => ['required', 'string', 'unique:App\Models\CostCategory,name,' . $id],
            'description' => 'nullable|string',
        ]);

        $data['slug'] = Str::slug($request->name);

        CostCategory::find($id)
            ->update($data);

        return redirect()->back()->with('success', 'Cost category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cost_category = CostCategory::query()
            ->withCount('costSubcategories')
            ->find($id);

        if ($cost_category->cost_subcategories_count > 0) {
            return redirect()->back()->with('errors', 'Failed to delete cost category. Cost category has some cost subcategories.');
        }

        $cost_category->delete();

        return redirect()->back()->with('success', 'Cost category deleted successfully.');

    }
}
