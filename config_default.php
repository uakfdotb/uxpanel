<?php

### ** server information

# a unique name for this uxpanel instance
$config['site_name'] = "UXPanel Unknown Site";

# format to use for displaying times
$config['format_time'] = "j M Y H:i:s T";

# format to use for displaying dates
$config['format_date'] = "j M Y";

# path where all of the service files and such are kept
$config['root_path'] = "/uxpanel/";

### ** GHost settings

# path to GHost on server, with trailing slash
$config['ghost_path'] = $config['root_path'] . "ghost/";

# password to use for GHost users
$config['ghost_password'] = "abc";

# additional GHost parameters in ghost.cfg
#$config['ghostParameters'] = array();
#$config['bnetParameters'] = array();

# additional GHost parameters in default.cfg
#$config['defaultParameters'] = array();

# additional GHost editable files
#$config['updatableFiles'] = array();

### ** channel settings

# path to channel on server, with trailing slash
$config['channel_path'] = $config['root_path'] . "channel/";

# additional channel parameters in chop.cfg
#$config['channelParameters'] = array();
#$config['defaultChannelParameters'] = array();

# additional pychop plugins
#$config['channelPlugins'] = array();

# additional pychop editable files
#$config['channelUpdatableFiles'] = array();

### ** Minecraft settings

# path to Minecraft on server, with trailing slash
$config['minecraft_path'] = $config['root_path'] . "minecraft/";

### ** database settings

# path to the cron file, if it is being used
$config['cron_path'] = "/cron.php";

#additional cron parameters
#$config['cronParameters'] = array();

### ** email information

# email address to send mail from
$config['mail_from'] = "uxpanel@example.com";

# name to use when sending mail
$config['mail_fromname'] = "UXPanel";

### ** account information

# admin account username
$config['admin_username'] = "admin";

# admin account password
# see admin_passwordformat for how to securely add a password here
$config['admin_password'] = "";

# this is the format of the admin password
#  plain: plaintext; admin_password simply contains the desired password
#  hash: hashed with the sha512 algorithm
#   use the chash function in common.php, or mkpasswd.php
#  pbkdf2: hashed using PBKDF2 standard
#   use the pbkdf2_create_hash function in pbkdf2.php, or mkpasswd.php
$config['admin_passwordformat'] = "plain";

### ** database information

# host
$config['db_hostname'] = "localhost";

# database name
$config['db_name'] = "uxpanel";

# username
$config['db_username'] = "root";

# password
$config['db_password'] = "";

### ** slave settings

# whether this is a slave uxpanel instance
# this allows remote logins
$config['slave_enabled'] = false;

# the master uxpanel base directory, with trailing slash
# users will be redirected here when they view anything other than the service
$config['slave_master'] = "http://uxpanel.example.com/";

# an id string or number for this slave instance
# this is only used if slave_enabled = true
$config['slave_id'] = 0;

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
