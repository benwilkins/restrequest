<?php
namespace ParamoreDigital;

/**
 * Class RestRequest
 * @author Ben Wilkins
 * @link http://github.com/benwilkins
 * @package RestRequest
 * @license Free, whatever. Use however you want :) It'd be a nice gesture to leave my name as the author.
 */
class RestRequest
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
    /**
     * @var array
     */
    protected $clientOptions;

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
    public function __construct($options = array())
    {
        $defaultOptions = array(
            'baseUrl' => '',
            'requestFormat' => 'JSON',
            'responseFormat' => 'JSON'
        );
        $this->clientOptions = array_merge($defaultOptions, $options);
        $this->httpOptions = array(
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_FOLLOWLOCATION => FALSE,
            CURLOPT_TIMEOUT        => 10
        );
    }

    /**
     * @param string $endPoint
     * @throws \LogicException
     */
    public function setEndPoint($endPoint)
    {
        if (! empty($endPoint)) {
            $this->endPoint = $endPoint;
        }

        if (! isset($this->endPoint)) {
            throw new \LogicException("No endpoint was set.");
        }
    }

    /**
     * @param $options
     */
    public function setHttpOptions($options)
    {
        $this->httpOptions = $this->httpOptions + $options;
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

        $options = array(CURLOPT_POST => TRUE);

        if (! empty($this->params)) {
            if ($this->clientOptions['requestFormat'] == "JSON") {
                $jsonString                  = json_encode($this->params);
                $options[CURLOPT_POSTFIELDS] = $jsonString;
                $options[CURLOPT_HTTPHEADER] = array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($jsonString)
                );

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
     * @throws \RuntimeException
     */
    protected function sendRequest()
    {
        $handle = curl_init($this->clientOptions['baseUrl'] . $this->endPoint);

        if (! curl_setopt_array($handle, $this->httpOptions)) {
            throw new \RuntimeException("Error setting cURL request options");
        }

        $this->response = curl_exec($handle);
        $this->validateResponse($handle);
        curl_close($handle);
    }

    /**
     * @param $handle
     * @throws \RuntimeException
     */
    protected function validateResponse($handle)
    {
        if (! $this->response) {
            throw new \RuntimeException(curl_error($handle), - 1);
        }

        $response_info = curl_getinfo($handle);
        $response_code = $response_info['http_code'];

        if (curl_errno($handle))
            throw new \RuntimeException(curl_errno($handle));

        if (! in_array($response_code, range(200, 207))) {
            throw new \RuntimeException($this->response, $response_code);
        }
    }
}

/* End of file RestRequest.php */