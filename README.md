# ZfcUserRedirectUri

Install in `modules.config.php`:
~~~
return [
    ..., 'ZfcUser', 'ZfcUserRedirectUri', ...
];
~~~

If you're using ZfcUser's `/user/login?redirect=ROUTE` feature, you may now redirect to any routable URI instead: `ROUTE` becomes `/ROUTABLE/URI/3?message=OK`.

*N.B. URI's that cannot be routed by your app are silently discarded.*
