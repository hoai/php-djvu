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

use Arhitector\Djvu\Document;
use Zerg\StreamInterface;

/**
 * Class Djvu
 *
 * @package Arhitector\Djvu\Chunk
 */
class Djvu implements ChunkInterface
{
	use ChunkTrait;
	
	/**
	 * Djvu constructor.
	 *
	 * @param StreamInterface $stream
	 * @param ChunkInterface  $parent
	 * @param Document        $document
	 */
	public function __construct(StreamInterface $stream, ChunkInterface $parent, Document $document)
	{
		$this->setStream($stream);
		$this->getStream()->readString(4);
		$this->setOffset($this->getStream()->getPosition());
		$this->setLength($parent->getLength() - 4);
	}
	
}
