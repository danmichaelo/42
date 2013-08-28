<?php

class OntosaurController extends BaseController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //$srcUrl = 'http://folk.uio.no/kvalel/ont42.owl'; // ;42.biblionaut.net/rdf/ont42.owl';
        //$srcUrl = 'https://dl.dropboxusercontent.com/u/1007809/42/ont42.rdf';
        $srcUrl = 'http://www.ub.uio.no/om/organisasjon/ureal/ureal/samlinger/42/ontosaur.rdf';
        $ont = new Ontosaur($srcUrl);
        $warnings = $ont->warnings;

        $subject_lookup = array();

        // MIDLERTIDIG: 
        // Lag et map fra labels til identifier siden emneordene enda ikke har identifikatorer!
        $nb_labels = array();
        foreach ($ont->labels as $identifier => $labels) {
            if (isset($labels['nb'])) {
                $nb_labels[] = $labels['nb']['prefLabel'];
                if (isset($labels['nb']['altLabel'])) {
                    foreach ($labels['nb']['altLabel'] as $altLabel) {
                        $nb_labels[] = $altLabel;
                    }
                }
            } else {
                $warnings[] = 'No nb prefLabel found for ' . htmlspecialchars($identifier);
            }
        }
        $label_map = array(); 
        /*$res = DB::table('subjects')
            ->select('id', 'label_nb')
            ->whereIn('label_nb', $nb_labels)
            ->get();
        */

        if (empty($nb_labels)) {
            $warnings[] = 'No nb labels found';
        } else {
            $res = DB::table('subjects')
                ->select(DB::raw('subjects.label_nb, subjects.id, COUNT(object_subject.object_id) AS object_count'))
                ->join('object_subject', 'subjects.id', '=', 'object_subject.subject_id')
                ->where('subjects.system', '=', 'noubomn')
                ->whereIn('subjects.label_nb', $nb_labels)
                ->groupBy('object_subject.subject_id')
                ->orderBy('object_count', 'desc')
                ->get();
            foreach ($res as $el) {
                $label = $el->label_nb;
                $label = mb_strtoupper(mb_substr($label, 0, 1)) . mb_substr($label, 1); // Normalize
                $label_map[$label] = array(
                    'id' => $el->id,
                    'object_count' => $el->object_count
                );
            }
        }

        /*
        foreach ($ont->labels as $identifier => $labels) {
            $label_nb = $labels['nb'];

            // Når realfagstermer får identifikatorer kan vi slå opp på de istedet!

            $rows = DB::table('subjects')
                    ->select(DB::raw('subjects.id, COUNT(object_subject.object_id) AS object_count'))
                    ->join('object_subject', 'subjects.id', '=', 'object_subject.subject_id')
                    ->where('subjects.system', '=', 'noubomn')
                    ->where('subjects.label_nb', '=', $label_nb)
                    ->groupBy('object_subject.subject_id')
                    ->orderBy('object_count', 'desc')
                    ->get();

            foreach ($rows as $row) {
                $subject_lookup[$label_nb] = array(
                    $row['id'],
                    $row['object_count']
                );
            }
        }
/*
        foreach ($ont->flatlist as $subject) {

            $sth = $db->prepare('SELECT count(*) FROM subject WHERE name=?');
            $sth->execute(array($subject));
            $row = $sth->fetch();
            $counts[$subject] = $row[0];
        }*/

        $lang = Input::get('lang', 'nb');

        $langs = array('nb' => 'Norsk bokmål', 'en' => 'Engelsk');

        return View::make('ontosaur.index')
            ->with('labels', $ont->labels)
            ->with('tree', $ont->tree)
            ->with('lang', $lang)
            ->with('label_map', $label_map)
            ->with('supported_languages', $langs)
            ->with('src_url', $srcUrl)
            ->with('title', 'Ontologi')
            ->with('warnings', $warnings);
    }

}
