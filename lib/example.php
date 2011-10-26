<?php
    // Include the helper library
    require "opendining.php";
	
	// Set API key and instantiate the client
	// Need an API Key?  Check out http://www.opendining.net/developers
    $key = 'Your API Key Goes Here';
    $client = new OpenDiningClient($key);
    
	// OK, we're done setting up the client...
	// Let's do some stuff!
	
	//
	//	Do a search for restaurants around Cleveland, OH
	//	We know that the latitude and longitude for Cleveland is approx. 41.4801503, -81.7244457
	//
	
	$params = array(
					'lat' => 41.4801503,
					'lon' => -81.7244457
				);
	
	$response = $client->request('search/restaurants', 'GET', $params);
	
	if ($response->is_error)
	{
    	echo "Error: {$response->error}";
    }
	else
	{
    	echo "<h2>Search Results</h2>";
		echo "<pre>";
		var_dump($response->data);
		echo "</pre>";
	}
	
	//
	// 	Get the restaurant information for Tres Potrillos
	//	This is one of our favorite restaurants!
	//
	
	$restaurant = '4c464aea8ead0eb261000000';
    $response = $client->request("restaurants/$restaurant");
    
    if ($response->is_error)
	{
    	echo "Error: {$response->error}";
    }
	else
	{
    	echo "<h2>Restaurant Info</h2>";
		echo "<pre>";
		var_dump($response->data);
		echo "</pre>";
	}
    
	//
	//	Get the Tres Potrillos menu, broken up into categories
	//	(we call this the "tiered" menu, as opposed to the "flat" menu, which just lists all the items)
	//
    
	$response = $client->request("/restaurants/$restaurant/menu/tier");
    
    if ($response->is_error)
	{
    	echo "Error: {$response->error}";
    }
	else
	{
    	echo "<h2>Menu</h2>";
		echo "<pre>";
		var_dump($response->data);
		echo "</pre>";
	}