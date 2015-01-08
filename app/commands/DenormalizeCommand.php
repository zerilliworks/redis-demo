<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Predis\Client as RedisClient;

class DenormalizeCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'demo:denormalize';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create denormalized data from the sample set.';

    /**
     * Create a new command instance.
     *
     * @return \DenormalizeCommand
     */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $this->info("Generating denormalized structures...");
        $redisInstance = Redis::connection();
        DB::connection()->disableQueryLog();

        if($this->option('flush')) {
            $redisInstance->flushDb();
        }

        $initialKeys = (isset($redisInstance->info('keyspace')['Keyspace']['db0']['keys']) ? intval($redisInstance->info('keyspace')['Keyspace']['db0']['keys']) : 0);

        $this->info("There are:");
        $this->info("\t".User::count()." users");
        $this->info("\t".Status::count()." statuses");
        $this->info("\t".Comment::count()." comments");
        $this->info("\t".Vote::count()." votes");

         $this->denormalizeUsers($redisInstance);
         $this->denormalizeStatuses($redisInstance);
         $this->denormalizeComments($redisInstance);
         $this->denormalizeVotes($redisInstance);

        $currentKeys = $initialKeys - intval($redisInstance->info('keyspace')['Keyspace']['db0']['keys']);

        $this->info('Finished -- '.$currentKeys - $initialKeys.' keys created');
	}

    private function denormalizeUsers(RedisClient $redis)
    {
        $startTime = microtime(true);
        $uc = 0;
        foreach($this->batchUsers() as $user) {
            $redis->pipeline(function($pipe) use ($user)
            {
                $pipe->hMset('users:'.$user->id, array_merge($user->toArray(), $user->attributesArray()));
                $pipe->sAdd('users', 'users:'.$user->id);
            });

            $uc++;
            if($uc % 25 === 0) {echo "\rProcessed $uc users";}
        }
        $elapsedTime = microtime(true) - $startTime;
        $this->info("\nFinished in $elapsedTime seconds\n");
    }

    private function denormalizeStatuses(RedisClient $redis)
    {
        $startTime = microtime(true);
        $uc = 0;
        foreach ($this->batchStatuses() as $status) {
            $redis->pipeline(function($pipe) use ($status)
            {
                $pipe->hMset('statuses:'.$status->id, $status->toArray());
                $pipe->sAdd('statuses', 'statuses:'.$status->id);
                $pipe->sAdd('users:'.$status->user->id.':statuses', 'statuses:'.$status->id);
            });

            $uc++;
            if($uc % 100 === 0) { echo "\rProcessed $uc statuses";}
        }
        $elapsedTime = microtime(true) - $startTime;
        $this->info("\nFinished in $elapsedTime seconds\n");
    }

    private function denormalizeComments(RedisClient $redis)
    {
        $startTime = microtime(true);
        $uc = 0;
        foreach ($this->batchComments() as $comment) {
            $redis->pipeline(function($pipe) use ($comment)
            {
                $pipe->hMset('comments:'.$comment->id, array_only($comment->toArray(), ['user_id', 'status_id', 'body']));
                $pipe->sAdd('comments', 'comments:'.$comment->id);
                $pipe->sAdd('statuses:'.$comment->status_id.':comments', 'comments:'.$comment->id);
                $pipe->sAdd('statuses:'.$comment->status_id.':commenters', 'users:'.$comment->user_id);
                $pipe->sAdd('users:'.$comment->user->id.':comments', 'comments:'.$comment->id);
            });

            $uc++;
            if($uc % 500 === 0) { echo "\rProcessed $uc comments";}
        }
        $elapsedTime = microtime(true) - $startTime;
        $this->info("\nFinished in $elapsedTime seconds\n");
    }

    private function denormalizeVotes(RedisClient $redis)
    {
        $startTime = microtime(true);
        $uc = 0;
        foreach ($this->batchStatuses() as $status) {
            $rank = $status->votes()->sum('value');
            $redis->pipeline(function($pipe) use ($status, $rank)
            {
                $pipe->hSet('statuses:'.$status->id, 'rating', $rank);
                $pipe->zAdd('statuses:ranked', $rank, 'statuses:'.$status->id);
            });

            $uc++;
            if($uc % 1000 === 0) { echo "\rProcessed $uc votes";}
        }
        $elapsedTime = microtime(true) - $startTime;
        $this->info("\nFinished in $elapsedTime seconds\n");
    }

    private function batchUsers($chunks = null)
    {
        $userCount = User::count();

        for($i = 0; $i < $userCount; $i+=100) {
            $result = User::skip($i)->take(100)->get();
            $chunks !== null ? (yield $result) : null;
            foreach ($result as $r) {
                yield $r;
            }
        }
    }

    private function batchStatuses($chunks = null)
    {
        $statusCount = Status::count();

        for($i = 0; $i < $statusCount; $i+=100) {
            $result = Status::skip($i)->take(100)->get();
            $chunks !== null ? (yield $result): null;
            foreach ($result as $r) {
                yield $r;
            }
        }
    }

    private function batchComments($chunks = null)
    {
        $commentCount = Comment::count();

        for($i = 0; $i < $commentCount; $i+=100) {
            $result = Comment::skip($i)->take(100)->get();
            $chunks !== null ? (yield $result) : null;
            foreach ($result as $r) {
                yield $r;
            }
        }
    }

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			['flush', null, InputOption::VALUE_NONE, 'Flush the database first?.'],
		);
	}

}
