<?php

class ArrayOfProcessParameters implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * @var ProcessParameters[] $ProcessParameters
     */
    protected $ProcessParameters = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return ProcessParameters[]
     */
    public function getProcessParameters()
    {
      return $this->ProcessParameters;
    }

    /**
     * @param ProcessParameters[] $ProcessParameters
     * @return ArrayOfProcessParameters
     */
    public function setProcessParameters(array $ProcessParameters = null)
    {
      $this->ProcessParameters = $ProcessParameters;
      return $this;
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset An offset to check for
     * @return boolean true on success or false on failure
     */
    public function offsetExists($offset)
    {
      return isset($this->ProcessParameters[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to retrieve
     * @return ProcessParameters
     */
    public function offsetGet($offset)
    {
      return $this->ProcessParameters[$offset];
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to assign the value to
     * @param ProcessParameters $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
      if (!isset($offset)) {
        $this->ProcessParameters[] = $value;
      } else {
        $this->ProcessParameters[$offset] = $value;
      }
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to unset
     * @return void
     */
    public function offsetUnset($offset)
    {
      unset($this->ProcessParameters[$offset]);
    }

    /**
     * Iterator implementation
     *
     * @return ProcessParameters Return the current element
     */
    public function current()
    {
      return current($this->ProcessParameters);
    }

    /**
     * Iterator implementation
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
      next($this->ProcessParameters);
    }

    /**
     * Iterator implementation
     *
     * @return string|null Return the key of the current element or null
     */
    public function key()
    {
      return key($this->ProcessParameters);
    }

    /**
     * Iterator implementation
     *
     * @return boolean Return the validity of the current position
     */
    public function valid()
    {
      return $this->key() !== null;
    }

    /**
     * Iterator implementation
     * Rewind the Iterator to the first element
     *
     * @return void
     */
    public function rewind()
    {
      reset($this->ProcessParameters);
    }

    /**
     * Countable implementation
     *
     * @return ProcessParameters Return count of elements
     */
    public function count()
    {
      return count($this->ProcessParameters);
    }

}
