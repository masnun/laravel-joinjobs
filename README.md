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

The process is simple: 

* Create a new join, pass it a handler. Choose whether it should be auto deleted or not. The handler could be a closure or fully qualified class name that has a `run()` method. The second parameter is for auto deletion. 

* We add jobs to the Join. Each job can take an optional closure/class to be executed. 

* We have to let the system know when all jobs have been dispatched. We can pass the optional 3rd argument to a job as `true` to mark it as the last job. Or alternatively we can call the `setFullyDispatched($joinId)` method on the JoinJobsManager. 

	Eg. `$joinjobsManager->setFullyDispatched($joinId)`  
	
	This is a requirement to make sure that our JoinHandler is not executed before all jobs have been dispatched while using a `sync` driver. 
 

#### Creating a new Join and adding jobs to it

```php
 	 
 	 // Create a new manager
    $joinjobsManager = new JoinJobsManager();

    // We create a new join, set auto deletion to true
    $joinId = $joinjobsManager->createJoin(null, true);
    
    $joinjobsManager->addHandlerToJoin($joinId, function() {
            echo "The Join successfully completed!";
    });

    // addJob()
    $jobId = $joinjobsManager->addJob($joinId);


    Queue::push('DemoJob', ['jobId' => $jobId, 'sleepDuration' => 5]);

    $joinjobsManager->addHandlerToJob($jobId, function() use($jobId) {
            echo "Completed Job ID: {$jobId}";
    });

	// This job is the last one, we are not adding any more jobs
    $jobId = $joinjobsManager->addJob($joinId, "\\Masnun\\Joinjobs\\JoinHandler", true);
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

#### Auto Deletion

The `createJoin()` method takes a second optional argument. If we set it true, the join and all jobs related to it will be deleted. 

#### Manual Deletion


	$joinjobsManager->deleteJoin($joinId);
	
That should delete the join and all the jobs belonging to it. 

