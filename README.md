RestRequest
===========

A PHP class for submitting REST requests via cURL.

## Configuration
Open the RestRequestConfig.php class and set the $baseUrl parameter to be the base URL of the API. Here, you can also set a default format for the request body (JSON or Form-encoded).

## Usage

You can set cURL options like this:

`$request->setHttpOptions(array(CURLOPT_HTTPAUTH => CURLAUTH_BASIC, CURLOPT_USERPWD => 'username:password'));`

You can set parameters like this:

`$request->setParams(array('firstName' => 'Ben', 'lastName' => 'Wilkins'));`

You can even set the format for sending params, either JSON or Form-encoded:
`$request->setRequestFormat('JSON');`

You can manually set an endpoint:

    $request->setEndPoint('/some/endpoint');
    $request->post();

Or, you can pass the endpoint straight into the action method:

`$request->post('/some/endpoint');`