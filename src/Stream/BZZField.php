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
namespace Arhitector\Djvu\Stream;

use Zerg\Field\Scalar;
use Zerg\StreamInterface;

/**
 * Class BZZField
 *
 * @package Arhitector\Djvu\Stream
 */
class BZZField extends Scalar
{
	
	/**
	 * Read part of data from source and return value in necessary format.
	 *
	 * This is method, so each implementation should return it's own
	 * type of value.
	 *
	 * @param StreamInterface $stream Stream from which read.
	 *
	 * @return int|string|null Value type depend by implementation.
	 */
	public function read(StreamInterface $stream)
	{
		$offset = 1 + 2 + 4 * $this->getDataSet()->getValue('nFiles');
		$bzzStream = $stream->readString($this->getSize() - $offset);
		
		return new BZZStream($bzzStream);
	}
	
}
