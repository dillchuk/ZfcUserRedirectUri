# ZfcUserRedirectUri

[![Build Status](https://travis-ci.org/dillchuk/ZfcUserRedirectUri.svg?branch=master)](https://travis-ci.org/dillchuk/ZfcUserRedirectUri)

Install in `modules.config.php`:
~~~
return [
    ..., 'ZfcUser', 'ZfcUserRedirectUri', ...
];
~~~

If you're using ZfcUser's `/user/login?redirect=ROUTE` feature, you may now redirect to any routable URI instead: `ROUTE` can become for example `/ROUTABLE/URI/3?message=OK`.

This is especially useful when using ZfcRbac and user is hit with the login screen; they log in then carry on right where they left off.

ZfcRbac config as follows:
~~~
'redirect_strategy' => [
	'redirect_when_connected' => true,
	'append_previous_uri' => true,
	'previous_uri_query_key' => 'redirect'
],
~~~

*N.B. URI's that cannot be routed by your app are silently discarded.*
