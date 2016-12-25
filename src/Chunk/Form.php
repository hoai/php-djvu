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
use Arhitector\Djvu\Exception;
use Zerg\Field\Collection;
use Zerg\Field\InvalidKeyException;
use Zerg\StreamInterface;

/**
 * Class Form
 *
 * @package Arhitector\Djvu\Chunk
 */
class Form implements ChunkInterface
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
	public function __construct(StreamInterface $stream, ChunkInterface $parent = null, Document $document)
	{
		$this->setStream($stream);
		
		$structure = new Collection([
			'id'     => ['string', 4],
			'length' => [
				'conditional',
				'/id',
				[
					'FORM' => ['int', 4]
				]
			]
		]);
		
		try
		{
			$structure = $structure->parse($this->getStream());
		}
		catch (InvalidKeyException $exc)
		{
			throw new Exception('The chunk is damaged.', 0, $exc);
		}
		
		$this->setOffset($this->getStream()->getPosition());
		$this->setLength($structure['length']);
	}
	
}
