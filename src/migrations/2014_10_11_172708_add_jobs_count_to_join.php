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
            $table->integer('jobs_dispatched')->nullable();
            $table->integer('jobs_completed')->nullable();
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
