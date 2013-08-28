<?php

class SubjectController extends BaseController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $system = Input::get('system');
        $min_count = intval(Input::get('threshold', '1'));

        if (is_null($system)) {
            $subjects = Subject::where('label_nb', '!=', '')->get();
        } else {
            /*$subjects = Subject::where('system', '=', $system)
                               ->where('label_nb', '!=', '')->get();
             */
            $subjects = DB::table('subjects')
                    ->select(DB::raw('subjects.id, subjects.label_nb, COUNT(object_subject.object_id) AS object_count'))
                    ->join('object_subject', 'subjects.id', '=', 'object_subject.subject_id')
                    ->where('subjects.system', '=', $system)
                    ->where('subjects.label_nb', '!=', '')
                    ->groupBy('object_subject.subject_id')
                    ->orderBy('object_count', 'desc')
                    ->get();

            $subjects = array_filter($subjects, 
                function($s) use ($min_count) { return ($s->object_count > $min_count); }
                );
        }

        $systems = DB::table('subjects')
                     ->where('system', '!=', '')
                     ->groupBy('system')->get();

        return View::make('subject.index')
            ->with('title', 'Emner')
            ->with('subjects', $subjects)
            ->with('systems', $systems)
            ->with('system', $system);
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
    public function showById($id)
    {
  
        $subj = Subject::find($id);

        // We should eventually do eager loading, something like this
        //$subj = Subject::with('objects.subjects')->find($id);

//        var_dump($subj->objects()->getQuery()->getSQL());

        if (!$subj) {
            return View::make('errors.missing');
        }
  
        $objects = $subj->objects()->orderBy('year')->get();

        if (Input::get('format') == 'json') {
            return Response::json(array(
                //'id' => $id,
                'subject' => $subj->toArray(),
                'objects' => $objects->toArray()
            ));
        } else {
            return View::make('subject.show')
                ->with('title', $subj->label_nb)
                ->with('subject', $subj)
                ->with('objects', $objects);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function showByLabel($label)
    {
  
        $subjects = Subject::where('label_nb', '=', $label)->get();

        if (!$subjects) {
            return View::make('errors.missing');
        }
        foreach ($subjects as $subject) {
            $objects = $subject->objects()->orderBy('year')->get();
        }

        $subject = $subjects[0];


        if (Input::get('format') == 'json') {
            return Response::json(array(
                //'id' => $id,
                'subject' => $subject->toArray(),
                'objects' => $objects->toArray()
            ));
        } else {
            return View::make('subject.show')
                ->with('title', "$label")
                ->with('subjects', $subjects)
                ->with('subject', $subject)
                ->with('objects', $objects);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function showBySystemAndLabel($system, $label)
    {
  
        $labels = array($label);
        if ($system == "noubomn") {

            // Check for alternative forms:

            $ontologyUrl = 'http://folk.uio.no/kvalel/ont42.owl'; // ;42.biblionaut.net/rdf/ont42.owl';
            $ont = new Ontosaur($ontologyUrl);
            $ontData = $ont->lookupPrefLabel($label, 'nb');
            foreach ($ontData['altLabel'] as $label) {
                $labels[] = $label; 
            }

        }

        $subjects = array();
        $objects = array();

        $fnd = false;
        foreach ($labels as $label) {

            $subj = Subject::where('label_nb','=',$label)
                           ->where('system','=',$system)->first();

    //        var_dump($subj->objects()->getQuery()->getSQL());
            
            if ($subj) {
                $fnd = true;
                $subjects[] = $subj;
                foreach ($subj->objects()->orderBy('year')->get() as $obj) {
                    $objects[] = $obj;
                }
            }

        }

        if (!$fnd) {
            return View::make('errors.missing');
        }

        if (Input::get('format') == 'json') {

            // Map using a Lambda function (requires php 5.3.0)
            $func = function($value) {
                return $value->toArray();
            };

            return Response::json(array(
                //'id' => $id,
                'subjects' => array_map($func, $subjects),
                'objects' => array_map($func, $objects)
            ));
        } else {
            return View::make('subject.show')
                ->with('title', "$label")
                ->with('subjects', $subjects)
                ->with('objects', $objects);
        }
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