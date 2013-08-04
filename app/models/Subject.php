<?php
# models/Subject.php

class Subject extends Eloquent {

    /* 
     * 
     */
    public static $keys = array('label_nb', 'system'); 
    public static $pivotColumns = array('subdivision', 'time', 'place', 'form');

    public function objects() {
    	// Will this work??? Let's test later:
    	// return call_user_func_array($this->belongsToMany('Object')->withPivot, static::$pivotColumns);
        return $this->belongsToMany('Object')
                    ->withPivot('subdivision', 'time', 'place', 'form');
    }

    public function label($lang) {
        $key = "label_$lang";
        $val = $this->$key;

        // Normalize:
        $val = mb_strtoupper(mb_substr($val, 0, 1)) . mb_substr($val, 1);

        return $val;
    }

}
