<?php

### ** server information

# a unique name for this uxpanel instance
$config['site_name'] = "UXPanel Unknown Site";

# path to GHost on server, with trailing slash
$config['ghost_path'] = "/ghost/";

# password to use for GHost users
$config['ghost_password'] = "abc";

# format to use for displaying times
$config['format_time'] = "j M Y H:i:s T";

# format to use for displaying dates
$config['format_date'] = "j M Y";

### ** email information

# email address to send mail from
$config['mail_from'] = "uxpanel@example.com";

# name to use when sending mail
$config['mail_fromname'] = "UXPanel";

### ** account information

# admin account username
$config['admin_username'] = "admin";

# admin account password
$config['admin_password'] = "";

### ** database information

# host
$config['db_hostname'] = "localhost";

# database name
$config['db_name'] = "uxpanel";

# username
$config['db_username'] = "root";

# password
$config['db_password'] = "";

### ** lock configuration

# the time in seconds a user must wait before trying again; otherwise they get locked out (count not increased)
$config['lock_time_initial'] = array('checkuser' => 5, 'checkadmin' => 5);

# the time that overloads last
$config['lock_time_overload'] = array('checkuser' => 60*2, 'checkadmin' => 60*2);

# the number of tries a user has (that passes the lock_time_initial test) before being locked by overload
$config['lock_count_overload'] = array('checkuser' => 12, 'checkadmin' => 12);

# if a previous lock found less than this many seconds ago, count++; otherwise old entry is replaced
$config['lock_time_reset'] = 60;

# max time to store locks in the database; this way we can clear old locks with one function
$config['lock_time_max'] = 60*5;

?>
