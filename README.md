## Laravel Join Jobs

A Laravel package that allows scheduling certain actions upon job completion or queue exhaustion. 

This package allows you to register a class or closure to be executed when an individual job completes. You can also register a class or closure when each and every job from a batch of jobs has finished running.  

<b>The package was sponsored by the good people at Pure SEO</b>


### Installation

The package is available from the Packagist composer repository. Add these to your `composer.json` file and run `composer install`:

```json
"masnun/joinjobs": "dev-master"
```

### Registering the Service Provider

Once you have installed the package, you need to register the service provider. Open `app/config/app.php` and add `Masnun\Joinjobs\JoinjobsServiceProvider` to the `providers` array. 

### Other relevant Configurations

* You must make sure that Laravel has an active database connection properly configured. 
* To test the queues, you need to configure Laravel queues properly. Use an async queue for the best results. 

Please consult the appropriate sections of the Laravel Docs to set them up properly. 

### Running The Migrations

The package uses certain database tables to keep track of jobs and queues. We need to run the migrations on the package to make sure the database schema is created properly. 

```bash
php artisan migrate --package=masnun/joinjobs
```

### Using the API

#### Creating a new Join and adding jobs to it

```php
	// Create a new JoinJobsManager object
    $joinjobsManager = new JoinJobsManager();

    // Create a join, pass a closure to be executed
    // when all the jobs are finished
    
    $queueId = $joinjobsManager->createJoin();
    $joinjobsManager->addHandlerToJoin($queueId, function() {
            echo "The Join successfully completed!";
    });

    // Add a job to that join
    $jobId = $joinjobsManager->addJob($queueId);

	// Use Laravel's API to push some jobs
    Queue::push('DemoJob', ['jobId' => $jobId, 'sleepDuration' => 5]);
    
	// We can add a handler once a job is created
    $joinjobsManager->addHandlerToJob($jobId, function() use($jobId) {
            echo "Completed Job ID: {$jobId}";
    });

	// Or we can also pass a handler as the second paramter
	// of addJob() method. We can also pass a class name
	// which has a method called join()
	$jobId = $joinjobsManager->addJob($queueId, "\\Masnun\\Joinjobs\\JoinHandler");
    Queue::push('DemoJob', ['jobId' => $jobId, 'sleepDuration' => 10]);

    return "Added two jobs!";
  ```
  
#### Marking a Job Complete

Once a job is complete, we must let our `JoinJobsManager` know that one of the job has finished executing. Here's a sample `DemoJob` class:

```php
class DemoJob
{
    public function fire($job, $data)
    {
        // We could put this in the DI containter 
        // if we use this a lot
        $joinjobsManager = new JoinJobsManager();

        $jobId = $data['jobId'];
        $sleepDuration = $data['sleepDuration'];
        
        sleep($sleepDuration);

        // Mark the job as complete
        $joinjobsManager->completeJob($jobId);

		// Remove the job from Queue
        $job->delete();
    }
}
```


