<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();
        DB::connection()->disableQueryLog();

//        DB::table('test_votes')->truncate();
//        DB::table('test_comments')->truncate();
//        DB::table('test_statuses')->truncate();
//        DB::table('test_attributes')->truncate();
//        DB::table('test_users')->truncate();

		 $this->call('UserTableSeeder');
		 $this->call('StatusesTableSeeder');
		 $this->call('CommentsTableSeeder');
		 $this->call('VotesTableSeeder');

        if(DB::connection() instanceof \Illuminate\Database\PostgresConnection) {
            // Do a VACUUM & analyze
            echo DB::statement('VACUUM (VERBOSE, ANALYZE)');
            echo PHP_EOL;
        }

        if(DB::connection() instanceof \Illuminate\Database\MySqlConnection) {
            // Do a FLUSH and OPTIMIZE TABLES
            echo DB::statement('FLUSH TABLES');
            echo PHP_EOL;
            echo DB::statement('OPTIMIZE TABLES');
            echo PHP_EOL;
        }
	}
}
