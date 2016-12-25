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
use Zerg\Field\Collection;
use Zerg\StreamInterface;

/**
 * Class Dirm
 *
 * @package Arhitector\Djvu\Chunk
 */
class Dirm implements ChunkInterface
{
	use ChunkTrait;
	
	/**
	 * Form constructor.
	 *
	 * @param StreamInterface $stream
	 * @param ChunkInterface  $parent
	 * @param Document        $document
	 *
	 * @throws \Arhitector\Djvu\Exception
	 */
	public function __construct(StreamInterface $stream, ChunkInterface $parent, Document $document)
	{
		$this->setStream($stream);
		
		$structure = new Collection([
			'id'      => ['string', 4],
			'length'  => ['int', 4]
		]);
		
		$structure = $structure->parse($this->getStream());
		
		$this->setOffset($this->getStream()->getPosition());
		$this->setLength($structure['length']);
		
		$this->getStream()->skip($structure['length']);
	}
	
}
