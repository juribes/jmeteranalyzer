# JMeteranalyzer

#### Summary
JMeteranalyzer is an application to analyze JMeter logs, it has a PHP backend and mysql DB.

#### Details
This application has been developed by a senior Performance tester to analyze the JMeter results files after a test execution.

This application has been developed as a SPA (Single Page Application) with HTML5, Bootstrap, AngularJS, PHP and a MySQL database. It requires a PHP server and a MySQL database, a XAMPP server is perfect for it.

The application will generate Charts, Tables and Statistics for all the test and for each request/label, it also allows to the Average, Maximum and Minimum response time in specific range of time of a test.


# Server Configuration

It is recommended but not mandatory do these configurations in PHP and MySQL

### PHP
File: xampp\php\php.ini (in a XAMPP server)
```
max_execution_time = 1200 
max_input_time = 360 
post_max_size = 80M 
upload_max_filesize = 80M 
default_socket_timeout = 360
```

### MySQL
File: xampp\mysql\bin\my.ini (in a XAMPP server)
```
max_allowed_packet = 100M
```

This configurations are done to let PHP and MySQL handle big requests and heavy processes.


# Database Configuration

In the file: \jmeteranalyzer\services\configuracion.php you should configure you Database
```
$db_host        =   "<db_server>";
$db_user        =   "<user>";
$db_password    =   "<password>";
$db_database    =   "<database>";
```
The user should have full rights on the specific database. 

# Installation

To install the database, you only need to request in you browser this URL:
```
http://<your server>/jmeteranalyzer/services/install.php
```
e.g http://localhost/jmeteranalyzer/services/install.php  (if you have the application in your PC)

# JMeter Configuration

It is important to do the configurations on JMeter to generate the results log with the correct structure. For a non-gui execution you need to modify the file: jmeter.propierties (apache-jmeter-2.##/bin) adding these lines:

```
jmeter.save.saveservice.output_format=csv
jmeter.save.saveservice.data_type=false
jmeter.save.saveservice.label=true
jmeter.save.saveservice.response_code=true
jmeter.save.saveservice.response_data.on_error=false
jmeter.save.saveservice.response_message=false
jmeter.save.saveservice.successful=true
jmeter.save.saveservice.thread_name=true
jmeter.save.saveservice.time=true
jmeter.save.saveservice.subresults=false
jmeter.save.saveservice.assertions=true
jmeter.save.saveservice.latency=true
jmeter.save.saveservice.bytes=true
jmeter.save.saveservice.hostname=true
jmeter.save.saveservice.thread_counts=true
jmeter.save.saveservice.sample_count=true
jmeter.save.saveservice.timestamp_format=ms
jmeter.save.saveservice.default_delimiter=,
jmeter.save.saveservice.autoflush=true
jmeter.save.saveservice.print_field_names=true
```
For a GUI execution you need to add a "Summary Report" listener and configure it as the image:

![guiconfig fw](https://cloud.githubusercontent.com/assets/8532620/9148060/18482a2e-3d37-11e5-9a1c-0d9432ddce9f.png)


# License

The license that I like is "CC BY-NC-SA" (Attribution-NonCommercial-ShareAlike 2.0 Generic). I think is fair (https://creativecommons.org/licenses/).

It uses canvas.js that has other license conditions (Creative Commons Attribution-NonCommercial 3.0 License) (http://canvasjs.com/license-canvasjs/) and it also uses an uploading code got from another Githug project (https://github.com/nervgh/angular-file-upload)


# Bugs and Issues
If you find an issue please submit it [here](https://github.com/gallinazo/jmeteranalyzer/issues).

All the ideas are welcome too, so please let me know if there is any functionality you want.


# Functionalities Screenshots

### General information
![generalinfo](https://cloud.githubusercontent.com/assets/8532620/9147584/c99ba2d2-3d2e-11e5-9d4a-adf5dbb6a7ff.png)

### Summary
![summary](https://cloud.githubusercontent.com/assets/8532620/9147587/c99ff468-3d2e-11e5-8977-95fd4027d1b4.png)

### Timeline
![timeline](https://cloud.githubusercontent.com/assets/8532620/9147588/c9a11f78-3d2e-11e5-9beb-577c71b0a50c.png)

### Timeline data
![timeline_data](https://cloud.githubusercontent.com/assets/8532620/9147589/c9a227f6-3d2e-11e5-87b5-b36a24f33d43.png)

### Percentiles
![percentiles](https://cloud.githubusercontent.com/assets/8532620/9147586/c99ea4d2-3d2e-11e5-9d44-4e2312ce6a70.png)

### Home
![home](https://cloud.githubusercontent.com/assets/8532620/9147585/c99dc508-3d2e-11e5-8d3e-7632a74856da.png)

### Upload
![upload](https://cloud.githubusercontent.com/assets/8532620/9147590/c9b13ec6-3d2e-11e5-9ff3-eec6d68e0680.png)

