<?php
# models/SubjectOntology.php

require_once('../LibRDF/LibRDF/LibRDF.php');

class Ontosaur {

    private $rdf_store;
    private $rdf_model;
    public $labels;
    public $tree;
    public $flatlist;

    private function basic_string_massage($str) {
        
        // convert UTF-16 based C/C++/Java/Json-style to UTF-8
        $str = preg_replace_callback('/(?:\\\\u[0-9a-fA-Z]{4})+/', function ($v) {
            $v = strtr($v[0], array('\\u' => ''));
            return mb_convert_encoding(pack('H*', $v), 'UTF-8', 'UTF-16BE');
        }, $str);

        return $str;
    }

    private function url_massage($str) {

        $str = $this->basic_string_massage($str);

        // remove brackets at beginning and end
        $str = trim($str, '<>');

        // strip namespace:
        $str = explode('#', $str);
        $identifier = $str[1];

        // make spaces
        return $identifier;
    }

    private function string_massage($node) {
        //print get_class($node)."\n";
        switch (get_class($node)) {
            case 'LibRDF_URINode':
                return $this->url_massage($node);
            case 'LibRDF_LiteralNode':
                return $this->basic_string_massage($node);
        }
    }

    function __construct($url) {
        $this->warnings = array();
        // All models, i.e. graphs, reside in a storage. 
        // This defaults to memory.
        $this->rdf_store = new LibRDF_Storage();
        $this->rdf_model = new LibRDF_Model($this->rdf_store);

        $this->rdf_model->loadStatementsFromURI(
            new LibRDF_Parser('rdfxml'),
            $url // 'http://42.biblionaut.net/rdf/ont42.owl'
        );

        $this->labels = $this->get_labels();
        list($this->flatlist, $this->tree) = $this->get_tree();
    }

    function get_labels() {
        /*
            Get a flat array of prefLabels
        */
        $labels = array();
        $query = new LibRDF_Query('
          PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
          PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
          SELECT 
            DISTINCT ?node ?prefLabel
          WHERE {
           ?node rdf:type skos:Concept.
           ?node skos:prefLabel ?prefLabel
          }
          ', null, 'sparql');

        //print 'results of type ' . get_class($results); //LibRDF_BindingsQueryResults


        foreach ($query->execute($this->rdf_model) as $result) {
            $node = $this->string_massage($result['node']);
            $exploded_node = explode('@', $this->string_massage($result['prefLabel']));
            $label = $exploded_node[0];
            $label = trim($label, '"');
            if (count($exploded_node) === 1) {
                $lang = 'nb';
                $this->warnings[] = 'The prefLabel "' . $label . '" for the node ' . htmlspecialchars($result['node']) . ' did not specify language.';
            } else {
                $lang = $exploded_node[1];
            }
            if (empty($label)) {
                $this->warnings[] = 'The ' . $lang . ' prefLabel for the node ' . htmlspecialchars($result['node']) . ' is empty.';
            }

            //print "$node -> $label ($lang) \n";
            if (!array_key_exists($node, $labels)) {
                $labels[$node] = array();
            }
            $labels[$node][$lang] = array('prefLabel' => $label);
            //$flatlist[] = $child;
            //echo  . " is subclass of " . massage_str($result['subject2']) . "\n";
        }

        /*
            Extend list with altLabels
        */
        $query = new LibRDF_Query('
          PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
          PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
          SELECT 
            DISTINCT ?node ?altLabel
          WHERE {
           ?node rdf:type skos:Concept.
           ?node skos:altLabel ?altLabel
          }
          ', null, 'sparql');

        //print 'results of type ' . get_class($results); //LibRDF_BindingsQueryResults


        foreach ($query->execute($this->rdf_model) as $result) {
            $node = $this->string_massage($result['node']);
            $exploded_node = explode('@', $this->string_massage($result['altLabel']));
            $label = $exploded_node[0];
            $label = trim($label, '"');
            if (count($exploded_node) === 1) {
                $lang = 'nb';
                $this->warnings[] = 'The altLabel "' . $label . '" for the node ' . htmlspecialchars($result['node']) . ' did not specify language.';
            } else {
                $lang = $exploded_node[1];
            }
            if (empty($label)) {
                $this->warnings[] = 'The ' . $lang . ' altLabel for the node ' . htmlspecialchars($result['node']) . ' is empty.';
            }

            //print "$node -> $label ($lang) \n";
            if (!array_key_exists($node, $labels)) {
                $labels[$node] = array();
            }
            if (!isset($labels[$node][$lang])) {
                $this->warnings[] = 'The node ' . htmlspecialchars($result['node']) . ' has an altLabel, but no prefLabel in ' . $lang . '.';
            } else {
                if (!isset($labels[$node][$lang]['altLabel'])) $labels[$node][$lang]['altLabel'] = array();
                $labels[$node][$lang]['altLabel'][] = $label;
            }
            //$flatlist[] = $child;
            //echo  . " is subclass of " . massage_str($result['subject2']) . "\n";
        }
        return $labels;
    }

    function get_tree() {
        // Create a SPARQL query
        $query = new LibRDF_Query("
          PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
          PREFIX emneord: <http://42.biblionaut.net/emneord#>
          PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
          SELECT ?child ?parent
          WHERE
            { ?child skos:broader ?parent }
          ", null, 'sparql');


        $children = array();
        $flatlist = array();

        foreach ($query->execute($this->rdf_model) as $result) {
            $child = $this->string_massage($result['child']);
            $parent = $this->string_massage($result['parent']);
            if (array_key_exists($parent, $children)) {
                $children[$parent][] = $child;
            } else {
                $children[$parent] = array($child);
            }
            $flatlist[] = $child;
            //echo  . " is subclass of " . massage_str($result['subject2']) . "\n";
        }
        $flatlist = array_unique($flatlist);

        function append_node(&$tree, $children, $name) {
            $tree[$name] = array();
            if (array_key_exists($name, $children)) {
                foreach ($children[$name] as $child) {
                    append_node($tree[$name], $children, $child);
                }
            }
        }

        $tree = array();
        append_node($tree, $children, 'samling42');

        return array($flatlist, $tree);
    }

    function lookupPrefLabel($prefLabel, $prefLang) {

        $query = new LibRDF_Query('
            PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
            PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
            SELECT DISTINCT ?node ?altLabel
            WHERE { 
              ?node rdf:type skos:Concept .
              ?node skos:altLabel ?altLabel .
              ?node skos:prefLabel "' . $prefLabel . '"@' . $prefLang . '
            }
          ', null, 'sparql');

        $data = array(
            'prefLabel' => $prefLabel, 
            'altLabel' => array()
        );
        foreach ($query->execute($this->rdf_model) as $result) {
            $data['node'] = $result['node'];
            $node = $this->string_massage($result['node']);
            $exploded_node = explode('@', $this->string_massage($result['altLabel']));
            $label = $exploded_node[0];
            $label = trim($label, '"');
            if (count($exploded_node) === 1) {
                $lang = 'nb';
                $this->warnings[] = 'The altLabel "' . $label . '" for the node ' . htmlspecialchars($result['node']) . ' did not specify language.';
            } else {
                $lang = $exploded_node[1];
            }
            if (empty($label)) {
                $this->warnings[] = 'The ' . $lang . ' altLabel for the node ' . htmlspecialchars($result['node']) . ' is empty.';
            }

            //print "$node -> $label ($lang) \n";
            if (!array_key_exists($lang, $data['altLabel'])) {
                if ($lang == $prefLang) {
                    $data['altLabel'][] = $label;
                }
            }
            //$flatlist[] = $child;
            //echo  . " is subclass of " . massage_str($result['subject2']) . "\n";
        }
        return $data;
    }

}
