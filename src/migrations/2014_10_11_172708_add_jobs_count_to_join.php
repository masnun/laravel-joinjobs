<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJobsCountToJoin extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('masnun_joins', function($table)
        {
            $table->integer('jobs_dispatched');
            $table->integer('jobs_completed');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('masnun_joins', function($table)
        {
            $table->dropColumn('jobs_dispatched', 'jobs_completed');
        });
	}

}
