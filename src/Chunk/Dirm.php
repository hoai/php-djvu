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
use Arhitector\Djvu\Stream\BZZField;
use Zerg\DataSet;
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
	 * @var bool
	 */
	protected $isBundled;
	
	/**
	 * @var int
	 */
	protected $version = 0;
	
	/**
	 * @var int
	 */
	protected $files = 0;
	
	/**
	 * @var int[]
	 */
	protected $offsets = [];
	
	/**
	 * @var string[]
	 */
	protected $names = [];
	
	/**
	 * @var int[]
	 */
	protected $flags = [];
	
	/**
	 * @var int[]
	 */
	protected $sizes = [];
	
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
			'length'  => ['int', 4],
			'flags'   => [
				'int',
				1,
				[
					'formatter' => function ($value) {
						return [
							'is_bundled' => ($value >> 7) == 1, // B[7]
							'version'    => $value & 127 // B[6..0]
						];
					}
				]
			],
			'nFiles'  => ['int', 2],
			'offsets' => [
				'conditional',
				'/flags/is_bundled',
				[
					true => ['arr', '/nFiles', ['int', 4]]
				],
				[
					'default' => ['string', 0]
				]
			],
			'encoded' => new BZZField('/length', [
				'formatter' => function (StreamInterface $stream, DataSet $dataSet) {
					return (new Collection([
							'sizes' => ['arr', $dataSet->getValue('nFiles'), ['int', 3]],
							'flags' => ['arr', $dataSet->getValue('nFiles'), ['int', 1]]
						]))
						       ->parse($stream) + ['stream' => $stream];
				}
			])
		]);
		
		$structure = $structure->parse($this->getStream());
		
		for ($i = 0; $i < $structure['nFiles']; $i++)
		{
			$buffer = '';
			
			while (($byte = $structure['encoded']['stream']->read(1)) !== "\0" || empty($buffer))
			{
				$buffer .= $byte;
			}
			
			$this->names[] = ltrim($buffer);
		}
		
		$this->isBundled = $structure['flags']['is_bundled'];
		$this->version = $structure['flags']['version'];
		$this->files = $structure['nFiles'];
		$this->offsets = $structure['offsets'];
		$this->sizes = $structure['encoded']['sizes'];
		$this->flags = $structure['encoded']['flags'];
		
		$this->setOffset($this->getStream()->getPosition());
		$this->setLength($structure['length']);
	}
	
}
