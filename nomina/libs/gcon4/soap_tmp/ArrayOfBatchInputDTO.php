<?php

class ArrayOfBatchInputDTO implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * @var BatchInputDTO[] $BatchInputDTO
     */
    protected $BatchInputDTO = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return BatchInputDTO[]
     */
    public function getBatchInputDTO()
    {
      return $this->BatchInputDTO;
    }

    /**
     * @param BatchInputDTO[] $BatchInputDTO
     * @return ArrayOfBatchInputDTO
     */
    public function setBatchInputDTO(array $BatchInputDTO = null)
    {
      $this->BatchInputDTO = $BatchInputDTO;
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
      return isset($this->BatchInputDTO[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to retrieve
     * @return BatchInputDTO
     */
    public function offsetGet($offset)
    {
      return $this->BatchInputDTO[$offset];
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to assign the value to
     * @param BatchInputDTO $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
      if (!isset($offset)) {
        $this->BatchInputDTO[] = $value;
      } else {
        $this->BatchInputDTO[$offset] = $value;
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
      unset($this->BatchInputDTO[$offset]);
    }

    /**
     * Iterator implementation
     *
     * @return BatchInputDTO Return the current element
     */
    public function current()
    {
      return current($this->BatchInputDTO);
    }

    /**
     * Iterator implementation
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
      next($this->BatchInputDTO);
    }

    /**
     * Iterator implementation
     *
     * @return string|null Return the key of the current element or null
     */
    public function key()
    {
      return key($this->BatchInputDTO);
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
      reset($this->BatchInputDTO);
    }

    /**
     * Countable implementation
     *
     * @return BatchInputDTO Return count of elements
     */
    public function count()
    {
      return count($this->BatchInputDTO);
    }

}
