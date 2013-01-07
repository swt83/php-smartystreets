<?php

class Smartystreets_Tables {

    /**
     * Make changes to the database.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('smartystreets', function($table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->boolean('is_success');
            $table->string('original_hash', 32);
            $table->string('original_street', 100);
            $table->string('original_city', 100);
            $table->string('original_state', 2);
            $table->string('original_zip', 10);
            $table->string('hash', 32);
            $table->string('street', 100);
            $table->string('city', 100);
            $table->string('state', 2);
            $table->string('zip', 10);
            $table->text('response');
            $table->index('is_success');
            $table->index('original_hash');
            $table->index('hash');
        });
    }

    /**
     * Revert the changes to the database.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('smartystreets');
    }

}