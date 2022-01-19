<?php

namespace mdref\Generator;
use phpDocumentor\Reflection\DocBlock;


class SeeAlso extends Scrap {
	public function __toString() : string {
		return parent::toString(__FILE__, __COMPILER_HALT_OFFSET__);
	}
}

/** @var $doc DocBlock */
/** @var $patch callable as function(string, \Reflector) */

__HALT_COMPILER();
<?php
if (($sees = $doc?->getTagsByName("see"))) :
	?>See also <?php
	foreach ($sees as $i => $see) :
		/** @var $see DocBlock\Tags\See */
		if (($desc = $see->getDescription())) :
			?>[<?= $see->getDescription() ?>](<?= $see->getReference() ?>)<?php
		else :
			?><?= $see->getReference() ?><?php
		endif;
		if ($i < count($sees)) :
			if ($i === count($sees) - 1) :
				?>.<?php
			else :
				?>, <?php
			endif;
			if ($i === count($sees) - 2) :
				?>and <?php
			endif;
		endif;
	endforeach;
endif;

