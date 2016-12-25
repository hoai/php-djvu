<?php
/**
 * This file is part of the arhitector/php-djvu library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 *
 * @license   http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2016 Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 */
namespace Arhitector\Djvu\Chunk;

use InvalidArgumentException;

/**
 * Class ChunkTrait
 *
 * @package Chunk
 */
trait ChunkTrait
{
	
	/**
	 * @var int The length of the chunk data.
	 */
	protected $length = 0;
	
	/**
	 * @var int The offset to the start of the chunk data.
	 */
	protected $offset = 0;
	
	/**
	 * @var string The chunk identifier.
	 */
	protected $name;
	
	/**
	 * Gets the length of the chunk data.
	 *
	 * @return int
	 */
	public function getLength()
	{
		return $this->length;
	}
	
	/**
	 * Gets the offset to the start of the chunk data.
	 *
	 * @return int
	 */
	public function getOffset()
	{
		return $this->offset;
	}
	
	/**
	 * Gets the chunk identifier.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * Sets the chunk identifier.
	 *
	 * @param $name
	 *
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	protected function setName($name)
	{
		if ( ! is_string($name))
		{
			throw new InvalidArgumentException('The name value must be a string type.');
		}
		
		$this->name = $name;
		
		return $this;
	}
	
	/**
	 * Sets the length value.
	 *
	 * @param int $length
	 *
	 * @return $this
	 */
	protected function setLength($length)
	{
		$this->length = (int) $length;
		
		return $this;
	}
	
	/**
	 * Sets the offset value.
	 *
	 * @param int $offset
	 *
	 * @return $this
	 */
	protected function setOffset($offset)
	{
		$this->offset = (int) $offset;
		
		return $this;
	}
	
}
