<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('masnun_jobs', function($table)
        {
            $table->increments('id');
            $table->string('join_id', 100)->nullable();
            $table->boolean('is_complete');
            $table->text('on_complete');
            $table->dateTime('created_at');
            $table->dateTime('completed_at');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('masnun_jobs');
	}

}
