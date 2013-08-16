<?php

require_once "RestRequestConfig.php";

/**
 * Class RestRequest
 * @author Ben Wilkins
 * @link http://github.com/benwilkins
 * @package RestRequest
 * @license Free, whatever. Use however you want :) It'd be a nice gesture to leave my name as the author.
 */
class RestRequest extends RestRequestConfig
{
    /**
     * @var string
     */
    protected $endPoint;
    /**
     * @var array
     */
    protected $httpOptions;
    /**
     * @var array
     */
    protected $params;
    /**
     * @var object
     */
    protected $response;

    // -- End fields

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     */
    public function __construct()
    {
        $this->httpOptions = array(
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_FOLLOWLOCATION => FALSE
        );
    }

    /**
     * @param string $endPoint
     * @throws LogicException
     */
    public function setEndPoint($endPoint)
    {
        if (! empty($endPoint)) {
            $this->endPoint = $endPoint;
        }

        if (! isset($this->endPoint)) {
            throw new LogicException("No endpoint was set.");
        }
    }

    /**
     * @param $options
     */
    public function setHttpOptions($options)
    {
        $this->httpOptions = array_merge($this->httpOptions, $options);
    }

    /**
     * @param mixed $params
     */
    public function setParams($params)
    {
        if (! is_array($params)) {
            $params = array($params);
        }

        $this->params = $params;
    }

    /**
     * @param string $endPoint
     * @return object
     */
    public function get($endPoint = '')
    {
        $this->setEndPoint($endPoint);

        if (! empty($this->params)) {
            $this->endPoint .= '?' . http_build_query($this->params);
        }

        $this->sendRequest();

        return $this->response;
    }

    /**
     * @param string $endPoint
     * @return object
     */
    public function delete($endPoint = '')
    {
        $this->setEndPoint($endPoint);
        $this->setHttpOptions(array(CURLOPT_CUSTOMREQUEST => 'DELETE'));
        $this->sendRequest();

        return $this->response;
    }

    /**
     * @param string $endPoint
     * @return object
     */
    public function post($endPoint = '')
    {
        $this->setEndPoint($endPoint);

        $options = array(CURLOPT_POST => TRUE, CURLOPT_POSTFIELDS => $this->params);

        if (! empty($this->params)) {
            if ($this->requestFormat == "JSON") {
                $options[CURLOPT_HTTPHEADER] = array('Content-Type: multipart/form-data');

            } else {
                $options[CURLOPT_HTTPHEADER] = array('Content-Type: multipart/form-data');
            }
        }

        $this->setHttpOptions($options);
        $this->sendRequest();

        return $this->response;
    }

    /**
     * @param string $endPoint
     * @return object
     */
    public function put($endPoint = '')
    {
        $this->setEndPoint($endPoint);

        $options[CURLOPT_CUSTOMREQUEST] = 'PUT';
        $options[CURLOPT_POSTFIELDS] = $this->params;

        $this->setHttpOptions($options);
        $this->sendRequest();

        return $this->response;
    }

    // -- End public methods

    /**
     * @throws RuntimeException
     */
    protected function sendRequest()
    {
        $handle = curl_init($this->baseUrl . $this->endPoint);

        if (! curl_setopt_array($handle, $this->httpOptions)) {
            throw new RuntimeException("Error setting cURL request options");
        }

        $this->response = curl_exec($handle);
        $this->validateResponse();
        curl_close($handle);
    }

    /**
     * @throws RuntimeException
     */
    protected function validateResponse()
    {
        if (! $this->response) {
            throw new RuntimeException(curl_error($this->handle), - 1);
        }

        $response_info = curl_getinfo($this->handle);
        $response_code = $response_info['http_code'];

        if (! in_array($response_code, range(200, 207))) {
            throw new RuntimeException($this->response, $response_code);
        }
    }
}

/* End of file RestRequest.php */