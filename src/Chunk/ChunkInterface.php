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

/**
 * Interface ChunkInterface
 *
 * @package Arhitector\Djvu\Chunk
 */
interface ChunkInterface
{
	
	/**
	 * Gets the length of the chunk data.
	 *
	 * @return int
	 */
	public function getLength();
	
	/**
	 * Gets the offset to the start of the chunk data.
	 *
	 * @return int
	 */
	public function getOffset();
	
	/**
	 * Gets the chunk identifier.
	 *
	 * @return string
	 */
	public function getName();
	
}
