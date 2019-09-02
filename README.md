# PHP Query Builder - PHP based Quick Query Builder  

This project will let you perform quick queries in PHP to a MySQL (MySQLi) database.

## Getting Started

Edit the config.json to match the server you are using. 
If you are using a service such as https://ngrok.com you may need to add the url to your Business Manager allowed origins.
**NOTE:** This package only forwards OCAPI requests from one point to another. The main purpose is for routing data around CORS and is typically useful for Mobile Applications. x-dw-client-id as a header attribute must be used instead of client_id as a url parameter.

## Prerequisites
```
Required:
    PHP
    IDE Any will do
    Local Webserver

## Installing

copy the querybuilder.php to a location within your project
```

## Usage 

In your PHP Code: 
```
include('querybuilder.php');

$qBuild = new QueryBuilder($conn); 
$columns = "member_id,student_active,member_name,student_custom,student_rank,member_custom";
$w1 = new WhereBuilder("org_id","$org->org_id","="); 
$w2 = new WhereBuilder("member_custom","$member_barcode","=");
$whereList = array();
array_push($whereList,$w1);
array_push($whereList,$w2);

$thisMember = $qBuild->select('students')->columns($columns)->where($whereList)->run();

$firstMember = $thisStudent[0];
```
Valid data from the QueryBuilder will return an array of json rows or no rows if there is an error or the query found no records. This will be expanded in future documentaiton.

## Logging

A Logger Module is in development

## Built With

* [**PHP**](https://php.net) 

## Authors

* **John Facey II** - *Lead Developer*  
[![GitHub followers](https://img.shields.io/github/followers/johnfacey.svg?label=Follow&style=social)](https://github.com/johnfacey)
[![Twitter followers](https://img.shields.io/twitter/follow/johnfacey.svg?label=Follow&style=social)](https://twitter.com/johnfacey)


## License

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

See the [LICENSE.md](LICENSE.md) file for details
