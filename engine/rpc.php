<?php
require_once('vendor/autoload.php');

if (!PRODUCTION)
{
  error_reporting(E_ALL);
  ini_set('display_errors','On');
}

function phoxy_conf()
{
  $ret = phoxy_default_conf();
  $ret["api_xss_prevent"] = PRODUCTION;

  return $ret;
}

function default_addons()
{
  $ret =
  [
    "cache" => PRODUCTION ? ['global' => '10m'] : "no",
    "result" => "canvas",
  ];
  return $ret;
}

include('phoxy/phoxy_return_worker.php');
phoxy_return_worker::$add_hook_cb = function($that)
{
  global $USER_SENSITIVE;

  if ($USER_SENSITIVE)
    $that->obj['cache'] = 'no';
};

error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE);
//phpsql\OneLineConfig(conf()->db->connection_string);

try
{
  include('phoxy/load.php');
} catch (Exception $e)
{
  $message = ["error" => $e->getMessage()];
  die (json_encode($message, true));
}
