<?php

/**
 * @see       https://github.com/laminas/laminas-filter for the canonical source repository
 * @copyright https://github.com/laminas/laminas-filter/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-filter/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Filter;

use Traversable;

class StringToLower extends AbstractUnicode
{
    /**
     * @var array
     */
    protected $options = array(
        'encoding' => null,
    );

    /**
     * Constructor
     *
     * @param string|array|Traversable $encodingOrOptions OPTIONAL
     */
    public function __construct($encodingOrOptions = null)
    {
        if ($encodingOrOptions !== null) {
            if (!static::isOptions($encodingOrOptions)) {
                $this->setEncoding($encodingOrOptions);
            } else {
                $this->setOptions($encodingOrOptions);
            }
        }
    }

    /**
     * Defined by Laminas\Filter\FilterInterface
     *
     * Returns the string $value, converting characters to lowercase as necessary
     *
     * If the value provided is non-scalar, the value will remain unfiltered
     * and an E_USER_WARNING will be raised indicating it's unfilterable.
     *
     * @param  string $value
     * @return string|mixed
     */
    public function filter($value)
    {
        if (null === $value) {
            return null;
        }

        if (!is_scalar($value)) {
            trigger_error(
                sprintf(
                    '%s expects parameter to be scalar, "%s" given; cannot filter',
                    __METHOD__,
                    (is_object($value) ? get_class($value) : gettype($value))
                ),
                E_USER_WARNING
            );
            return $value;
        }

        if ($this->options['encoding'] !== null) {
            return mb_strtolower((string) $value,  $this->options['encoding']);
        }

        return strtolower((string) $value);
    }
}
