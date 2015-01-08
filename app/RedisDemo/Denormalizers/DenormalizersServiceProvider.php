<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 8/3/14
// Time: 9:28 AM
// For: Redis Demo


namespace RedisDemo\Denormalizers;


use Comment;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Status;
use User;
use Vote;

class DenormalizersServiceProvider extends ServiceProvider {

    public function boot()
    {
        return;
        // Listen for Eloquent events
        User::observe(new UserDenormalizer);
        Status::observe(new StatusDenormalizer);
        Comment::observe(new CommentDenormalizer);
        Vote::observe(new VoteDenormalizer);

        // Listen for domain-specific events
        Event::listen('status.voted', 'VoteDenormalizer@recordVote');
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('UserDenormalizer', function() {
            return new UserDenormalizer();
        });

        $this->app->bind('StatusDenormalizer', function() {
            return new StatusDenormalizer();
        });

        $this->app->bind('CommentDenormalizer', function() {
            return new CommentDenormalizer();
        });

        $this->app->bind('VoteDenormalizer', function() {
            return new VoteDenormalizer();
        });
    }
}