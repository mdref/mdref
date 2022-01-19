<?php

namespace mdref\Generator;

use mdref\Generator;
use phpDocumentor\Reflection\{DocBlock, DocBlock\Tags};

class Arg extends Scrap {
	public function __toString() : string {
		return parent::toString(__FILE__, __COMPILER_HALT_OFFSET__, [
			"tag" => $this->getParamTag($this->ref->getName())
		]);
	}

}

/** @var $gen Generator */
/** @var $ref \ReflectionParameter */
/** @var $doc ?DocBlock */
/** @var $tag ?Tags\Param */

__HALT_COMPILER();
<?= $ref->hasType() ? $ref->getType() : str_replace("\\ref", "", $tag?->getType() ?? "mixed")
?> <?php
if ($ref->isVariadic()) : ?>
	?>...<?php
endif;
if ($ref->isPassedByReference()) :
	?>&<?php
endif;
?>$<?=$ref->getName()
?><?php
if ($ref->isDefaultValueAvailable()) :
	?> = <?php var_export($ref->getDefaultValue()) ?><?php
endif;
