# jmeteranalyzer

JMeteranalyzer is an application to analyze JMeter logs, it has a PHP backend and mysql DB

This application has been developed by a senior Performance tester to analyze the JMeter results files after a test execution.

This aplication requires a PHP server and a MySQL database, a XAMPP server is perfect for it.

The application will generate Charts, tables and statistics for all the test and for each request/label.


:::Server Configuration:::

It is recomended but not mandatory do these configurations in PHP and MySQL

PHP:
File: xampp\php\php.ini (in a XAMPP server)
--------------------------------------------------
max_execution_time = 600 
max_input_time = 360 
post_max_size = 80M 
upload_max_filesize = 80M 
default_socket_timeout = 360

MySQL
File: xampp\mysql\bin\my.ini
--------------------------------------------------
max_allowed_packet = 100M

This configurations are done to let PHP and MySQL handle big requests and heavy processes.

In the file: \jmeteranalyzer\services\configuracion.php you should configure you Database

$db_host        =   "localhost";
$db_user        =   "juribe";
$db_password    =   'nombre y otorgar';
$db_database    =   "k12_results";


:::Jmeter Configuration:::

The file: jmeter.propierties should have this configuration:

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
