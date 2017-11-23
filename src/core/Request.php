<?php

namespace nadir\core;

/**
 * The class provides the centralized access to the parameters of input request.
 * @author Leonid Selikhov
 */
class Request
{

    /** @var array It contains the raw request body. */
    private $rawBody = null;

    /**
     * The constructor inits the private properties of the object.
     * @return self.
     */
    public function __construct()
    {
        $this->rawBody = @file_get_contents('php://input');
    }

    /**
     * The method returns the server parameter value by the key. It's wrapper over
     * the filter_input() function.
     * @param string $sName Name of a variable to get.
     * @param int $nFilter
     * @param mixed $mOptions Associative array of options or bitwise disjunction
     * of flags. If filter accepts options, flags can be provided in "flags"
     * field of array.
     * @return mixed Value of the requested variable on success, false if the filter
     * fails, or null if the variable is not set.
     */
    public function getServerParam(
        $sName,
        $nFilter = \FILTER_DEFAULT,
        $mOptions = null
    ) {
        // Can be useful if FastCGI has strange side-effects with unexpected null
        // values when using INPUT_SERVER and INPUT_ENV with this function.
        return isset($_SERVER[$sName]) ? filter_var($_SERVER[$sName], $nFilter, $mOptions)
            : null;
    }

    /**
     * The method returns the raw body of the request as string, which was gotten
     * from the input stream.
     * @return string.
     */
    public function getRawBody()
    {
        return $this->rawBody;
    }

    /**
     * The method returns the parameter value of request by the passed key. It's
     * wrapper over the filter_var() function.
     * @param string $sName Name of a variable to get.
     * @param int $nFilter
     * @param mixed $mOptions Associative array of options or bitwise disjunction
     * of flags. If filter accepts options, flags can be provided in "flags"
     * field of array.
     * @return mixed Value of the requested variable on success, false if the filter
     * fails, or null if the variable is not set.
     */
    public function getParam(
        $sName,
        $nFilter = \FILTER_DEFAULT,
        $mOptions = null
    ) {
        // Can be useful when INPUT_REQUEST is implemented for the filter_input()
        // function.
        //return filter_input(\INPUT_REQUEST, $name, $filter, $options);
        return isset($_REQUEST[$sName]) ? filter_var(
            $_REQUEST[$sName],
            $nFilter,
            $mOptions
        ) : null;
    }

    /**
     * It returns the request HTTP-method.
     * @return string|null
     */
    public function getMethod()
    {
        return $this->getServerParam('REQUEST_METHOD', \FILTER_SANITIZE_STRING);
    }

    /**
     * It returns the array of request headers.
     * @return string[]
     */
    public function getAllHeaders()
    {
        return getallheaders();
    }

    /**
     * It returns the header by passed name. The search is case-insensitive.
     * @param string $sName
     * @return string|null
     */
    public function getHeader($sName)
    {
        $sName = strtolower($sName);
        foreach ($this->getAllHeaders() as $sKey => $mValue) {
            if (strtolower($sKey) === $sName) {
                return $mValue;
            }
        }
        return null;
    }

    /**
     * It returns cookie value by name if it exists and matches predefined filter.
     * @param string $sName Cookie name.
     * @return string|false|null
     */
    public function getCookie($sName)
    {
        return filter_input(\INPUT_COOKIE, $sName, \FILTER_SANITIZE_STRING);
    }

    /**
     * The method returns the associated array of cookies.
     * @return mixed[]|null
     */
    public function getAllCookies()
    {
        return filter_input_array(\INPUT_COOKIE, array_combine(
            array_keys($_COOKIE),
            array_fill(0, count($_COOKIE), \FILTER_SANITIZE_STRING)
        ));
    }

    /**
     * It returns the URL path of the request.
     * @return string|null
     */
    public function getUrlPath()
    {
        $sUri = $this->getServerParam('REQUEST_URI', \FILTER_SANITIZE_URL);
        if (!is_null($sUri)) {
            return parse_url($sUri, \PHP_URL_PATH);
        }
        return null;
    }

    /**
     * It checks if the request is an ajax request.
     * @return boolean.
     */
    public function isAjax()
    {
        $sParam = $this->getServerParam('HTTP_X_REQUESTED_WITH', \FILTER_SANITIZE_STRING);
        return !is_null($sParam) && strtolower($sParam) === 'xmlhttprequest';
    }
}
