<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTables extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        // Corresponds to 'manifestations' in the FRBR model
        Schema::create('objects', function(Blueprint $table) {
            $table->increments('id');
            $table->string('bibsys_id', 9)->unique()->nullable();   // possibly we could story it as integer of length-9 and left zero-pad it to get objektids like 042482321
            $table->string('linked_bibsys_id', 9)->unique()->nullable();    // for electronic version
            $table->string('lccn', 10)->unique()->nullable();       // 2 + 6 from 1898 to 2000, then 4 + 6, again we could store it as string, but it becomes a little bit complicated to pad correclty
            $table->smallInteger('year', 4)->unsigned()->nullable();
            $table->smallInteger('pagecount', 4)->unsigned()->nullable();
            $table->text('title');
            $table->text('subtitle')->nullable();
            $table->text('responsibility')->nullable();
            $table->string('publisher', 30)->nullable();
            $table->string('place', 30)->nullable();
            $table->string('dimensions', 30)->nullable();
            $table->timestamps();
            $table->dateTime('oai_date')->nullable();
            $table->string('version', 20); #005
        });

        Schema::create('isbns', function(Blueprint $table) {
            $table->increments('id');
            $table->string('object_id', 10);
            $table->string('number', 13); // string, can include x for 10, 
                                        // ->unique() ? or not...?
            $table->string('form', 10)->nullable(); // h., ib.
            $table->timestamps();
        });

        Schema::create('authors', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('authority', 50);
            $table->timestamps();
            $table->unique(array('name', 'authority'));
        });

        Schema::create('object_authors', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('object_id');
            $table->integer('author_id');
            $table->timestamps();
        });

        Schema::create('subjects', function(Blueprint $table) {
            $table->increments('id');
            $table->string('label_nb', 50);
            $table->string('label_en', 50);
            $table->string('system', 20)->default('');      # eg. 'noubomn'
            $table->string('kind', 10); // main, time, place, form
            $table->timestamps();
            $table->unique(array('label_nb', 'system'));
        });

        Schema::create('object_subject', function(Blueprint $table) {
            $table->integer('object_id');
            $table->integer('subject_id');
            $table->string('subdivision', 50);
            $table->string('time', 50);
            $table->string('place', 50);
            $table->string('form', 50);
            $table->index(array('object_id', 'subject_id')); // not unique..
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('objects');
        Schema::drop('isbns');
        Schema::drop('authors');
        Schema::drop('object_authors');
        Schema::drop('subjects');
        Schema::drop('object_subject');
    }

}
