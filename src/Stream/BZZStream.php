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

use Exception;
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
	
	const FREQ_MAX = 4;
	
	const CTX_IDS = 3;
	
	const MAX_BLOCK = 4096;
	
	/**
	 * @var StreamInterface
	 */
	protected $rawStream;
	
	/**
	 * @var int[] Machine independent ffz table.
	 */
	protected $ffzt = [];
	
	/**
	 * @var int
	 */
	protected $code = 0xFFFF;
	
	/**
	 * @var int
	 */
	protected $fence = 0;
	
	/**
	 * @var int
	 */
	protected $scount = 0;
	
	/**
	 * @var int
	 */
	protected $byte;
	
	/**
	 * @var int
	 */
	protected $delay = 0;
	
	/**
	 * @var int
	 */
	protected $a = 0;
	
	/**
	 * @var int[]
	 */
	protected $mtf;
	
	/**
	 * @var int[]
	 */
	protected $ctx;
	
	/**
	 * @var int
	 */
	protected $size = 0;
	
	/**
	 * @var int
	 */
	protected $block_size = 0;
	
	/**
	 * @var int[]
	 */
	protected $p = [
		0x8000,
		0x8000,
		0x8000,
		0x6bbd,
		0x6bbd,
		0x5d45,
		0x5d45,
		0x51b9,
		0x51b9,
		0x4813,
		0x4813,
		0x3fd5,
		0x3fd5,
		0x38b1,
		0x38b1,
		0x3275,
		0x3275,
		0x2cfd,
		0x2cfd,
		0x2825,
		0x2825,
		0x23ab,
		0x23ab,
		0x1f87,
		0x1f87,
		0x1bbb,
		0x1bbb,
		0x1845,
		0x1845,
		0x1523,
		0x1523,
		0x1253,
		0x1253,
		0x0fcf,
		0x0fcf,
		0x0d95,
		0x0d95,
		0x0b9d,
		0x0b9d,
		0x09e3,
		0x09e3,
		0x0861,
		0x0861,
		0x0711,
		0x0711,
		0x05f1,
		0x05f1,
		0x04f9,
		0x04f9,
		0x0425,
		0x0425,
		0x0371,
		0x0371,
		0x02d9,
		0x02d9,
		0x0259,
		0x0259,
		0x01ed,
		0x01ed,
		0x0193,
		0x0193,
		0x0149,
		0x0149,
		0x010b,
		0x010b,
		0x00d5,
		0x00d5,
		0x00a5,
		0x00a5,
		0x007b,
		0x007b,
		0x0057,
		0x0057,
		0x003b,
		0x003b,
		0x0023,
		0x0023,
		0x0013,
		0x0013,
		0x0007,
		0x0007,
		0x0001,
		0x0001,
		0x5695,
		0x24ee,
		0x8000,
		0x0d30,
		0x481a,
		0x0481,
		0x3579,
		0x017a,
		0x24ef,
		0x007b,
		0x1978,
		0x0028,
		0x10ca,
		0x000d,
		0x0b5d,
		0x0034,
		0x078a,
		0x00a0,
		0x050f,
		0x0117,
		0x0358,
		0x01ea,
		0x0234,
		0x0144,
		0x0173,
		0x0234,
		0x00f5,
		0x0353,
		0x00a1,
		0x05c5,
		0x011a,
		0x03cf,
		0x01aa,
		0x0285,
		0x0286,
		0x01ab,
		0x03d3,
		0x011a,
		0x05c5,
		0x00ba,
		0x08ad,
		0x007a,
		0x0ccc,
		0x01eb,
		0x1302,
		0x02e6,
		0x1b81,
		0x045e,
		0x24ef,
		0x0690,
		0x2865,
		0x09de,
		0x3987,
		0x0dc8,
		0x2c99,
		0x10ca,
		0x3b5f,
		0x0b5d,
		0x5695,
		0x078a,
		0x8000,
		0x050f,
		0x24ee,
		0x0358,
		0x0d30,
		0x0234,
		0x0481,
		0x0173,
		0x017a,
		0x00f5,
		0x007b,
		0x00a1,
		0x0028,
		0x011a,
		0x000d,
		0x01aa,
		0x0034,
		0x0286,
		0x00a0,
		0x03d3,
		0x0117,
		0x05c5,
		0x01ea,
		0x08ad,
		0x0144,
		0x0ccc,
		0x0234,
		0x1302,
		0x0353,
		0x1b81,
		0x05c5,
		0x24ef,
		0x03cf,
		0x2b74,
		0x0285,
		0x201d,
		0x01ab,
		0x1715,
		0x011a,
		0x0fb7,
		0x00ba,
		0x0a67,
		0x01eb,
		0x06e7,
		0x02e6,
		0x0496,
		0x045e,
		0x030d,
		0x0690,
		0x0206,
		0x09de,
		0x0155,
		0x0dc8,
		0x00e1,
		0x2b74,
		0x0094,
		0x201d,
		0x0188,
		0x1715,
		0x0252,
		0x0fb7,
		0x0383,
		0x0a67,
		0x0547,
		0x06e7,
		0x07e2,
		0x0496,
		0x0bc0,
		0x030d,
		0x1178,
		0x0206,
		0x19da,
		0x0155,
		0x24ef,
		0x00e1,
		0x320e,
		0x0094,
		0x432a,
		0x0188,
		0x447d,
		0x0252,
		0x5ece,
		0x0383,
		0x8000,
		0x0547,
		0x481a,
		0x07e2,
		0x3579,
		0x0bc0,
		0x24ef,
		0x1178,
		0x1978,
		0x19da,
		0x2865,
		0x24ef,
		0x3987,
		0x320e,
		0x2c99,
		0x432a,
		0x3b5f,
		0x447d,
		0x5695,
		0x5ece,
		0x8000,
		0x8000,
		0x5695,
		0x481a,
		0x481a
	];
	
	/**
	 * @var int[]
	 */
	protected $m = [
		0x0000,
		0x0000,
		0x0000,
		0x10a5,
		0x10a5,
		0x1f28,
		0x1f28,
		0x2bd3,
		0x2bd3,
		0x36e3,
		0x36e3,
		0x408c,
		0x408c,
		0x48fd,
		0x48fd,
		0x505d,
		0x505d,
		0x56d0,
		0x56d0,
		0x5c71,
		0x5c71,
		0x615b,
		0x615b,
		0x65a5,
		0x65a5,
		0x6962,
		0x6962,
		0x6ca2,
		0x6ca2,
		0x6f74,
		0x6f74,
		0x71e6,
		0x71e6,
		0x7404,
		0x7404,
		0x75d6,
		0x75d6,
		0x7768,
		0x7768,
		0x78c2,
		0x78c2,
		0x79ea,
		0x79ea,
		0x7ae7,
		0x7ae7,
		0x7bbe,
		0x7bbe,
		0x7c75,
		0x7c75,
		0x7d0f,
		0x7d0f,
		0x7d91,
		0x7d91,
		0x7dfe,
		0x7dfe,
		0x7e5a,
		0x7e5a,
		0x7ea6,
		0x7ea6,
		0x7ee6,
		0x7ee6,
		0x7f1a,
		0x7f1a,
		0x7f45,
		0x7f45,
		0x7f6b,
		0x7f6b,
		0x7f8d,
		0x7f8d,
		0x7faa,
		0x7faa,
		0x7fc3,
		0x7fc3,
		0x7fd7,
		0x7fd7,
		0x7fe7,
		0x7fe7,
		0x7ff2,
		0x7ff2,
		0x7ffa,
		0x7ffa,
		0x7fff,
		0x7fff,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000,
		0x0000
	];
	
	/**
	 * @var int[]
	 */
	protected $up = [
		84,
		3,
		4,
		5,
		6,
		7,
		8,
		9,
		10,
		11,
		12,
		13,
		14,
		15,
		16,
		17,
		18,
		19,
		20,
		21,
		22,
		23,
		24,
		25,
		26,
		27,
		28,
		29,
		30,
		31,
		32,
		33,
		34,
		35,
		36,
		37,
		38,
		39,
		40,
		41,
		42,
		43,
		44,
		45,
		46,
		47,
		48,
		49,
		50,
		51,
		52,
		53,
		54,
		55,
		56,
		57,
		58,
		59,
		60,
		61,
		62,
		63,
		64,
		65,
		66,
		67,
		68,
		69,
		70,
		71,
		72,
		73,
		74,
		75,
		76,
		77,
		78,
		79,
		80,
		81,
		82,
		81,
		82,
		9,
		86,
		5,
		88,
		89,
		90,
		91,
		92,
		93,
		94,
		95,
		96,
		97,
		82,
		99,
		76,
		101,
		70,
		103,
		66,
		105,
		106,
		107,
		66,
		109,
		60,
		111,
		56,
		69,
		114,
		65,
		116,
		61,
		118,
		57,
		120,
		53,
		122,
		49,
		124,
		43,
		72,
		39,
		60,
		33,
		56,
		29,
		52,
		23,
		48,
		23,
		42,
		137,
		38,
		21,
		140,
		15,
		142,
		9,
		144,
		141,
		146,
		147,
		148,
		149,
		150,
		151,
		152,
		153,
		154,
		155,
		70,
		157,
		66,
		81,
		62,
		75,
		58,
		69,
		54,
		65,
		50,
		167,
		44,
		65,
		40,
		59,
		34,
		55,
		30,
		175,
		24,
		177,
		178,
		179,
		180,
		181,
		182,
		183,
		184,
		69,
		186,
		59,
		188,
		55,
		190,
		51,
		192,
		47,
		194,
		41,
		196,
		37,
		198,
		199,
		72,
		201,
		62,
		203,
		58,
		205,
		54,
		207,
		50,
		209,
		46,
		211,
		40,
		213,
		36,
		215,
		30,
		217,
		26,
		219,
		20,
		71,
		14,
		61,
		14,
		57,
		8,
		53,
		228,
		49,
		230,
		45,
		232,
		39,
		234,
		35,
		138,
		29,
		24,
		25,
		240,
		19,
		22,
		13,
		16,
		13,
		10,
		7,
		244,
		249,
		10,
		89,
		230
	];
	
	/**
	 * @var int[]
	 */
	protected $dn = [
		145,
		4,
		3,
		1,
		2,
		3,
		4,
		5,
		6,
		7,
		8,
		9,
		10,
		11,
		12,
		13,
		14,
		15,
		16,
		17,
		18,
		19,
		20,
		21,
		22,
		23,
		24,
		25,
		26,
		27,
		28,
		29,
		30,
		31,
		32,
		33,
		34,
		35,
		36,
		37,
		38,
		39,
		40,
		41,
		42,
		43,
		44,
		45,
		46,
		47,
		48,
		49,
		50,
		51,
		52,
		53,
		54,
		55,
		56,
		57,
		58,
		59,
		60,
		61,
		62,
		63,
		64,
		65,
		66,
		67,
		68,
		69,
		70,
		71,
		72,
		73,
		74,
		75,
		76,
		77,
		78,
		79,
		80,
		85,
		226,
		6,
		176,
		143,
		138,
		141,
		112,
		135,
		104,
		133,
		100,
		129,
		98,
		127,
		72,
		125,
		102,
		123,
		60,
		121,
		110,
		119,
		108,
		117,
		54,
		115,
		48,
		113,
		134,
		59,
		132,
		55,
		130,
		51,
		128,
		47,
		126,
		41,
		62,
		37,
		66,
		31,
		54,
		25,
		50,
		131,
		46,
		17,
		40,
		15,
		136,
		7,
		32,
		139,
		172,
		9,
		170,
		85,
		168,
		248,
		166,
		247,
		164,
		197,
		162,
		95,
		160,
		173,
		158,
		165,
		156,
		161,
		60,
		159,
		56,
		71,
		52,
		163,
		48,
		59,
		42,
		171,
		38,
		169,
		32,
		53,
		26,
		47,
		174,
		193,
		18,
		191,
		222,
		189,
		218,
		187,
		216,
		185,
		214,
		61,
		212,
		53,
		210,
		49,
		208,
		45,
		206,
		39,
		204,
		195,
		202,
		31,
		200,
		243,
		64,
		239,
		56,
		237,
		52,
		235,
		48,
		233,
		44,
		231,
		38,
		229,
		34,
		227,
		28,
		225,
		22,
		223,
		16,
		221,
		220,
		63,
		8,
		55,
		224,
		51,
		2,
		47,
		87,
		43,
		246,
		37,
		244,
		33,
		238,
		27,
		236,
		21,
		16,
		15,
		8,
		241,
		242,
		7,
		10,
		245,
		2,
		1,
		83,
		250,
		2,
		143,
		246
	];
	
	/**
	 * @var int[]
	 */
	protected $data = [];
	
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
		
		$this->rawStream = new StringStream($string, $endian, $buffer);
		
		for ($i = 0; $i < 256; $i++)
		{
			$this->ffzt[$i] = 0;
			
			for ($j = $i; $j & 0x80; $j <<= 1)
			{
				++$this->ffzt[$i];
			}
		}
		
		if ($this->rawStream->getEofPosition() >= 2)
		{
			$this->code = $this->rawStream->readInt(2);
		}
		else
		{
			if ($this->rawStream->getPosition() > 0)
			{
				$this->code = ($this->rawStream->readInt(1) << 8) | 0xFF;
			}
		}
		
		$this->fence = min($this->code, 0x7fff);
		$this->delay = 25;
		
		$this->preload();
		
		$this->mtf = range(0, 255, 1);
		$this->ctx = array_fill(0, 300, 0);
		
		do
		{
			$size = $this->decode();
		} while ($size);
		
		$this->write(implode($this->data));
		$this->setPosition(0);
	}
	
	/**
	 * Read bytes.
	 *
	 * @return bool
	 * @throws \Arhitector\Djvu\Exception
	 */
	public function preload()
	{
		while ($this->scount <= 24)
		{
			$byte = 0xFF;
			
			if ($this->rawStream->canReadBytes(1))
			{
				$byte = $this->rawStream->readInt(1);
			}
			else
			{
				if (--$this->delay < 1)
				{
					return false;
				}
			}
			
			$this->byte = ($this->byte << 8) | $byte;
			$this->scount += 8;
		}
		
		return true;
	}
	
	/**
	 *
	 *
	 * @param int $bits
	 *
	 * @return int
	 * @throws \Arhitector\Djvu\Exception
	 */
	protected function decode_raw($bits)
	{
		$n = 1;
		$m = 1 << $bits;
		
		while ($n < $m)
		{
			$b = $this->zpcodec_decoder();
			$n = ($n << 1) | $b;
		}
		
		return $n - $m;
	}
	
	/**
	 *
	 *
	 * @param int   $index
	 * @param int   $bits
	 *
	 * @return int
	 */
	public function decode_binary($index, $bits)
	{
		$n = 1;
		$m = (1 << $bits);
		
		while ($n < $m)
		{
			$b = $this->zpcodec_decode($index + $n);
			$n = ($n << 1) | $b;
		}
		
		return $n - $m;
	}
	
	/**
	 *
	 *
	 * @param int $mps
	 * @param int $z
	 *
	 * @return int
	 * @throws \Arhitector\Djvu\Exception
	 */
	protected function decode_sub_simple($mps, $z)
	{
		if ($z > $this->code) // Test MPS/LPS
		{
			$mps ^= 1;
			
			// LPS branch
			$z = 0x10000 - $z;
			$this->a += $z;
			$this->code += $z;
			
			// LPS renormalization
			$shift = $this->ffz($this->a);
			$this->scount -= $shift;
			$this->a = 0xffff & ($this->a << $shift);
			$this->code = 0xffff & (($this->code << $shift) | ($this->byte >> $this->scount) & ((1 << $shift) - 1));
		}
		else
		{
			// MPS renormalization
			--$this->scount;
			$this->a = 0xffff & ($z << 1);
			$this->code = 0xffff & (($this->code << 1) | (($this->byte >> $this->scount) & 1));
		}
		
		if ($this->scount < 16)
		{
			if ( ! $this->preload())
			{
				return -1;
			}
		}
		
		$this->fence = min($this->code, 0x7fff);
		
		return $mps;
	}
	
	/**
	 *
	 *
	 * @param int $x
	 *
	 * @return int
	 */
	protected function ffz($x)
	{
		return ($x >= 0xFF00) ? ($this->ffzt[$x & 0xFF] + 8) : $this->ffzt[($x >> 8) & 0xFF];
	}
	
	/**
	 *
	 *
	 * @return int
	 * @throws \Exception
	 */
	protected function decode()
	{
		if ( ! ($this->size = $this->decode_raw(24)))
		{
			return false;
		}
		
		if ($this->size > self::MAX_BLOCK * 1024)
		{
			throw new Exception('Too big block.');
		}
		
		if ($this->block_size < $this->size)
		{
			$this->block_size = $this->size;
		}
		
		// Decode Estimation Speed
		$fshift = 0;
		
		if ($this->zpcodec_decoder())
		{
			$fshift++;
			
			if ($this->zpcodec_decoder())
			{
				$fshift++;
			}
		}
		
		// Prepare Quasi MTF ** уже есть
		$freq = array_fill(0, self::FREQ_MAX, 0);
		$fadd = 4;
		$mtfno = 3;
		$markerpos = -1;
		
		for ($i = 0; $i < $this->size; $i++)
		{
			$ctxid = self::CTX_IDS - 1;
			
			if ($ctxid > $mtfno)
			{
				$ctxid = $mtfno;
			}
			
			if ($this->zpcodec_decode($ctxid))
			{
				$mtfno = 0;
				$this->data[$i] = $this->mtf[$mtfno];
				
				goto rotate;
			}
			
			$ctxid += self::CTX_IDS;
			
			if ($this->zpcodec_decode($ctxid))
			{
				$mtfno = 1;
				$this->data[$i] = $this->mtf[$mtfno];
				
				goto rotate;
			}
			
			$ctxid = 2 * self::CTX_IDS;
			
			for ($j = 1; $j < 8; $j++)
			{
				if ($this->zpcodec_decode($ctxid))
				{
					$mtfno = (1 << $j) + $this->decode_binary($ctxid, $j);
					$this->data[$i] = $this->mtf[$mtfno];
					
					goto rotate;
				}
				
				$ctxid += 1 << $j;
			}
			
			$mtfno = 256;
			$this->data[$i] = 0;
			$markerpos = $i;
			
			continue;
			
			// Rotate mtf according to empirical frequencies (new!)
			// Adjust frequencies for overflow
			rotate:
			
			$fadd += ($fadd >> $fshift);
			
			if ($fadd > 0x10000000)
			{
				$fadd >>= 24;
				$freq[0] >>= 24;
				$freq[1] >>= 24;
				$freq[2] >>= 24;
				$freq[3] >>= 24;
				
				for ($k = 4; $k < self::FREQ_MAX; $k++)
				{
					$freq[$k] >>= 24;
				}
			}
			
			// Relocate new char according to new freq
			$fc = $fadd;
			
			if ($mtfno < self::FREQ_MAX)
			{
				$fc += $freq[$mtfno];
			}
			
			for ($k = $mtfno; $k >= self::FREQ_MAX; $k--)
			{
				$this->mtf[$k] = $this->mtf[$k - 1];
			}
			
			for (; ($k > 0) && ((0xffffffff & $fc) >= (0xffffffff & $freq[$k - 1])); $k--)
			{
				$this->mtf[$k] = $this->mtf[$k - 1];
				$freq[$k] = $freq[$k - 1];
			}
			
			$this->mtf[$k] = $this->data[$i];
			$freq[$k] = $fc;
		}
		
		// Reconstruct the string
		if (($markerpos < 1) || ($markerpos >= $this->size))
		{
			throw new Exception('Byte stream is damaged.');
		}
		
		$pos = array_fill(0, $this->size, 0);
		$count = array_fill(0, 256, 0);
		
		for ($i = 0; $i < $markerpos; $i++)
		{
			$c = $this->data[$i];
			$pos[$i] = ($c << 24) | ($count[0xff & $c] & 0xffffff);
			$count[0xff & $c]++;
		}
		
		for ($i = $markerpos + 1; $i < $this->size; $i++)
		{
			$c = $this->data[$i];
			$pos[$i] = ($c << 24) | ($count[0xff & $c] & 0xffffff);
			$count[0xff & $c]++;
		}
		
		// Compute sorted char positions
		$last = 1;
		
		for ($i = 0; $i < 256; $i++)
		{
			$tmp = $count[$i];
			$count[$i] = $last;
			$last += $tmp;
		}
		
		// Undo the sort transform
		$j = 0;
		$last = $this->size - 1;
		
		$this->data[$last] = pack('C', $this->data[$last]);
		
		while ($last > 0)
		{
			$n = $pos[$j];
			$c = $pos[$j] >> 24;
			$this->data[--$last] = pack('C', 0xff & $c);
			$j = $count[0xff & $c] + ($n & 0xffffff);
		}
		
		if ($j != $markerpos)
		{
			throw new Exception('Byte stream is damaged.');
		}
		
		return $this->size;
	}
	
	/**
	 *
	 *
	 * @param int $index
	 * @param int $z
	 *
	 * @return int
	 */
	public function decode_sub($index, $z)
	{
		$bit = ($this->ctx[$index] & 1);
		$shift = null;
		
		// Avoid interval reversion
		$d = 0x6000 + (($z + $this->a) >> 2);
		
		if ($z > $d)
		{
			$z = $d;
		}
		
		if ($z > $this->code) // Test MPS/LPS
		{
			$bit ^= 1;
			
			// LPS branch
			$z = 0x10000 - $z;
			$this->a += $z;
			$this->code += $z;
			
			// LPS adaptation
			$this->ctx[$index] = $this->dn[$this->ctx[$index]];
			
			// LPS renormalization
			$shift = $this->ffz($this->a);
			$this->scount -= $shift;
			$this->a = 0xffff & ($this->a << $shift);
			$this->code = 0xffff & (($this->code << $shift) | ($this->byte >> $this->scount) & ((1 << $shift) - 1));
		}
		else
		{
			// MPS adaptation
			if ($this->a >= $this->m[$this->ctx[$index]])
			{
				$this->ctx[$index] = $this->up[$this->ctx[$index]];
			}
			
			// MPS renormalization
			--$this->scount;
			$this->a = 0xFFFF & ($z << 1);
			$this->code = 0xffff & (($this->code << 1) | (($this->byte >> $this->scount) & 1));
		}
		
		if ($this->scount < 16)
		{
			if ( ! $this->preload())
			{
				return -1;
			}
		}
		
		$this->fence = min($this->code, 0x7fff);
		
		return $bit;
	}
	
	/**
	 *
	 *
	 * @return int
	 * @throws \Arhitector\Djvu\Exception
	 */
	public function zpcodec_decoder()
	{
		return $this->decode_sub_simple(0, 0x8000 + ($this->a >> 1));
	}
	
	/**
	 *
	 *
	 * @param int $index
	 *
	 * @return int
	 */
	public function zpcodec_decode($index)
	{
		$z = $this->a + $this->p[$this->ctx[$index]];
		
		if ($z <= $this->fence)
		{
			$this->a = $z;
			
			return $this->ctx[$index] & 1;
		}
		
		return $this->decode_sub($index, $z);
	}
	
}
