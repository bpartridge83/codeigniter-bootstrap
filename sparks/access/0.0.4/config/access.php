<?php

# If this is enabled, it will prompt the user for their username for any
#  page access. Useful for restrcting an entire site.
$config['access_site_protection_enabled'] = false;

# The logins that should be valid. It's an associative array of usernames
#  to passwords
$config['access_logins'] = array (
    'bpartridge'  => 'jsbach',
    'jason'  => 'lefkowitz',
    'james'  => 'cramphin',
);

# Set an amount of time to hang before the login appears. Ie,
#  make things a bit difficult to brute force
$config['access_delay'] = 1;

# The realm. Should be somethng like Your Site Name
$config['access_realm'] = "Small Ball Stats";

# Only show the prompt for base URLs that conform to this pattern.
#  This is a standard perl regular expression.
#  Useful for only prompting for a QA site, or even protecting
#  part of a site. This is affected by the settings immediately below,
#  access_force_list_include. If this array is empty the functionality
#  is ignored.
#
# NOTE: Using this list will override anywhere you explicitly call
#  $this->access->prompt()
$config['access_force_list'] = array();

# Set to true if the list above SHOULD be prompted for a password. Set to
# false if they should actually be EXcluded
$config['access_force_list_include'] = false;