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

use Zerg\Buffer\BufferInterface;
use Zerg\Endian;
use Zerg\StringStream;
use Zerg\StreamInterface;

/**
 * Class BZZStream
 *
 * @package Arhitector\Djvu\Stream
 */
class BZZStream extends StringStream implements StreamInterface
{
	
	/**
	 * Return new file stream that will read data form given file.
	 *
	 * @param string              $string
	 * @param int                 $endian
	 * @param int|BufferInterface $buffer Buffer type.
	 *
	 * @throws \Zerg\Exception
	 * @throws \InvalidArgumentException
	 * @throws \Arhitector\Djvu\Exception
	 */
	public function __construct($string, $endian = Endian::ENDIAN_BIG, $buffer = self::BUFFER_BYTE)
	{
		parent::__construct('', $endian, $buffer);
	}
	
}
