<?php

class ObjectController extends BaseController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $defaultPerPage = 25;
        $perPage = intval(Input::get('perPage', Session::get('perPage', $defaultPerPage)));
        if ($perPage <= 0 || $perPage > 500) $perPage = $defaultPerPage;
        Session::put('perPage', $perPage);
        $objects = Object::with('isbns', 'authors')->paginate($perPage);
        return View::make('object.index')
            ->with('title', 'Objekter')
            ->with('objects', $objects);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {   
        $obj = Object::find($id);
        if (!$obj) {
            return View::make('errors.missing');
        }
        $subjects = $obj->subjects()->orderBy('system')->get();
        return View::make('object.show')
            ->with('title', $obj->title)
            ->with('object', $obj)
            ->with('subjects', $subjects);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function showByBibsysId($id)
    {   
        $obj = Object::where('bibsys_id', '=', $id)->first();
        if (!$obj) {
            return View::make('errors.missing');
        }
        $subjects = $obj->subjects()->orderBy('system')->get();
        return View::make('object.show')
            ->with('title', $obj->title)
            ->with('object', $obj)
            ->with('subjects', $subjects);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

}