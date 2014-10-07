<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDispatchedOnJoins extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('masnun_joins', function($table)
		{
			$table->boolean('fully_dispatched');
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
			$table->dropColumn('fully_dispatched');
		});
	}

}
