<?php

/*
 | --------------------------------------------------------------------
 | App Namespace
 | --------------------------------------------------------------------
 |
 | This defines the default Namespace that is used throughout
 | CodeIgniter to refer to the Application directory. Change
 | this constant to change the namespace that all application
 | classes should use.
 |
 | NOTE: changing this will require manually modifying the
 | existing namespaces of App\* namespaced-classes.
 */
defined('APP_NAMESPACE') || define('APP_NAMESPACE', 'App');

/*
 | --------------------------------------------------------------------------
 | Composer Path
 | --------------------------------------------------------------------------
 |
 | The path that Composer's autoload file is expected to live. By default,
 | the vendor folder is in the Root directory, but you can customize that here.
 */
defined('COMPOSER_PATH') || define('COMPOSER_PATH', ROOTPATH . 'vendor/autoload.php');

/*
 |--------------------------------------------------------------------------
 | Timing Constants
 |--------------------------------------------------------------------------
 |
 | Provide simple ways to work with the myriad of PHP functions that
 | require information to be in seconds.
 */
defined('SECOND') || define('SECOND', 1);
defined('MINUTE') || define('MINUTE', 60);
defined('HOUR')   || define('HOUR', 3600);
defined('DAY')    || define('DAY', 86400);
defined('WEEK')   || define('WEEK', 604800);
defined('MONTH')  || define('MONTH', 2_592_000);
defined('YEAR')   || define('YEAR', 31_536_000);
defined('DECADE') || define('DECADE', 315_360_000);

/*
 | --------------------------------------------------------------------------
 | Exit Status Codes
 | --------------------------------------------------------------------------
 |
 | Used to indicate the conditions under which the script is exit()ing.
 | While there is no universal standard for error codes, there are some
 | broad conventions.  Three such conventions are mentioned below, for
 | those who wish to make use of them.  The CodeIgniter defaults were
 | chosen for the least overlap with these conventions, while still
 | leaving room for others to be defined in future versions and user
 | applications.
 |
 | The three main conventions used for determining exit status codes
 | are as follows:
 |
 |    Standard C/C++ Library (stdlibc):
 |       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
 |       (This link also contains other GNU-specific conventions)
 |    BSD sysexits.h:
 |       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
 |    Bash scripting:
 |       http://tldp.org/LDP/abs/html/exitcodes.html
 |
 */
defined('EXIT_SUCCESS')        || define('EXIT_SUCCESS', 0);        // no errors
defined('EXIT_ERROR')          || define('EXIT_ERROR', 1);          // generic error
defined('EXIT_CONFIG')         || define('EXIT_CONFIG', 3);         // configuration error
defined('EXIT_UNKNOWN_FILE')   || define('EXIT_UNKNOWN_FILE', 4);   // file not found
defined('EXIT_UNKNOWN_CLASS')  || define('EXIT_UNKNOWN_CLASS', 5);  // unknown class
defined('EXIT_UNKNOWN_METHOD') || define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     || define('EXIT_USER_INPUT', 7);     // invalid user input
defined('EXIT_DATABASE')       || define('EXIT_DATABASE', 8);       // database error
defined('EXIT__AUTO_MIN')      || define('EXIT__AUTO_MIN', 9);      // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      || define('EXIT__AUTO_MAX', 125);    // highest automatically-assigned error code

/**
 * @deprecated Use \CodeIgniter\Events\Events::PRIORITY_LOW instead.
 */
define('EVENT_PRIORITY_LOW', 200);

/**
 * @deprecated Use \CodeIgniter\Events\Events::PRIORITY_NORMAL instead.
 */
define('EVENT_PRIORITY_NORMAL', 100);

/**
 * @deprecated Use \CodeIgniter\Events\Events::PRIORITY_HIGH instead.
 */
define('EVENT_PRIORITY_HIGH',10);


defined('INITIAL_CASE_NUMBER_SUFFIX') ||  define('INITIAL_CASE_NUMBER_SUFFIX', 400000);
defined('INCREMENT_FOR_NEW_YEAR',) ||  define('INCREMENT_FOR_NEW_YEAR', 1);
//Response code status 
/**
     * Constants for status codes.
     * From  https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
     */
    // Informational
    defined('HTTP_CONTINUE')                        || define('HTTP_CONTINUE', 100);
    defined('HTTP_SWITCHING_PROTOCOLS')             || define('HTTP_SWITCHING_PROTOCOLS', 101);
    defined('HTTP_PROCESSING')                      || define('HTTP_PROCESSING', 102);
    defined('HTTP_EARLY_HINTS')                     || define('HTTP_EARLY_HINTS', 103);
    defined('HTTP_OK')                              || define('HTTP_OK', 200);
    defined('HTTP_CREATED')                         || define('HTTP_CREATED', 201);
    defined('HTTP_ACCEPTED')                        || define('HTTP_ACCEPTED', 202);
    defined('HTTP_NONAUTHORITATIVE_INFORMATION')    || define('HTTP_NONAUTHORITATIVE_INFORMATION', 203);
    defined('HTTP_NO_CONTENT')                      || define('HTTP_NO_CONTENT', 204);
    defined('HTTP_RESET_CONTENT')                   || define('HTTP_RESET_CONTENT', 205);
    defined('HTTP_PARTIAL_CONTENT')                 || define('HTTP_PARTIAL_CONTENT', 206);
    defined('HTTP_MULTI_STATUS')                    || define('HTTP_MULTI_STATUS', 207);
    defined('HTTP_ALREADY_REPORTED')                || define('HTTP_ALREADY_REPORTED', 208);
    defined('HTTP_IM_USED')                         || define('HTTP_IM_USED', 226);
    defined('HTTP_MULTIPLE_CHOICES')                || define('HTTP_MULTIPLE_CHOICES', 300);
    defined('HTTP_MOVED_PERMANENTLY')               || define('HTTP_MOVED_PERMANENTLY', 301);
    defined('HTTP_FOUND')                           || define('HTTP_FOUND', 302);
    defined('HTTP_SEE_OTHER')                       || define('HTTP_SEE_OTHER', 303);
    defined('HTTP_NOT_MODIFIED')                    || define('HTTP_NOT_MODIFIED', 304);
    defined('HTTP_USE_PROXY')                       || define('HTTP_USE_PROXY', 305);
    defined('HTTP_SWITCH_PROXY')                    || define('HTTP_SWITCH_PROXY', 306);
    defined('HTTP_TEMPORARY_REDIRECT')              || define('HTTP_TEMPORARY_REDIRECT', 307);
    defined('HTTP_PERMANENT_REDIRECT')              || define('HTTP_PERMANENT_REDIRECT', 308);
    defined('HTTP_BAD_REQUEST')                     || define('HTTP_BAD_REQUEST', 400);
    defined('HTTP_UNAUTHORIZED')                    || define('HTTP_UNAUTHORIZED', 401);
    defined('HTTP_PAYMENT_REQUIRED')                || define('HTTP_PAYMENT_REQUIRED', 402);
    defined('HTTP_FORBIDDEN')                       || define('HTTP_FORBIDDEN', 403);
    defined('HTTP_NOT_FOUND')                       || define('HTTP_NOT_FOUND', 404);
    defined('HTTP_METHOD_NOT_ALLOWED')              || define('HTTP_METHOD_NOT_ALLOWED', 405);
    defined('HTTP_NOT_ACCEPTABLE')                  || define('HTTP_NOT_ACCEPTABLE', 406);
    defined('HTTP_PROXY_AUTHENTICATION_REQUIRED')   || define('HTTP_PROXY_AUTHENTICATION_REQUIRED', 407);
    defined('HTTP_REQUEST_TIMEOUT')                 || define('HTTP_REQUEST_TIMEOUT', 408);
    defined('HTTP_CONFLICT')                        || define('HTTP_CONFLICT', 409);
    defined('HTTP_GONE')                            || define('HTTP_GONE', 410);
    defined('HTTP_LENGTH_REQUIRED')                 || define('HTTP_LENGTH_REQUIRED', 411);
    defined('HTTP_PRECONDITION_FAILED')             || define('HTTP_PRECONDITION_FAILED', 412);
    defined('HTTP_PAYLOAD_TOO_LARGE')               || define('HTTP_PAYLOAD_TOO_LARGE', 413);
    defined('HTTP_URI_TOO_LONG')                    || define('HTTP_URI_TOO_LONG', 414);
    defined('HTTP_UNSUPPORTED_MEDIA_TYPE')          || define('HTTP_UNSUPPORTED_MEDIA_TYPE', 415);
    defined('HTTP_RANGE_NOT_SATISFIABLE')           || define('HTTP_RANGE_NOT_SATISFIABLE', 416);
    defined('HTTP_EXPECTATION_FAILED')              || define('HTTP_EXPECTATION_FAILED', 417);
    defined('HTTP_IM_A_TEAPOT')                     || define('HTTP_IM_A_TEAPOT', 418);
    defined('HTTP_MISDIRECTED_REQUEST')             || define('HTTP_MISDIRECTED_REQUEST', 421);
    defined('HTTP_UNPROCESSABLE_ENTITY')            || define('HTTP_UNPROCESSABLE_ENTITY', 422);
    defined('HTTP_LOCKED')                          || define('HTTP_LOCKED', 423);
    defined('HTTP_FAILED_DEPENDENCY')               || define('HTTP_FAILED_DEPENDENCY', 424);
    defined('HTTP_TOO_EARLY')                       || define('HTTP_TOO_EARLY', 425);
    defined('HTTP_UPGRADE_REQUIRED')                || define('HTTP_UPGRADE_REQUIRED', 426);
    defined('HTTP_PRECONDITION_REQUIRED')           || define('HTTP_PRECONDITION_REQUIRED', 428);
    defined('HTTP_TOO_MANY_REQUESTS')               || define('HTTP_TOO_MANY_REQUESTS', 429);
    defined('HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE') || define('HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE', 431);
    defined('HTTP_UNAVAILABLE_FOR_LEGAL_REASONS')   || define('HTTP_UNAVAILABLE_FOR_LEGAL_REASONS', 451);
    defined('HTTP_CLIENT_CLOSED_REQUEST')           || define('HTTP_CLIENT_CLOSED_REQUEST', 499);
    defined('HTTP_INTERNAL_SERVER_ERROR')           || define('HTTP_INTERNAL_SERVER_ERROR', 500);
    defined('HTTP_NOT_IMPLEMENTED')                 || define('HTTP_NOT_IMPLEMENTED', 501);
    defined('HTTP_BAD_GATEWAY')                     || define('HTTP_BAD_GATEWAY', 502);
    defined('HTTP_SERVICE_UNAVAILABLE')             || define('HTTP_SERVICE_UNAVAILABLE', 503);
    defined('HTTP_GATEWAY_TIMEOUT')                 || define('HTTP_GATEWAY_TIMEOUT', 504);
    defined('HTTP_HTTP_VERSION_NOT_SUPPORTED')      || define('HTTP_HTTP_VERSION_NOT_SUPPORTED', 505);
    defined('HTTP_VARIANT_ALSO_NEGOTIATES')         || define('HTTP_VARIANT_ALSO_NEGOTIATES', 506);
    defined('HTTP_INSUFFICIENT_STORAGE')            || define('HTTP_INSUFFICIENT_STORAGE', 507);
    defined('HTTP_LOOP_DETECTED')                   || define('HTTP_LOOP_DETECTED', 508);
    defined('HTTP_NOT_EXTENDED')                    || define('HTTP_NOT_EXTENDED', 510);
    defined('HTTP_NETWORK_AUTHENTICATION_REQUIRED') || define('HTTP_NETWORK_AUTHENTICATION_REQUIRED', 511);
    defined('HTTP_NETWORK_CONNECT_TIMEOUT_ERROR')   || define('HTTP_NETWORK_CONNECT_TIMEOUT_ERROR', 599);
