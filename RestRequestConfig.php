<?php


/**
 * Class RestRequestConfig
 * @author Ben Wilkins
 * @link http://github.com/benwilkins
 * @package RestRequest
 * @license Free, whatever. Use however you want :) It'd be a nice gesture to leave my name as the author.
 */
class RestRequestConfig
{
    /**
     * @var string
     */
    protected $baseUrl = "";
    /**
     * Valid values: JSON|FORM
     * @var string
     */
    protected $requestFormat = "JSON";
    /**
     * Valid values: JSON|XML|HTML|TEXT
     * @var string
     */
    protected $responseFormat = "JSON";

    // -- End Fields

    /**
     * @param string $requestFormat
     */
    public function setRequestFormat($requestFormat)
    {
        $this->requestFormat = $requestFormat;
    }

    /**
     * @param string $responseFormat
     */
    public function setResponseFormat($responseFormat)
    {
        $this->responseFormat = $responseFormat;
    }
}

/* End of file RestRequestConfig.php */